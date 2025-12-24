<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>العناوين - بنك الامتحانات</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box}
    html,body{margin:0;padding:0;font-family:'Tajawal',system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#0f172a;color:#e5e7eb}
    body{min-height:100vh;display:flex;flex-direction:column}
    a{text-decoration:none;color:inherit}
    button{font-family:inherit}
    .site-hd{background:#020617;color:#e5e7eb;padding:16px 0;box-shadow:0 1px 0 rgba(15,23,42,.6)}
    .container{max-width:1100px;margin:0 auto;padding:0 1.5rem}
    .site-title{font-weight:800;font-size:1.4rem;display:flex;align-items:center;gap:8px}
    .site-title span{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:999px;background:radial-gradient(circle at 30% 20%,#f97316,#b91c1c)}
    main{flex:1;padding:24px 0}
    .row-btn{width:100%;padding:16px 16px;background:#0b1120;color:#e5e7eb;border-radius:999px;border:1px solid rgba(148,163,184,.7);display:flex;align-items:center;justify-content:space-between;cursor:pointer;transition:.15s all ease;box-shadow:0 8px 26px rgba(15,23,42,.8)}
    .row-btn:hover{border-color:#60a5fa;box-shadow:0 12px 36px rgba(37,99,235,.60);transform:translateY(-1px)}
    .row-btn .title{font-weight:800;font-size:1.02rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-left:8px}
    .row-btn .meta{font-size:.8rem;opacity:.95}

    body.ov-open{overflow:hidden}
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.65);display:none;align-items:center;justify-content:center;z-index:1000}
    .ov.show{display:flex}
    .ov .box{background:#0b1220;color:#e5e7eb;width:min(1200px,96vw);height:min(92svh,940px);border-radius:18px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.40),0 0 0 1px rgba(255,255,255,.06) inset;outline:1px solid rgba(255,255,255,.06);}
    .ov header{padding:10px 14px;border-bottom:1px solid #1f2937;display:flex;align-items:center;justify-content:space-between;background:radial-gradient(circle at 0 0,rgba(96,165,250,.24),transparent 50%);}
    .ov header h3{margin:0;font-size:.98rem;font-weight:700;max-width:420px}
    .ov header .btn{background:#111827;color:#e5e7eb;border:1px solid #374151;border-radius:999px;padding:6px 11px;font-size:.78rem;cursor:pointer;display:inline-flex;align-items:center;gap:4px}
    .ov header .btn:hover{background:#1f2937;border-color:#4b5563}
    .ov header .btn.close{background:#b91c1c;border-color:#ef4444}
    .ov header .btn.close:hover{background:#dc2626}
    .ov .body{flex:1;display:flex;min-height:0;min-width:0}
    .ov .body.no-side .viewer{flex:1}
    .ov .viewer{position:relative;background:#0b1220;display:flex;align-items:center;justify-content:center;padding:0;min-width:0;min-height:0;}
    .ov .viewer iframe,.ov .viewer embed{width:100%;height:100%;border:0;background:white}
    .ov .viewer img{width:100%;height:100%;object-fit:contain;background:#0b1220}
    .ov .side{border-left:1px solid #1f2a3a;overflow:auto;background:#0b1220}
    .ov .side.hidden{display:none}
    .ov .file-item{padding:12px;border-bottom:1px solid #1f2a3a;cursor:pointer;display:flex;align-items:center;gap:8px}
    .ov .file-item:hover{background:#101a2e}
    .wm{position:absolute;inset:0;pointer-events:none;z-index:9;opacity:.22;background-repeat:repeat;background-size:300px 240px;}
    .loading{color:#9ca3af;font-size:.9rem}

    footer.site-ft{padding:14px 0 18px;border-top:1px solid rgba(148,163,184,.30);background:#020617;color:#9ca3af;font-size:.8rem;margin-top:20px}
    .flex{display:flex}
    .items-center{align-items:center}
    .gap-2{gap:.5rem}
    .gap-3{gap:.75rem}
    .min-w-0{min-width:0}
    .truncate{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .text-center{text-align:center}
    .text-sm{font-size:.875rem}
    .mt-2{margin-top:.5rem}
    .mt-8{margin-top:2rem}
    .mb-6{margin-bottom:1.5rem}
    .space-y-3 > * + *{margin-top:.75rem}
    .mt-1{margin-top:.25rem}
    .mb-3{margin-bottom:.75rem}
    .mb-4{margin-bottom:1rem}
    .text-slate-300{color:#e5e7eb}
    .text-slate-400{color:#9ca3af}
    .text-slate-600{color:#4b5563}
    .bg-blue-600{background:#2563eb}
    .text-white{color:#fff}
    .rounded{border-radius:.5rem}
    .px-4{padding-left:1rem;padding-right:1rem}
    .py-2{padding-top:.5rem;padding-bottom:.5rem}
    .mt-3{margin-top:.75rem}
  </style>
</head>
<body>
  <header class="site-hd">
    <div class="container mx-auto px-6 flex items-center justify-between">
      <div class="site-title">
        <span>OM</span>
        <div>امتحانات منهاج سلطنة عمان</div>
      </div>
      <a href="subjects.html" style="font-size:.85rem;color:#cbd5f5">المواد</a>
    </div>
  </header>

  <main class="container mx-auto px-6">
    <div class="text-center mb-6">
      <h2 class="text-3xl md:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
        العناوين
      </h2>
      <div id="hdr" class="mt-2 text-slate-700 dark:text-slate-300"></div>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">اضغط على أي زر لفتح المرفقات.</p>
    </div>

    <div class="ad-safe-gap"></div>

    <div id="list" class="space-y-3"></div>
    <div id="msg" class="mt-8 text-center text-slate-600 dark:text-slate-400"></div>
  </main>

  <footer class="site-ft">
    <div class="container mx-auto px-6 text-center text-sm">
      © <span id="yr"></span> جميع الحقوق محفوظة | امتحانات منهاج سلطنة عمان
    </div>
  </footer>

  <div id="ov" class="ov" aria-modal="true" role="dialog">
    <div class="box">
      <header>
        <div class="flex items-center gap-3 min-w-0">
          <button class="btn close" onclick="closeOv()">إغلاق</button>
          <h3 id="ov_title" class="truncate" title=""></h3>
        </div>
        <div class="flex items-center gap-2">
          <button id="openTab" class="btn" type="button" title="فتح في تبويب">↗️ فتح</button>
          <button id="downloadFile" class="btn" type="button" title="تنزيل">⬇️ تنزيل</button>
          <button id="toggleSide" class="btn" type="button" title="إظهار/إخفاء قائمة الملفات">📁 الملفات</button>
        </div>
      </header>
      <div id="ovBody" class="body no-side">
        <div id="viewer" class="viewer">
          <div id="wm" class="wm" aria-hidden="true"></div>
          لا يوجد ملف
        </div>
        <div id="filesSide" class="side hidden"></div>
      </div>
    </div>
  </div>

  <script>
  const pathName = location.pathname;
  const marker = '/student/';
  const baseStudent = pathName.includes(marker)
    ? pathName.slice(0, pathName.indexOf(marker) + marker.length)
    : pathName.replace(/[^/]*$/, '');
  const HOME_URL = new URL(baseStudent + '../', location.origin).href;

  document.getElementById('yr').textContent = new Date().getFullYear();

  (function initTheme(){
    const saved = localStorage.getItem('theme');
    if(saved === 'dark'){
      document.documentElement.classList.add('dark');
    }
  })();

  const urlParams = new URLSearchParams(location.search);
  const grade_id = +urlParams.get('grade_id') || null;
  const subject_id = +urlParams.get('subject_id') || null;
  const term_no = +urlParams.get('term_no') || null;

  const DEFAULT_WM = 'اكاديمية منهاجي التعليمية';

  function absUrl(p){
    try{
      if(!p) return '';
      return new URL(p, HOME_URL).href;
    }catch(_e){
      return p;
    }
  }

  function iconOf(mime,path){
    const lower = (mime || '').toLowerCase();
    const p = (path || '').toLowerCase();
    if(lower.includes('pdf') || /\.pdf(\?|$)/.test(p)) return '📄';
    if(lower.includes('image') || /\.(png|jpg|jpeg|webp|gif)(\?|$)/.test(p)) return '🖼️';
    if(/\.(doc|docx)(\?|$)/.test(p)) return '📘';
    if(/\.(ppt|pptx)(\?|$)/.test(p)) return '📊';
    if(/\.(xls|xlsx)(\?|$)/.test(p)) return '📗';
    if(/drive\.google\.com/.test(p)) return '🔗';
    if(/^https?:\/\//.test(p)) return '🌐';
    return '📎';
  }

  function isPDF(mime, path){
    const m = (mime || '').toLowerCase();
    const p = (path || '').toLowerCase();
    return m.includes('pdf') || /\.pdf(\?|$)/.test(p);
  }

  function applyWatermark(text){
    const wmEl = document.getElementById('wm');
    const t = (text || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="300" height="240" viewBox="0 0 300 240">
      <defs>
        <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" stop-color="#ffffff" stop-opacity="0.4"/>
          <stop offset="100%" stop-color="#9ca3af" stop-opacity="0.2"/>
        </linearGradient>
      </defs>
      <g transform="translate(150 120) rotate(-30)">
        <text text-anchor="middle" fill="url(#g)" font-size="24" font-family="Tajawal, system-ui" opacity="0.9">
          ${t}
        </text>
      </g>
    </svg>`;
    wmEl.style.backgroundImage = `url("data:image/svg+xml,${encodeURIComponent(svg)}")`;
  }

  async function apiGet(name, params = {}){
    const q = new URLSearchParams(params);
    const url = new URL('../exams.php', baseStudent);
    if (q.toString()) url.search = q.toString();
    const res = await fetch(url, {credentials:'omit', cache:'no-cache'});
    if(!res.ok) throw new Error('API failed');
    return res.json();
  }

  function openOv(title, files){
    window.__files = files || [];
    const ov = document.getElementById('ov');
    const bodyEl = document.getElementById('ovBody');
    const side = document.getElementById('filesSide');
    document.getElementById('ov_title').textContent = title || '';
    side.classList.add('hidden');
    bodyEl.classList.add('no-side');
    ov.classList.add('show');
    document.body.classList.add('ov-open');
    if (window.__files.length) {
      renderViewer(window.__files[0]);
      renderSide(window.__files);
    } else {
      document.getElementById('viewer').innerHTML = '<div class="text-slate-300">لا يوجد ملف</div>';
    }
    applyWatermark(DEFAULT_WM);
  }

  function closeOv(){
    if(window.currentBlobUrl){
      URL.revokeObjectURL(window.currentBlobUrl);
      window.currentBlobUrl = '';
    }
    window.currentFileUrl = '';
    document.getElementById('ov').classList.remove('show');
    document.body.classList.remove('ov-open');
  }

  window.addEventListener('keydown', (e)=>{ if(e.key==='Escape'){ closeOv(); } });

  document.getElementById('toggleSide').addEventListener('click', ()=>{
    const side = document.getElementById('filesSide');
    const bodyEl = document.getElementById('ovBody');
    const show = side.classList.contains('hidden');
    side.classList.toggle('hidden', !show);
    bodyEl.classList.toggle('no-side', !show);
  });

  // =========================
  // renderViewer (مُعدَّلة)
  // =========================
  async function renderViewer(file){
    const v = document.getElementById('viewer');
    if(!file){
      v.innerHTML = '<div class="text-slate-300">لا يوجد ملف</div>';
      return;
    }

    const mime = (file.mime || '').toLowerCase();
    const raw  = (file.path || file.file_path || '').trim();
    const path = absUrl(raw);
    window.currentFileUrl = path;

    if(window.currentBlobUrl){
      URL.revokeObjectURL(window.currentBlobUrl);
      window.currentBlobUrl = '';
    }

    const wmEl = document.getElementById('wm');

    // صور: نعرضها مباشرة بدون تعقيد
    const isImage = mime.includes('image') || /\.(png|jpg|jpeg|webp|gif)(\?|$)/i.test(path);
    if (isImage){
      v.innerHTML = '';
      const img = document.createElement('img');
      img.src = path;
      img.alt = '';
      v.appendChild(img);
      v.appendChild(wmEl);
      document.getElementById('openTab').disabled = false;
      document.getElementById('downloadFile').disabled = false;
      return;
    }

    // لأي نوع آخر (PDF, Docs, Google Drive, ...):
    // نحاول دائماً العرض داخل iframe باستخدام blob أو مباشرة
    v.innerHTML = '';
    const loading = document.createElement('div');
    loading.className = 'loading';
    loading.textContent = 'جارٍ تحميل الملف';
    v.appendChild(loading);

    let ok = false;
    let blobUrl = '';
    let finalUrl = path;

    try{
      let url = path;
      if (location.protocol === 'https:' && url.startsWith('http:')) {
        url = url.replace(/^http:/,'https:');
      }
      finalUrl = url;
      window.currentFileUrl = finalUrl;

      // محاولة fetch فقط للروابط التي تسمح عادة بذلك (نفس الدومين أو بدون CORS واضح)
      const sameOrigin = new URL(finalUrl, location.href).origin === location.origin;
      if (sameOrigin && !/drive\.google\.com/i.test(finalUrl)) {
        const res = await fetch(finalUrl, {credentials:'omit', cache:'no-cache'});
        if (res.ok){
          const ct  = (res.headers.get('content-type') || '').toLowerCase();
          const buf = await res.blob();
          const blob = new Blob([buf], {type: ct || 'application/octet-stream'});
          blobUrl = URL.createObjectURL(blob);
          window.currentBlobUrl = blobUrl;
          ok = true;
        }
      }
    }catch(_e){
      ok = false;
    }

    v.innerHTML = '';

    if (ok && blobUrl){
      // نجحنا في إنشاء blob: نعرضه داخل iframe (نفس الصفحة)
      const iframe = document.createElement('iframe');
      iframe.src = blobUrl + (isPDF(mime, finalUrl) ? '#toolbar=1&navpanes=0&view=FitH' : '');
      iframe.title = 'ملف';
      iframe.loading = 'eager';
      v.appendChild(iframe);
      v.appendChild(wmEl);
    } else {
      // لم نتمكن من العرض داخليًّا (غالباً رابط خارجي مثل Google Drive أو CORS)
      const isExternal = file.is_external || /^https?:\/\//i.test(finalUrl);

      v.innerHTML = `
        <div class="text-center text-slate-100 p-4">
          <p class="mb-3">تعذّر عرض الملف مباشرة داخل الصفحة.</p>
          <p class="mb-4 text-sm text-slate-300">
            ${isExternal ? 'يبدو أن الموقع الخارجي يمنع التضمين داخل الصفحات الأخرى (مثل Google Drive)، لذلك يجب فتحه في تبويب جديد.' : 'تحقّق من رابط الملف أو إعدادات الخادم.'}
          </p>
          <a class="px-4 py-2 rounded bg-blue-600 text-white"
             href="${finalUrl}" target="_blank" rel="noopener">
            فتح في تبويب جديد (إجباري)
          </a>
        </div>
      `;
      v.appendChild(wmEl);
    }

    // أزرار أعلى المودال
    const openBtn = document.getElementById('openTab');
    const dlBtn   = document.getElementById('downloadFile');
    openBtn.disabled = false;
    dlBtn.disabled   = false;
    openBtn.onclick  = ()=> window.open(window.currentFileUrl || finalUrl, '_blank', 'noopener');
    dlBtn.onclick    = ()=> window.open(window.currentFileUrl || finalUrl, '_blank');
  }

  function renderSide(files){
    const s = document.getElementById('filesSide');
    s.innerHTML = '';
    if(!files || !files.length){
      s.innerHTML = '<div class="p-4 text-slate-400">لا مرفقات</div>';
      return;
    }
    files.forEach((f)=>{
      const pth = f.path || f.file_path || '';
      const d   = document.createElement('div');
      d.className = 'file-item';
      d.innerHTML = `<span>${iconOf(f.mime,pth)}</span><span class="truncate">${(pth.split('/').pop()||'ملف')}</span>`;
      d.addEventListener('click',()=> renderViewer(f));
      s.appendChild(d);
    });
  }

  async function loadExams(){
    if(!grade_id || !subject_id || !term_no){
      document.getElementById('msg').textContent='معاملات ناقصة.';
      return;
    }

    (async ()=>{
      const grades = await apiGet('get_grades');
      const gRow   = (grades.data || []).find(x=>+x.id===grade_id);

      const subs = await apiGet('get_subjects',{grade_id});
      const sRow = (subs.data || []).find(x=>+x.id===subject_id);

      const terms = await apiGet('get_terms');
      const tRow = (terms.data || []).find(x=>+x.term_no===term_no);

      const hdr = document.getElementById('hdr');
      hdr.textContent = `الصف: ${gRow ? gRow.name_ar : grade_id} · المادة: ${sRow ? sRow.name_ar : subject_id} · الفصل: ${tRow ? tRow.name_ar : term_no}`;
    })().catch(()=>{});

    const apiRes = await apiGet('list_exams', {grade_id,subject_id,term_no});
    if(!apiRes.ok){
      document.getElementById('msg').textContent='تعذّر تحميل البيانات.';
      return;
    }
    const list = apiRes.data || [];
    const box  = document.getElementById('list');
    box.innerHTML = '';

    if(!list.length){
      document.getElementById('msg').textContent='لا توجد عناوين متاحة.';
      return;
    }

    list.forEach((ex)=>{
      const files = ex.files || [];
      const wrap = document.createElement('div');
      wrap.innerHTML = `
        <button class="row-btn" type="button">
          <span class="title">${ex.title}</span>
          <span class="meta">${files.length} ملف${ex.exam_date ? (' • ' + ex.exam_date) : ''}</span>
        </button>
      `;
      const btn = wrap.querySelector('button');
      btn.addEventListener('click',()=> openOv(ex.title, files));
      box.appendChild(wrap.firstElementChild);
    });
  }

  (async function init(){
    await loadExams();
  })();
  </script>
</body>
</html>