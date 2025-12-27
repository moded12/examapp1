<?php // Student Portal – inline iframe viewer + built-in gallery with swipe for multiple images ?>
<!doctype html>
<html lang="ar" dir="rtl" class="h-full">
<head>
<meta charset="utf-8">
<title>واجهة الطالب - بنك الامتحانات</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="color-scheme" content="light">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<!-- Google AdSense -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-xxxxxxxxxxx" crossorigin="anonymous"></script>
<script>
tailwind.config = {
  theme:{extend:{
    colors:{brand:{bg:'#7ecfdb',panel:'#ffffffd0',border:'#ffffff99',accent:'#1d4ed8'}},
    fontFamily:{cairo:['Cairo','system-ui','-apple-system','Segoe UI','Roboto','Noto Naskh Arabic','Tahoma','Arial','sans-serif']}
}}};
</script>
<style>
body{background:#7ecfdb;font-family:'Cairo',system-ui}
.result-card{
  position:relative;
  transition:background .25s,box-shadow .25s,border-color .25s;
  border-inline-start:6px solid transparent;
  background:#fffffff2;
}
.result-card:hover{background:#ffffff;box-shadow:0 4px 16px -6px rgba(0,0,0,.15)}
.result-card.has-file{cursor:default;}
.result-card.no-file{cursor:pointer;}
.open-direct{ position:relative; z-index:5; display:inline-flex; align-items:center; gap:6px; }
.badge{ background:#1d4ed81a; color:#1d4ed8; font-size:11px; padding:2px 8px; border-radius:6px; line-height:1.2; display:inline-flex; align-items:center; gap:4px; }
.file-icon{ font-size:14px; display:inline-flex; align-items:center; line-height:1; }
.no-file-badge{ background:#e2e8f0; color:#475569; font-size:11px; padding:4px 8px; border-radius:8px; font-weight:600; display:inline-flex; align-items:center; gap:4px; }
.attach-list a{ color:#1d4ed8; text-decoration:underline; }

/* العارض */
#viewer.backdrop{ position:fixed; inset:0; background:rgba(0,0,0,.6); display:none; z-index:50; }
#viewer.show{ display:block; }
#viewer .box{
  position:absolute; inset:auto 12px 12px 12px; top:64px;
  background:#0b1220; color:#fff; border-radius:14px; border:1px solid rgba(255,255,255,.08);
  box-shadow:0 12px 24px rgba(0,0,0,.35); overflow:hidden; display:flex; flex-direction:column;
}
@media(min-width:900px){ #viewer .box{ left:50%; right:auto; transform:translateX(-50%); width:min(1100px,96vw); } }
#viewer .head{ display:flex; align-items:center; justify-content:space-between; padding:10px 12px; gap:8px; background:#0d162b; border-bottom:1px solid rgba(255,255,255,.08); }
#viewer .head .btn{ display:inline-flex; align-items:center; gap:6px; font-weight:700; padding:8px 10px; border-radius:9px; font-size:13px; }
#viewer .head .btn.close{ background:#ef4444; color:#fff; }
#viewer .head .btn.action{ background:#1f2937; color:#e5e7eb; }

/* وضع iframe */
#viewer .frame-wrap{ position:relative; background:#0b1220; min-height:60vh; height:70vh; }
#viewer iframe{ position:absolute; inset:0; width:100%; height:100%; border:0; background:#0b1220; }
#viewer .fallback{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; }

/* وضع المعرض */
#viewer .gallery-wrap{ position:relative; background:#0b1220; min-height:60vh; height:70vh; display:none; }
#viewer .gallery-stage{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; overflow:hidden; touch-action:pan-y; }
#viewer .gallery-img{ max-width:100%; max-height:100%; display:none; }
#viewer .gallery-img.active{ display:block; }
#viewer .gallery-counter{ position:absolute; top:8px; left:12px; background:#111827; color:#e5e7eb; font-size:12px; padding:4px 8px; border-radius:8px; opacity:.9 }
#viewer .nav-btn{
  position:absolute; top:50%; transform:translateY(-50%);
  background:#111827cc; color:#e5e7eb; border:1px solid #1f2937; border-radius:12px;
  width:44px; height:44px; display:flex; align-items:center; justify-content:center;
}
#viewer .nav-btn.prev{ right:10px; } /* في واجهة RTL: السهم السابق على اليمين */
#viewer .nav-btn.next{ left:10px; }  /* والسهم التالي على اليسار */
#viewer .nav-btn:hover{ background:#111827; }
</style>
</head>
<body class="text-slate-800">

<header class="sticky top-0 z-30 backdrop-blur-md bg-white/70 border-b border-white/50">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <button id="goBackBtn" class="px-3 py-2 text-xs font-semibold rounded-md bg-slate-200 hover:bg-slate-300">⬅️ رجوع</button>
      <h1 class="text-xl md:text-2xl font-extrabold text-slate-800">واجهة الطالب - بنك الامتحانات</h1>
    </div>
    <button id="btnReloadTop" class="hidden md:inline-flex px-4 py-2 text-xs font-semibold rounded-md bg-blue-600 text-white hover:bg-blue-500">
      تحديث
    </button>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 pt-5 pb-10 w-full">

  <section class="rounded-2xl border border-white/70 bg-white/80 backdrop-blur-md shadow p-5 space-y-5">
    <div class="grid gap-4" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));">
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-600">الصف</label>
        <select id="f_grade" class="rounded-lg bg-white border border-white/70 text-sm"></select>
      </div>
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-600">المادة</label>
        <select id="f_subject" class="rounded-lg bg-white border border-white/70 text-sm">
          <option value="">اختر مادة</option>
        </select>
      </div>
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-600">الفصل</label>
        <select id="f_term" class="rounded-lg bg-white border border-white/70 text-sm">
          <option value="">اختر الفصل</option><option value="1">1</option><option value="2">2</option>
        </select>
      </div>
      <div class="flex flex-col gap-1 col-span-2 sm:col-span-1">
        <label class="text-xs font-semibold text-slate-600">بحث بالعنوان</label>
        <input id="f_q" placeholder="اكتب جزءاً" class="rounded-lg bg-white border border-white/70 text-sm px-3 py-2">
      </div>
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-600">ترتيب</label>
        <select id="f_sort" class="rounded-lg bg-white border border-white/70 text-sm">
          <option value="latest">الأحدث أولاً</option>
          <option value="oldest">الأقدم أولاً</option>
        </select>
      </div>
    </div>
    <div class="flex flex-wrap gap-3 items-center text-[14px]">
      <button id="btnReload" class="px-5 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-500 shadow">
        تحديث
      </button>
      <button id="btnClear" class="px-5 py-2 text-sm font-semibold rounded-lg bg-slate-600/70 hover:bg-slate-600/90 text-white">
        مسح البحث
      </button>
      <span id="statusLine" class="text-sm font-medium text-slate-700 flex items-center gap-2"></span>
    </div>
  </section>

  <!-- AdSense -->
  <section class="max-w-6xl mx-auto mt-5">
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="ca-pub-xxxxxxxxxxx"
         data-ad-slot="2136328739"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>(adsbygoogle=window.adsbygoogle||[]).push({});</script>
  </section>

  <div id="resultContainer" class="mt-6 space-y-4"></div>
  <div id="noData" class="hidden mt-6 p-6 text-center text-slate-600 text-sm rounded-xl border border-white/70 bg-white/90">
    لا توجد نتائج.
  </div>
  <div id="skeletonList" class="mt-6 space-y-4"></div>

</main>

<footer class="mt-10 py-8 text-center text-xs font-medium text-slate-700">
  © <?php echo date('Y'); ?> بنك الامتحانات.
</footer>

<!-- عارض داخلي: يدعم وضع iframe + وضع Gallery للصور -->
<div id="viewer" class="backdrop">
  <div class="box">
    <div class="head">
      <div class="flex items-center gap-2">
        <button id="viewerOpenNew" class="btn action" type="button">فتح 🔗</button>
        <button id="viewerDownload" class="btn action" type="button">تنزيل ⬇️</button>
        <span id="viewerTitle" class="text-sm text-slate-300 ms-2"></span>
      </div>
      <button id="viewerClose" class="btn close" type="button">إغلاق ✕</button>
    </div>

    <!-- وضع iframe -->
    <div id="frameWrap" class="frame-wrap">
      <iframe id="viewerFrame" src="about:blank" allow="fullscreen"></iframe>
      <div id="viewerFallback" class="fallback hidden">
        <a id="viewerNewTabLink" target="_blank" rel="noopener" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-500">فتح في تبويب جديد</a>
      </div>
    </div>

    <!-- وضع المعرض -->
    <div id="galleryWrap" class="gallery-wrap">
      <div class="gallery-counter" id="galleryCounter"></div>
      <button class="nav-btn prev" id="galPrev" type="button">⟸</button>
      <button class="nav-btn next" id="galNext" type="button">⟹</button>
      <div class="gallery-stage" id="galleryStage"></div>
    </div>
  </div>
</div>

<script>
/* HOME_URL + زر الرجوع */
const pathName = location.pathname;
const marker = '/student/';
const baseStudent = pathName.includes(marker) ? pathName.slice(0, pathName.indexOf(marker) + marker.length) : pathName.substring(0, pathName.lastIndexOf('/') + 1);
const HOME_URL = new URL(baseStudent + '../', location.origin).href;
document.getElementById('goBackBtn').addEventListener('click', ()=>{
  if (history.length > 1) history.back(); else location.href = HOME_URL;
});

/* مسار API */
let basePath = baseStudent;
const API_ADMIN = basePath + '../admin.php';

const PER_PAGE=10;
let defaultMode=true;

function $(id){return document.getElementById(id);}
function esc(s){return String(s||'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));}
function setStatus(msg){$('statusLine').textContent=msg;}
function showSkeleton(show){
  const s=$('skeletonList'); s.innerHTML='';
  if(!show) return;
  for(let i=0;i<5;i++) s.innerHTML+='<div class="h-20 rounded-xl bg-white/60 animate-pulse"></div>';
}
function clearResults(){ $('resultContainer').innerHTML=''; $('noData').classList.add('hidden'); }

async function legacyGet(action, params={}){
  const u=new URL(API_ADMIN,location.origin);
  u.searchParams.set('action',action);
  Object.entries(params).forEach(([k,v])=>{ if(v!==''&&v!=null) u.searchParams.set(k,v); });
  const r=await fetch(u,{cache:'no-store'});
  const text=await r.text();
  if(!r.ok) throw new Error('HTTP '+r.status);
  let json;try{json=JSON.parse(text);}catch{throw new Error('استجابة غير صالحة');}
  if(json.ok===false) throw new Error(json.error||'خطأ');
  return json;
}

/* ترتيب الصفوف */
function gradeRankName(n){
  if(!n) return 999;
  if(n.includes('الثاني عشر')) return 12;
  if(n.includes('الحادي عشر')) return 11;
  if(n.includes('العاشر')) return 10;
  if(n.includes('التاسع')) return 9;
  if(n.includes('الثامن')) return 8;
  if(n.includes('السابع')) return 7;
  if(n.includes('السادس')) return 6;
  if(n.includes('الخامس')) return 5;
  if(n.includes('الرابع')) return 4;
  if(n.includes('الثالث')) return 3;
  if(n.includes('الثاني')) return 2;
  if(n.includes('الأول')) return 1;
  return 999;
}
async function loadGrades(){
  try{
    const res=await legacyGet('get_grades');
    const sel=$('f_grade');
    sel.innerHTML='<option value="">اختر صف</option>';
    const rows=(res.data||[]);
    rows.sort((a,b)=> gradeRankName(a.name_ar)-gradeRankName(b.name_ar));
    rows.forEach(g=> sel.append(new Option(g.name_ar,g.id)));
  }catch{}
}
async function loadSubjects(){
  const gid=$('f_grade').value;
  const sel=$('f_subject'); sel.innerHTML='<option value="">اختر مادة</option>';
  if(!gid) return;
  try{
    const res=await legacyGet('get_subjects_by_grade',{grade_id:gid});
    (res.data||[]).forEach(s=> sel.append(new Option(s.name_ar,s.id)));
  }catch{}
}
function getFilters(){
  return {
    grade_id:$('f_grade').value,
    subject_id:$('f_subject').value,
    term_no:$('f_term').value,
    q:$('f_q').value.trim(),
    sort:$('f_sort')?$('f_sort').value:'latest'
  };
}

/* كشف أنواع الملفات */
function isExternalLink(url){ return /^https?:\/\//i.test(url||''); }
function isPdf(urlOrMime){
  const s=(urlOrMime||'').toLowerCase();
  return s.includes('pdf') || /\.pdf(\?|$)/i.test(s);
}
function isImage(urlOrMime){
  const s=(urlOrMime||'').toLowerCase();
  return s.includes('image') || /\.(png|jpg|jpeg|webp|gif|bmp|svg)(\?|$)/i.test(s);
}
function fileTypeIcon(file){
  if(!file) return '📄';
  const mime=(file.mime||'').toLowerCase(), p=(file.path||'').toLowerCase();
  if(mime==='link'||/^https?:\/\//i.test(p)) return '🔗';
  if(mime.includes('pdf')||p.endsWith('.pdf')) return '📕';
  if(mime.includes('image')||/\.(png|jpg|jpeg|webp|gif)$/i.test(p)) return '🖼️';
  return '📄';
}
function fileTypeLabel(file){
  if(!file) return 'ملف';
  const mime=(file.mime||'').toLowerCase(), p=(file.path||'').toLowerCase();
  if(mime==='link'||/^https?:\/\//i.test(p)) return 'رابط';
  if(mime.includes('pdf')||p.endsWith('.pdf')) return 'PDF';
  if(mime.includes('image')||/\.(png|jpg|jpeg|webp|gif)$/i.test(p)) return 'صورة';
  return 'ملف';
}

/* فتح داخل iframe (روابط/ PDF/ proxy) */
const viewer = $('viewer'), frameWrap=$('frameWrap'), galleryWrap=$('galleryWrap');
const viewerFrame = $('viewerFrame');
const viewerTitle = $('viewerTitle');
const viewerOpenNew = $('viewerOpenNew');
const viewerDownload = $('viewerDownload');
const viewerFallback = $('viewerFallback');
const viewerNewTabLink = $('viewerNewTabLink');

let viewerMode = 'iframe'; // 'iframe' | 'gallery'
function setViewerMode(mode){
  viewerMode = mode;
  if(mode==='iframe'){
    frameWrap.style.display='';
    galleryWrap.style.display='none';
  }else{
    frameWrap.style.display='none';
    galleryWrap.style.display='';
  }
}
function openViewerIframe(src, title=''){
  setViewerMode('iframe');
  viewerTitle.textContent = title||'';
  viewerOpenNew.onclick = ()=> window.open(src, '_blank', 'noopener');
  viewerDownload.onclick = ()=> window.open(src, '_blank');
  viewerNewTabLink.href = src;
  viewerFrame.src = src;
  viewer.classList.add('show');
  viewerFallback.classList.add('hidden');
}
function closeViewer(){
  viewerFrame.src = 'about:blank';
  viewer.classList.remove('show');
}
$('viewerClose').addEventListener('click', closeViewer);
viewer.addEventListener('click', (e)=>{ if(e.target===viewer) closeViewer(); });
document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeViewer(); });

/* Proxy للروابط المسموحة (مثلاً shneler) */
function allowedEmbedHost(u){
  try{
    const h=new URL(u).hostname.toLowerCase();
    return ['shneler.com','www.shneler.com'].includes(h);
  }catch{return false;}
}
function proxyUrl(u, mode='gallery', title=''){
  const base = new URL('../proxy.php', location.href);
  base.searchParams.set('url', u);
  base.searchParams.set('mode', mode);
  if (title) base.searchParams.set('title', title);
  return base.href;
}
function openInline(url, mime='', title=''){
  const external = isExternalLink(url);
  let src = url;
  if (external) {
    if (allowedEmbedHost(url)) {
      src = proxyUrl(url,'gallery', title||'عرض الصور');
    } else if (isPdf(url) || isImage(url) || (mime && (isPdf(mime)||isImage(mime)))) {
      src = url;
    } else {
      window.open(url, '_blank', 'noopener'); return;
    }
  }
  openViewerIframe(src, title);
}

/* وضع المعرض للصور المتعددة (سحب يمين/يسار + أزرار) */
const galleryStage=$('galleryStage'), galleryCounter=$('galleryCounter'), galPrev=$('galPrev'), galNext=$('galNext');
let galFiles=[], galIndex=0;

function openGallery(files, startIndex=0, title=''){
  if(!files||!files.length){ return; }
  // خذ فقط الصور
  const imgs = files.filter(f=> isImage(f.mime) || isImage(f.path));
  if(!imgs.length){ // لو لا توجد صور، عُد لـ iframe
    openInline(files[0]?.path||'', files[0]?.mime||'', title);
    return;
  }
  galFiles = imgs;
  galIndex = Math.min(Math.max(0, startIndex|0), imgs.length-1);

  // ابنِ عناصر الصور
  galleryStage.innerHTML='';
  imgs.forEach((f,i)=>{
    const img=document.createElement('img');
    img.className='gallery-img';
    img.alt='img'+i;
    img.loading='lazy';
    img.decoding='async';
    img.src = f.path; // روابط داخلية للرفع/ أو مطلقة
    galleryStage.appendChild(img);
  });

  // أزرار الرأس
  viewerTitle.textContent = title||'';
  const currentSrc = ()=> (galFiles[galIndex]?.path||'');
  viewerOpenNew.onclick = ()=> window.open(currentSrc(), '_blank', 'noopener');
  viewerDownload.onclick = ()=> window.open(currentSrc(), '_blank');

  setViewerMode('gallery');
  viewer.classList.add('show');
  renderGallery();
}

function renderGallery(){
  const nodes = [...galleryStage.querySelectorAll('.gallery-img')];
  nodes.forEach((el,i)=> el.classList.toggle('active', i===galIndex));
  galleryCounter.textContent = (galIndex+1)+' / '+galFiles.length;
  // تحضير المسبق (اختياري)
  const pre=[galIndex-1, galIndex+1].filter(i=>i>=0 && i<galFiles.length);
  pre.forEach(i=>{
    const n=nodes[i]; if(n && !n.complete){ const _=n.naturalWidth; }
  });
}
function galPrevFn(){ galIndex = (galIndex-1+galFiles.length)%galFiles.length; renderGallery(); }
function galNextFn(){ galIndex = (galIndex+1)%galFiles.length; renderGallery(); }
galPrev.addEventListener('click', galPrevFn);
galNext.addEventListener('click', galNextFn);
document.addEventListener('keydown', (e)=>{
  if(!viewer.classList.contains('show') || viewerMode!=='gallery') return;
  if(e.key==='ArrowLeft') galNextFn();   // في RTL: السهم الأيسر = التالي
  if(e.key==='ArrowRight') galPrevFn();  // السهم الأيمن = السابق
});

// دعم السحب باللمس يمين/يسار
let touchStartX=0, touchStartY=0, touchMoved=false;
galleryStage.addEventListener('touchstart', (e)=>{
  const t=e.touches[0]; touchStartX=t.clientX; touchStartY=t.clientY; touchMoved=false;
},{passive:true});
galleryStage.addEventListener('touchmove', (e)=>{
  const t=e.touches[0]; const dx=t.clientX - touchStartX, dy=t.clientY - touchStartY;
  if(Math.abs(dx)>10 || Math.abs(dy)>10) touchMoved=true;
},{passive:true});
galleryStage.addEventListener('touchend', (e)=>{
  if(!touchMoved) return;
  const t=e.changedTouches[0]; const dx=t.clientX - touchStartX;
  const TH=30; // عتبة
  if(dx <= -TH){ galNextFn(); }   // سحب لليسار => التالي
  else if(dx >= TH){ galPrevFn(); } // سحب لليمين => السابق
});

/* تحميل افتراضي/مفلتر ثم رسم البطاقات */
async function loadDefaultLatest(){
  showSkeleton(true); clearResults(); setStatus('جلب آخر 10...');
  try{
    const data=await legacyGet('list_exams',{});
    let rows=(data.data||[]).sort((a,b)=>{
      const ad=a.exam_date||'', bd=b.exam_date||'';
      return (bd>ad?1:bd<ad?-1:(b.id-a.id));
    }).slice(0,PER_PAGE);
    renderCards(rows);
    setStatus(rows.length? ('تم جلب آخر '+rows.length):'لا نتائج');
    if(!rows.length) $('noData').classList.remove('hidden');
  }catch(e){ setStatus('خطأ: '+e.message); }
  finally{ showSkeleton(false); }
}
async function loadFiltered(){
  showSkeleton(true); clearResults(); setStatus('تحميل...');
  const f=getFilters();
  try{
    let res=await legacyGet('list_exams',{
      grade_id:f.grade_id,
      subject_id:f.subject_id,
      term_no:f.term_no,
      q:f.q
    });
    let list=res.data||[];
    list.sort((a,b)=>{
      const ad=a.exam_date||'', bd=b.exam_date||'';
      return f.sort==='latest'
        ? (bd>ad?1:bd<ad?-1:(b.id-a.id))
        : (ad>bd?1:ad<bd?-1:(a.id-b.id));
    });
    renderCards(list);
    setStatus(list.length? ('عدد '+list.length):'لا نتائج');
    if(!list.length) $('noData').classList.remove('hidden');
  }catch(e){ setStatus('خطأ: '+e.message); }
  finally{ showSkeleton(false); }
}

/* Render */
function fileBox(ex){
  const files = ex.files||[];
  const firstFile = files[0];
  const firstPath = firstFile? firstFile.path : '';
  const icon = fileTypeIcon(firstFile);
  const label= fileTypeLabel(firstFile);
  const badges = `
      ${ex.grade_name?`<span class="badge">${esc(ex.grade_name)}</span>`:''}
      ${ex.subject_name?`<span class="badge">${esc(ex.subject_name)}</span>`:''}
      ${ex.term_no?`<span class="badge">فصل ${ex.term_no}</span>`:''}
      ${ex.exam_date?`<span class="badge">${esc(ex.exam_date)}</span>`:''}
      <span class="badge"><span class="file-icon">${icon}</span> ${files.length} ملف</span>
      ${firstFile?`<span class="badge">${label}</span>`:''}
  `;
  return {files, firstFile, firstPath, icon, label, badges};
}
function renderCards(list){
  const c=$('resultContainer'); c.innerHTML='';
  list.forEach(ex=>{
    const {files, firstFile, firstPath, icon, label, badges} = fileBox(ex);
    const card=document.createElement('div');
    card.className='result-card rounded-xl border border-white/70 bg-white/95 p-4 shadow ' + (firstFile?'has-file':'no-file');

    const hasImages = (files||[]).some(f=> isImage(f.mime)||isImage(f.path));
    const openBtn = document.createElement('button');
    openBtn.type='button';
    openBtn.className='open-direct px-5 py-3 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-500 focus:ring-2 focus:ring-blue-400 focus:outline-none';
    openBtn.innerHTML=`<span class="file-icon">${icon}</span> فتح`;

    const leftCol = document.createElement('div');
    leftCol.className='flex md:flex-col gap-2';
    if(firstFile){ leftCol.appendChild(openBtn); } else {
      const span=document.createElement('span');
      span.className='no-file-badge';
      span.innerHTML='<span class="file-icon">⛔</span> لا ملف';
      leftCol.appendChild(span);
    }

    const rightCol = document.createElement('div');
    rightCol.className='flex-1 min-w-0';
    rightCol.innerHTML=`
      <div class="text-lg font-semibold text-slate-800 break-words">${esc(ex.title)}</div>
      <div class="mt-2 flex flex-wrap gap-1.5">${badges}</div>
    `;

    // قائمة المرفقات التفصيلية (مع زر فتح مباشر لكل عنصر)
    if(files && files.length){
      const details=document.createElement('details');
      details.className='mt-3 attach-list';
      const summary=document.createElement('summary');
      summary.className='cursor-pointer text-sm text-slate-700';
      summary.textContent=`المرفقات (${files.length})`;
      const ul=document.createElement('ul');
      ul.className='mt-2 ps-4 space-y-1';
      files.forEach(f=>{
        const li=document.createElement('li');
        li.className='text-sm';
        const extIcon=fileTypeIcon(f);
        const extLabel=fileTypeLabel(f);
        li.innerHTML=`
          <span class="file-icon">${extIcon}</span>
          <span class="inline-block ms-1 px-2 py-0.5 text-[11px] rounded bg-slate-100 text-slate-700">${extLabel}</span>
          <button type="button" class="ms-2 underline text-blue-700 btn-open-one">فتح مباشر</button>
          <a class="ms-3" href="${f.path}" target="_blank" rel="noopener">فتح في تبويب</a>
          <span class="text-slate-500 ms-2 break-all">${esc(f.path)}</span>
        `;
        const btn=li.querySelector('.btn-open-one');
        btn.addEventListener('click',()=> openInline(f.path, f.mime||'', ex.title||''));
        ul.appendChild(li);
      });
      details.appendChild(summary); details.appendChild(ul);
      rightCol.appendChild(details);
    }

    const row=document.createElement('div');
    row.className='flex flex-col md:flex-row md:items-start gap-3 w-full relative';
    row.appendChild(leftCol);
    row.appendChild(rightCol);
    card.appendChild(row);

    // سلوك زر “فتح”: إن كان أكثر من صورة نفتح المعرض بالسحب، وإلا نفتح أول مرفق كالمعتاد
    if(firstFile){
      openBtn.addEventListener('click', ()=>{
        const images = (files||[]).filter(f=> isImage(f.mime)||isImage(f.path));
        if(images.length>1){
          openGallery(images, 0, ex.title||'');
        }else{
          openInline(firstPath, firstFile.mime||'', ex.title||'');
        }
      });
    }else{
      card.addEventListener('click',()=> alert('لا يوجد ملف مرفق لهذا الامتحان.'));
    }

    c.appendChild(card);
  });
}

/* أحداث الفلاتر */
['f_grade','f_subject','f_term','f_sort'].forEach(id=>{
  const el=$(id); if(!el) return;
  el.addEventListener('change',()=>{
    if(id==='f_grade'){ loadSubjects(); }
    if(defaultMode) defaultMode=false;
    loadFiltered();
  });
});
$('f_q').addEventListener('input',()=>{
  if(defaultMode) defaultMode=false;
  clearTimeout(window.__filterDebounce);
  window.__filterDebounce=setTimeout(loadFiltered,300);
});
$('btnReload').addEventListener('click',()=> defaultMode?loadDefaultLatest():loadFiltered());
$('btnReloadTop').addEventListener('click',()=> defaultMode?loadDefaultLatest():loadFiltered());
$('btnClear').addEventListener('click',()=>{
  defaultMode=true;
  ['f_grade','f_subject','f_term','f_q'].forEach(id=> { if($(id)) $(id).value=''; });
  if($('f_sort')) $('f_sort').value='latest';
  loadSubjects();
  loadDefaultLatest();
});

/* Init */
(async function init(){
  await loadGrades();
  await loadSubjects();
  await loadDefaultLatest();
})();
</script>
</body>
</html>