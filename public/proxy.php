<?php
declare(strict_types=1);

/* Proxy مع وضع "معرض صور" واستكشاف صور المجلد لروابط السلايدر (shneler.com)
   - قائمة بيضاء للأمان
   - إن لم نجد <img> في الـHTML، نجرب البحث عن 1..50.{jpg|jpeg|png|webp} داخل المجلد
   - نبني صفحة Gallery داخلية تُفتح في iframe
*/

$allowedHosts = ['shneler.com','www.shneler.com'];

function bad(int $code, string $msg){ http_response_code($code); header('Content-Type: text/plain; charset=utf-8'); echo $msg; exit; }
function ok_html(string $html){ header('Content-Type: text/html; charset=utf-8'); echo $html; exit; }
function ok_passthru(string $contentType, string $body){ header('Content-Type: '.$contentType); echo $body; exit; }

$url   = $_GET['url']  ?? '';
$mode  = $_GET['mode'] ?? 'gallery'; // gallery | raw
$title = $_GET['title'] ?? '';

if (!$url || !preg_match('~^https?://~i', $url)) bad(400,'Bad URL');
$parts = parse_url($url);
$host  = strtolower($parts['host'] ?? '');
if (!in_array($host, $allowedHosts, true)) bad(403,'Host not allowed');

/* cURL helpers */
function curl_fetch(string $u, bool $withHeader = true, int $timeout = 25){
  $ch=curl_init($u);
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_FOLLOWLOCATION=>true,
    CURLOPT_MAXREDIRS=>5,
    CURLOPT_CONNECTTIMEOUT=>6,
    CURLOPT_TIMEOUT=>$timeout,
    CURLOPT_ENCODING=>'',
    CURLOPT_USERAGENT=>'ExamAppProxy/1.2 (+https://example.com)',
    CURLOPT_HEADER=>$withHeader,
  ]);
  $resp=curl_exec($ch);
  if($resp===false){ $err=curl_error($ch); curl_close($ch); return ['ok'=>false,'err'=>$err]; }
  $info=curl_getinfo($ch);
  curl_close($ch);
  return ['ok'=>true,'body'=>$resp,'info'=>$info];
}
function curl_head_or_probe_image(string $u): array {
  // HEAD أولوية أولاً
  $ch=curl_init($u);
  curl_setopt_array($ch,[
    CURLOPT_NOBODY=>true,
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_FOLLOWLOCATION=>true,
    CURLOPT_MAXREDIRS=>3,
    CURLOPT_CONNECTTIMEOUT=>4,
    CURLOPT_TIMEOUT=>8,
    CURLOPT_USERAGENT=>'ExamAppProxy/1.2 (+https://example.com)',
    CURLOPT_HEADER=>true,
  ]);
  $resp=curl_exec($ch);
  if($resp!==false){
    $info=curl_getinfo($ch);
    $ct=$info['content_type'] ?? '';
    $code=(int)($info['http_code'] ?? 0);
    curl_close($ch);
    if($code>=200 && $code<400 && stripos($ct,'image/')===0){
      return ['ok'=>true,'content_type'=>$ct];
    }
  } else {
    curl_close($ch);
  }
  // إن فشل HEAD، جرب GET بنطاق صغير
  $ch=curl_init($u);
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_NOBODY=>false,
    CURLOPT_HTTPHEADER=>['Range: bytes=0-0'],
    CURLOPT_FOLLOWLOCATION=>true,
    CURLOPT_MAXREDIRS=>3,
    CURLOPT_CONNECTTIMEOUT=>4,
    CURLOPT_TIMEOUT=>8,
    CURLOPT_USERAGENT=>'ExamAppProxy/1.2 (+https://example.com)',
    CURLOPT_HEADER=>true,
  ]);
  $resp=curl_exec($ch);
  if($resp===false){ $err=curl_error($ch); curl_close($ch); return ['ok'=>false,'err'=>$err]; }
  $info=curl_getinfo($ch);
  $headerSize=$info['header_size'] ?? 0;
  $headersRaw=substr($resp,0,$headerSize);
  $code=(int)($info['http_code'] ?? 0);
  $ct=''; foreach(explode("\r\n",$headersRaw) as $h){ if(stripos($h,'Content-Type:')===0){ $ct=trim(substr($h,13)); break; } }
  curl_close($ch);
  if(($code===200||$code===206) && stripos($ct,'image/')===0) return ['ok'=>true,'content_type'=>$ct];
  return ['ok'=>false,'err'=>'not image or code '.$code];
}

function effective_base(string $eff): string {
  $p = parse_url($eff);
  $scheme = $p['scheme'] ?? 'https';
  $host   = $p['host'] ?? '';
  $port   = isset($p['port']) ? ':'.$p['port'] : '';
  $path   = $p['path'] ?? '/';
  $dir    = preg_replace('~[^/]+$~', '', $path);
  return "$scheme://$host$port$dir";
}
function absolutize(string $rel, string $base): string {
  if (preg_match('~^https?://~i', $rel)) return $rel;
  if (strpos($rel, '//') === 0) {
    $scheme = parse_url($base, PHP_URL_SCHEME) ?: 'https';
    return $scheme . ':' . $rel;
  }
  $bp = parse_url($base);
  $scheme = $bp['scheme'] ?? 'https';
  $host   = $bp['host'] ?? '';
  $port   = isset($bp['port']) ? ':'.$bp['port'] : '';
  $path   = $bp['path'] ?? '/';
  if (substr($rel, 0, 1) === '/') return "$scheme://$host$port$rel";
  $dir = preg_replace('~[^/]+$~','', $path);
  $combined = $dir . $rel;
  $segments=[];
  foreach (explode('/', $combined) as $seg){
    if ($seg===''||$seg==='.') continue;
    if ($seg==='..') array_pop($segments); else $segments[]=$seg;
  }
  return "$scheme://$host$port/".implode('/',$segments);
}
function strip_meta_policies(string $html): string {
  $html = preg_replace('~<meta[^>]+http-equiv=[\"\\\']?content-security-policy[\"\\\']?[^>]*>~i', '', $html);
  $html = preg_replace('~<meta[^>]+http-equiv=[\"\\\']?x-frame-options[\"\\\']?[^>]*>~i', '', $html);
  return $html;
}
function inject_base_once(string $html, string $baseHref): string {
  if (stripos($html, '<head') !== false && stripos($html, '<base ') === false) {
    return preg_replace('~(<head[^>]*>)~i', '$1<base href="'.htmlspecialchars($baseHref, ENT_QUOTES, 'UTF-8').'">', $html, 1);
  }
  return $html;
}

/* جلب المورد */
$res = curl_fetch($url, true, 25);
if (!$res['ok']) bad(502, 'Fetch failed: '.$res['err']);

$info = $res['info'] ?? [];
$headerSize = $info['header_size'] ?? 0;
$effective  = $info['url'] ?? $url;
$headersRaw = substr($res['body'], 0, $headerSize);
$body       = substr($res['body'], $headerSize);

/* Content-Type */
$contentType = 'text/html; charset=utf-8';
foreach (explode("\r\n", $headersRaw) as $h) {
  if (stripos($h, 'Content-Type:') === 0) {
    $contentType = trim(substr($h, 13));
    break;
  }
}

/* لو لم يكن HTML، مرّره كما هو */
$isHtml = stripos($contentType, 'text/html') === 0 || preg_match('~<!DOCTYPE|<html~i', $body);
if (!$isHtml) {
  ok_passthru($contentType, $body);
}

/* HTML */
$base = effective_base($effective);
$clean = strip_meta_policies($body);

if ($mode === 'gallery') {
  /* 1) حاول استخراج <img> أو روابط صور من HTML */
  $imgs=[];
  if (preg_match_all('~(?:src|href)\\s*=\\s*([\'\"])(.+?)\\1~i', $clean, $m)) {
    foreach ($m[2] as $u) {
      $u = trim($u);
      if (preg_match('~^(javascript:|mailto:)~i',$u)) continue;
      $abs = absolutize($u, $base);
      $path = parse_url($abs, PHP_URL_PATH) ?? '';
      if (preg_match('~\\.(?:png|jpe?g|webp|gif|bmp|svg)(?:\\?.*)?$~i', $path)) {
        $imgs[$abs]=true;
      }
    }
  }
  $images = array_keys($imgs);

  /* 2) إن لم نجد صوراً وبدت كـ “مجلد” (ينتهي بـ/) فجرّب استكشاف 1..50.* */
  $looksLikeFolder = (substr($url,-1)==='/');
  if (!$images && $looksLikeFolder) {
    $candidates=[];
    $exts=['jpg','jpeg','png','webp'];
    for($i=1;$i<=50;$i++){
      foreach($exts as $ext){
        // 1.jpg و 01.jpg
        $names = [$i.'.'.$ext, sprintf('%02d.%s',$i,$ext)];
        foreach($names as $name){
          $cand = $url.$name;
          $candidates[]=$cand;
        }
      }
    }
    // افحص بسرعة برؤوس فقط
    $found=[];
    foreach($candidates as $cand){
      $chk = curl_head_or_probe_image($cand);
      if($chk['ok']) $found[]=$cand;
      // قيد سريع لتجنب الضغط
      if(count($found)>=60) break;
    }
    if ($found) $images = $found;
  }

  if ($images) {
    $safeTitle = htmlspecialchars($title ?: 'عرض الصور', ENT_QUOTES, 'UTF-8');
    $list = '';
    foreach ($images as $i=>$u) {
      $uEsc = htmlspecialchars($u, ENT_QUOTES, 'UTF-8');
      $list .= "<img class=\"slide\" data-index=\"$i\" src=\"$uEsc\" alt=\"img$i\" loading=\"lazy\">";
    }
    $thumbs = '';
    foreach ($images as $i=>$u) {
      $uEsc = htmlspecialchars($u, ENT_QUOTES, 'UTF-8');
      $thumbs .= "<button class=\"th\" data-go=\"$i\"><img src=\"$uEsc\" alt=\"t$i\"></button>";
    }
    $html = <<<HTML
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>$safeTitle</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;background:#0b1220;color:#fff;font-family:system-ui,Segoe UI,Roboto}
.viewer{display:flex;flex-direction:column;height:100vh}
.toolbar{display:flex;gap:8px;align-items:center;justify-content:space-between;padding:8px 10px;background:#0d162b;border-bottom:1px solid #1f2a44}
.toolbar .btn{background:#1f2937;color:#e5e7eb;border:0;border-radius:8px;padding:8px 10px;cursor:pointer}
.stage{flex:1;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
.stage img.slide{max-width:100%;max-height:100%;display:none}
.stage img.slide.active{display:block}
.thumbs{display:flex;gap:6px;flex-wrap:wrap;padding:8px;background:#0d162b;border-top:1px solid #1f2a44;overflow:auto}
.thumbs .th{border:0;background:#1f2937;border-radius:6px;padding:2px;cursor:pointer}
.thumbs .th img{height:54px;display:block;border-radius:4px}
.counter{font-size:12px;color:#9ca3af;margin-inline-start:auto}
</style>
</head>
<body>
<div class="viewer">
  <div class="toolbar">
    <div style="display:flex;gap:8px">
      <button id="prev" class="btn">⟸ السابق</button>
      <button id="next" class="btn">التالي ⟹</button>
    </div>
    <div class="counter" id="counter"></div>
  </div>
  <div class="stage" id="stage">
    $list
  </div>
  <div class="thumbs" id="thumbs">
    $thumbs
  </div>
</div>
<script>
const slides=[...document.querySelectorAll('.slide')];
let idx=0;
function show(i){
  if(!slides.length) return;
  idx = (i+slides.length)%slides.length;
  slides.forEach((img,j)=> img.classList.toggle('active', j===idx));
  document.getElementById('counter').textContent = (idx+1)+' / '+slides.length;
}
document.getElementById('prev').onclick=()=>show(idx-1);
document.getElementById('next').onclick=()=>show(idx+1);
document.getElementById('thumbs').addEventListener('click',e=>{
  const b=e.target.closest('button.th'); if(!b) return;
  const n=+b.dataset.go; if(!Number.isNaN(n)) show(n);
});
document.addEventListener('keydown',e=>{
  if(e.key==='ArrowRight') show(idx+1);
  if(e.key==='ArrowLeft') show(idx-1);
});
show(0);
</script>
</body>
</html>
HTML;
    ok_html($html);
  }
  // وإلا نكمل بوضع raw المنظّف
}

/* تمرير HTML تنظيفي: إزالة meta المانعة وحقن base لتصحيح الروابط */
$clean = inject_base_once($clean, $base);
ok_html($clean);