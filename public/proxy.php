<?php
declare(strict_types=1);

/* Proxy بسيط للسماح بتضمين صفحات محددة من نطاقات موثوقة داخل العارض
   - قائمة بيضاء للمضيفين المسموح بهم
   - جلب عبر cURL مع مهلات ومعالجة نوع المحتوى
   - لصفحات HTML: حقن <base href="..."> لضبط الروابط النسبية
   - لا يمرر ترويسات X-Frame-Options/CSP من المصدر
   تحذير: استخدمه فقط لما تسمح به حقوقك وشروط الموقع البعيد.
*/

$allowedHosts = ['shneler.com','www.shneler.com'];

$url = $_GET['url'] ?? '';
if (!$url || !preg_match('~^https?://~i', $url)) {
  http_response_code(400); echo 'Bad URL'; exit;
}
$parts = parse_url($url);
$host = strtolower($parts['host'] ?? '');
if (!in_array($host, $allowedHosts, true)) {
  http_response_code(403); echo 'Host not allowed'; exit;
}

$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_MAXREDIRS => 5,
  CURLOPT_CONNECTTIMEOUT => 5,
  CURLOPT_TIMEOUT => 20,
  CURLOPT_ENCODING => '',         // دعم gzip/deflate
  CURLOPT_USERAGENT => 'ExamAppProxy/1.0 (+https://example.com)',
  CURLOPT_HEADER => true,
]);

$resp = curl_exec($ch);
if ($resp === false) {
  http_response_code(502);
  echo 'Fetch failed: '.curl_error($ch);
  exit;
}
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headersRaw = substr($resp, 0, $headerSize);
$body = substr($resp, $headerSize);
curl_close($ch);

/* استخراج نوع المحتوى */
$contentType = 'text/html; charset=utf-8';
foreach (explode("\r\n", $headersRaw) as $h) {
  if (stripos($h, 'Content-Type:') === 0) {
    $contentType = trim(substr($h, 13));
    break;
  }
}

/* السماح بأنواع محدودة */
$ok = false;
$allowList = ['text/html','application/pdf','text/plain','text/css','application/javascript','text/ecmascript'];
foreach ($allowList as $t) {
  if (stripos($contentType, $t) === 0) { $ok = true; break; }
}
if (!$ok && stripos($contentType, 'image/') === 0) $ok = true;
if (!$ok) $contentType = 'text/html; charset=utf-8';

header('Content-Type: '.$contentType);
// لا نعيد أي ترويسات أمنية تمنع التضمين

/* لصفحات HTML: حقن <base> ليسهل تحميل الموارد النسبية */
if (stripos($contentType, 'text/html') === 0) {
  $scheme = $parts['scheme'] ?? 'https';
  $port   = isset($parts['port']) ? ':'.$parts['port'] : '';
  $path   = $parts['path'] ?? '/';
  $dir    = preg_replace('~[^/]+$~', '', $path); // مجلد الصفحة
  $base   = $scheme.'://'.$host.$port.$dir;

  if (stripos($body, '<head') !== false && stripos($body, '<base ') === false) {
    $body = preg_replace('~(<head[^>]*>)~i', '$1<base href="'.htmlspecialchars($base, ENT_QUOTES, 'UTF-8').'">', $body, 1);
  }
  echo $body;
} else {
  echo $body;
}