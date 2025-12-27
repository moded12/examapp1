<?php // Admin Panel – groups removed from UI; exams can be created without selecting a group ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>لوحة الإدارة</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#0080ff;
  --sidebar:#0e152b;
  --panel:#0f172a;
  --border:#1f2937;
  --border-soft:#243146;
  --text:#e5e7eb;
  --muted:#9ca3af;
  --accent:#2563eb;
  --accent2:#a5b4fc;
  --success:#16a34a;
  --danger:#b91c1c;
  --radius:14px;
  --focus-ring:0 0 0 3px rgba(37,99,235,.35);
}
*{box-sizing:border-box;font-family:'Cairo',system-ui,Segoe UI,Roboto,'Noto Naskh Arabic',Tahoma,Arial,sans-serif}
html,body{margin:0;background:var(--bg);color:var(--text);min-height:100vh;-webkit-tap-highlight-color:transparent}
body.overlay-open{overflow:hidden}
a{color:#93c5fd;text-decoration:none}
a:hover{text-decoration:underline}
h1,h2,h3{margin:0;font-weight:600}
button{font-family:inherit}
.topbar{position:sticky;top:0;z-index:100;background:#0d1527ea;backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid var(--border)}
.topbar .title{font-size:18px;font-weight:700;letter-spacing:.5px;color:var(--accent2)}
.topbar .menu-toggle{background:#1f2937;border:1px solid var(--border-soft);width:44px;height:44px;display:none;align-items:center;justify-content:center;border-radius:12px;color:var(--text);cursor:pointer;font-size:20px}
.wrap{display:flex;min-height:calc(100vh - 60px)}
.sidebar{width:270px;background:var(--sidebar);border-left:1px solid var(--border);padding:18px 16px;display:flex;flex-direction:column;gap:10px;position:sticky;top:60px;height:calc(100vh - 60px);overflow-y:auto;transition:transform .25s}
.sidebar h2{margin:0 0 8px;font-size:16px;color:var(--accent2)}
.nav-btn{display:flex;align-items:center;gap:8px;width:100%;padding:11px 14px;font-size:14px;border:1px solid var(--border-soft);background:#1d2738;color:var(--text);border-radius:12px;cursor:pointer;text-align:right;transition:.2s}
.nav-btn:hover{background:#253448}
.nav-btn.active{background:#334155;border-color:#3f5167;box-shadow:inset 0 0 0 1px #48607e}
.nav-btn .step{background:#334155;color:#a5b4fc;font-size:11px;padding:2px 6px;border-radius:6px;min-width:24px;text-align:center}
.sidebar-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:90;display:none}
.sidebar.mobile{position:fixed;top:60px;right:0;height:calc(100vh - 60px);transform:translateX(100%);z-index:99;box-shadow:-4px 0 12px -3px rgba(0,0,0,.55)}
.sidebar.mobile.open{transform:translateX(0)}
.sidebar-overlay.show{display:block}
.content{flex:1;min-width:0;padding:18px 20px 120px}
.panel{background:var(--panel);border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px 18px;margin-bottom:18px;box-shadow:0 4px 14px -6px rgba(0,0,0,.5)}
.panel h3{margin:0 0 14px;font-size:17px;font-weight:600;color:#a7f3d0;display:flex;align-items:center;gap:8px}
.panel h3:before{content:"";width:6px;height:22px;border-radius:4px;background:linear-gradient(180deg,#2563eb,#1d4ed8)}
label{display:block;margin:6px 0 5px;font-size:13px;color:var(--muted);font-weight:500}
input,select,textarea{width:100%;padding:11px 12px;border-radius:11px;border:1px solid #2a3a52;background:#101d34;color:var(--text);font-size:14px;transition:.2s}
input:focus,select:focus,textarea:focus{outline:none;border-color:#3b82f6;box-shadow:var(--focus-ring)}
textarea{min-height:90px;resize:vertical}
.row{display:grid;gap:14px}
.row.cols-2{grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
.row.cols-3{grid-template-columns:repeat(auto-fit,minmax(200px,1fr))}
.row.cols-4{grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}
.muted{color:var(--muted);font-size:12.5px}
.btn{--btn-bg:#1f2937;--btn-border:#334155;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 16px;border-radius:11px;border:1px solid var(--btn-border);background:var(--btn-bg);color:var(--text);font-size:14px;cursor:pointer;font-weight:500;transition:.25s;text-decoration:none}
.btn:hover{background:#2c3b4f}
.btn.success{--btn-bg:var(--success);--btn-border:var(--success)}
.btn.success:hover{filter:brightness(1.05)}
.btn.danger{--btn-bg:#b91c1c;--btn-border:#b91c1c}
.btn.danger:hover{filter:brightness(1.06)}
.btn.small{padding:7px 12px;font-size:12.5px}
.btn.outline{background:transparent}
.seed-box{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-top:12px}
.seed-box .status{font-size:12.5px;color:#8fb4d6}
.sticky-actions{position:fixed;bottom:0;right:0;left:0;background:#0f172acc;backdrop-filter:blur(10px);padding:10px 14px;display:flex;gap:10px;justify-content:center;z-index:80;border-top:1px solid var(--border)}
@media(min-width:900px){.sticky-actions{position:static;background:transparent;backdrop-filter:none;padding:0;border:0;justify-content:flex-start;margin-top:14px}}
.list{border:1px solid var(--border);border-radius:14px;overflow:hidden;background:#101d34}
.list table{width:100%;border-collapse:collapse;font-size:13.5px}
.list th,.list td{padding:10px 12px;border-bottom:1px solid #18283c;vertical-align:top;text-align:right}
.list th{background:#18263a;color:#9fb3cc;font-weight:600;font-size:12.5px}
.list tbody tr:hover{background:#1b2c42}
dialog{border:none;border-radius:18px;padding:0;max-width:880px;width:96%;background:transparent}
dialog::backdrop{background:rgba(0,0,0,.6);backdrop-filter:blur(4px)}
dialog .dlg{background:#0f172a;border:1px solid var(--border);border-radius:18px;padding:20px 20px 26px;max-height:80vh;overflow-y:auto}
.drop-area{
  border:2px dashed #365173;
  background:#132338;
  padding:20px;
  border-radius:14px;
  text-align:center;
  font-size:13px;
  color:#8aa2ba;
  cursor:pointer;
  transition:.25s;
  position: relative; /* مهم: لتموضع input داخلياً */
}
.drop-area.dragover{background:#1c3552;border-color:#4a76a5;color:#b9d5f0}
.file-counter{margin-top:6px;font-size:12px;color:#8fb4d6}
.badge-inline{display:inline-flex;align-items:center;gap:4px;background:#1e2d42;color:#b5c8dd;padding:4px 8px;border-radius:8px;font-size:11px;margin:3px 4px 0 0;line-height:1.2}
.badge-inline a{color:#93c5fd}

/* اجعل input يغطي كامل مساحة الإسقاط مع شفافية كاملة للضغط المباشر */
.drop-area input[type=file]{
  position:absolute;
  inset:0;
  width:100%;
  height:100%;
  opacity:0;
  cursor:pointer;
}

@media(max-width:880px){
  .topbar .menu-toggle{display:flex}
  .sidebar.desktop{display:none}
  .sidebar.mobile{display:flex}
  .content{padding:16px 14px 130px}
  .row.cols-4,.row.cols-3,.row.cols-2{grid-template-columns:1fr 1fr}
}
@media(max-width:560px){
  .row.cols-4,.row.cols-3,.row.cols-2{grid-template-columns:1fr}
  .sticky-actions{padding:12px 10px}
  .btn{border-radius:10px}
}
.fade-in{animation:fade .4s both}
@keyframes fade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>

<header class="topbar">
  <button class="menu-toggle" id="menuToggle" aria-label="القائمة">☰</button>
  <div class="title">لوحة الإدارة</div>
  <div></div>
</header>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="wrap">
  <aside class="sidebar desktop">
    <h2>القائمة</h2>
    <button data-p="subjects" class="nav-btn"><span class="step">1</span> إضافة مادة</button>
    <button data-p="terms" class="nav-btn"><span class="step">2</span> إضافة فصل</button>
    <!-- تمت إزالة خطوة المجموعات -->
    <button data-p="exams" class="nav-btn"><span class="step">3</span> إضافة امتحان</button>
    <button data-p="browse" class="nav-btn"><span class="step">⊙</span> استعراض سريع</button>
  </aside>

  <aside class="sidebar mobile" id="mobileSidebar">
    <h2 style="display:flex;align-items:center;justify-content:space-between">
      القائمة
      <button class="btn small" type="button" onclick="closeMobileMenu()">✕</button>
    </h2>
    <button data-p="subjects" class="nav-btn"><span class="step">1</span> مادة</button>
    <button data-p="terms" class="nav-btn"><span class="step">2</span> فصل</button>
    <!-- تمت إزالة خطوة المجموعات -->
    <button data-p="exams" class="nav-btn"><span class="step">3</span> امتحان</button>
    <button data-p="browse" class="nav-btn"><span class="step">⊙</span> استعراض</button>
  </aside>

  <main class="content">

    <!-- 1) مادة + زر Seed -->
    <section id="p-subjects" class="panel fade-in">
      <h3>1) إضافة مادة</h3>
      <div class="row cols-3">
        <div><label>الصف</label><select id="s1_grade"></select></div>
        <div><label>اسم المادة</label><input id="s1_name" placeholder="مثال: التربية الإسلامية"></div>
        <div style="align-self:end">
          <button class="btn success" type="button" onclick="addSubjectToGrade()">حفظ</button>
        </div>
      </div>
      <div class="seed-box">
        <button id="btnSeedGrades" class="btn outline small" type="button" onclick="seedGrades()">تهيئة الصفوف (Seed)</button>
        <span id="seedStatus" class="status"></span>
      </div>
      <div class="muted" style="margin-top:8px">اختر الصف ثم اكتب اسم المادة واضغط حفظ. زر (تهيئة الصفوف) يظهر فقط إذا كان هناك نقص.</div>
    </section>

    <!-- 2) فصل -->
    <section id="p-terms" class="panel fade-in" style="display:none">
      <h3>2) إضافة فصل</h3>
      <div class="row cols-3">
        <div><label>الصف</label><select id="s2_grade" onchange="loadSubjectsFor('s2_grade','s2_subject').then(listTermsForGS)"></select></div>
        <div><label>المادة</label><select id="s2_subject" onchange="listTermsForGS()"></select></div>
        <div><label>الفصل</label><select id="s2_term" onchange="listTermsForGS()"><option>1</option><option>2</option></select></div>
      </div>
      <div style="margin-top:12px">
        <button class="btn success" type="button" onclick="addTermForGS()">حفظ الفصل</button>
      </div>
      <div id="s2_list" class="list" style="margin-top:16px"></div>
    </section>

    <!-- تمت إزالة 3) مجموعة بالكامل -->

    <!-- 3) امتحان -->
    <section id="p-exams" class="panel fade-in" style="display:none">
      <h3>3) إضافة امتحان</h3>
      <form id="examForm" onsubmit="return createExamItem(event)" enctype="multipart/form-data">
        <div class="row cols-4">
          <div><label>الصف</label><select id="s4_grade" name="grade_id" onchange="loadSubjectsFor('s4_grade','s4_subject')"></select></div>
          <div><label>المادة</label><select id="s4_subject" name="subject_id"></select></div>
          <div><label>الفصل</label><select id="s4_term" name="term_no"><option>1</option><option>2</option></select></div>
          <!-- تمت إزالة حقل المجموعة نهائياً -->
        </div>
        <div class="row cols-3" style="margin-top:14px">
          <div><label>عنوان الامتحان</label><input id="s4_title" name="title" required placeholder="مثال: اختبار نهاية الفصل"></div>
          <div><label>تاريخ الامتحان</label><input id="s4_date" name="exam_date" type="date"></div>
          <div>
            <label>المرفقات (صور أو PDF)</label>
            <div class="drop-area" id="dropArea">
              اسحب وأفلت أو اضغط لاختيار
              <input id="s4_files" name="files[]" type="file" accept=".pdf,image/*" multiple>
            </div>
            <div class="file-counter" id="fileCounter">لم يتم اختيار ملفات بعد.</div>
          </div>
        </div>
        <div style="margin-top:14px">
          <label>روابط مرفقات (اختياري)</label>
          <textarea id="s4_links" name="links" placeholder="ضع كل رابط في سطر"></textarea>
          <div style="margin-top:6px;display:flex;gap:10px;flex-wrap:wrap">
            <button class="btn small outline" type="button" onclick="splitLinksToBadges()">تحويل الروابط لشارات</button>
            <div id="linksBadges" style="flex:1;"></div>
          </div>
        </div>
        <div class="muted" style="margin-top:8px">
          المسموح: PDF / JPG / PNG / WEBP حتى 25MB لكل ملف + روابط خارجية.
        </div>
        <div class="sticky-actions">
          <button class="btn success" type="submit" style="min-width:160px;font-weight:600">حفظ الامتحان ✓</button>
          <button class="btn" type="reset" onclick="resetExamForm()">مسح</button>
        </div>
      </form>
    </section>

    <!-- استعراض -->
    <section id="p-browse" class="panel fade-in" style="display:none">
      <h3>استعراض سريع</h3>
      <div class="row cols-4">
        <div><label>الصف</label><select id="b_grade" onchange="loadSubjectsFor('b_grade','b_subject').then(reloadBrowse)"></select></div>
        <div><label>المادة</label><select id="b_subject" onchange="reloadBrowse()"></select></div>
        <div><label>الفصل</label><select id="b_term" onchange="reloadBrowse()"><option value="">الكل</option><option>1</option><option>2</option></select></div>
        <div><label>بحث بالعنوان</label><input id="b_q" oninput="reloadBrowse()" placeholder="مثال: رياضيات"></div>
      </div>
      <div id="b_list" class="list" style="margin-top:16px"></div>
    </section>

  </main>
</div>

<dialog id="dlg">
  <div class="dlg">
    <h3 id="dlg_title" style="margin-top:0">معاينة / تعديل الامتحان</h3>
    <div id="dlg_body" class="muted" style="margin:8px 0 14px"></div>
    <div id="dlg_files"></div>
    <div class="row cols-2" style="margin-top:18px">
      <div>
        <label>ملفات جديدة</label>
        <input id="edit_files" type="file" accept=".pdf,image/*" multiple style="margin-bottom:6px">
        <button class="btn small" type="button" onclick="uploadMoreFiles()">رفع الملفات</button>
      </div>
      <div>
        <label>روابط جديدة</label>
        <textarea id="edit_links" placeholder="https://example.com/file.pdf"></textarea>
        <button class="btn small" type="button" onclick="uploadMoreLinks()">حفظ الروابط</button>
      </div>
    </div>
    <hr>
    <form id="editForm" onsubmit="return saveEdit(event)">
      <input type="hidden" id="edit_id">
      <div class="row cols-2">
        <div><label>العنوان</label><input id="edit_title" required></div>
        <div><label>التاريخ</label><input id="edit_date" type="date"></div>
      </div>
      <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;justify-content:space-between">
        <div style="display:flex;gap:10px">
          <button class="btn success" type="submit">حفظ التعديل</button>
          <button class="btn" type="button" onclick="dlg.close()">إغلاق</button>
        </div>
        <button class="btn danger" type="button" onclick="confirmDelete()">حذف الامتحان</button>
      </div>
    </form>
  </div>
</dialog>

<script>
// اجعل مسار API متيناً (يعمل مع /admin/ و /public/admin/ ...)
const API = new URL('../admin.php', location.href).href;
function qs(id){ return document.getElementById(id); }
function html(el,s){ el.innerHTML=s; }
function opt(v,t){ const o=document.createElement('option'); o.value=v; o.textContent=t; return o; }
function icon(m){
  if(!m) return '📄';
  const mm=m.toLowerCase();
  if(mm==='link') return '🔗 رابط';
  if(mm.includes('pdf')) return '📕 PDF';
  if(/image|jpg|jpeg|png|webp/.test(mm)) return '🖼️ صورة';
  return '📄 ملف';
}
async function apiGet(a,params={}){
  const u=new URL(API,location.origin); u.searchParams.set('action',a);
  Object.entries(params).forEach(([k,v])=>{ if(v!==''&&v!=null) u.searchParams.set(k,v); });
  const r=await fetch(u); return r.json();
}
async function apiForm(a,fd){
  const u=new URL(API,location.origin); u.searchParams.set('action',a);
  const r=await fetch(u,{method:'POST',body:fd}); return r.json();
}

/* --- Mobile Menu --- */
function activatePanel(id){
  document.querySelectorAll('[data-p]').forEach(b=>b.classList.remove('active'));
  document.querySelectorAll('[data-p]').forEach(b=>{ if('p-'+b.dataset.p===id) b.classList.add('active'); });
  document.querySelectorAll('main .panel').forEach(p=>p.style.display='none');
  const sec=qs(id); if(sec){ sec.style.display=''; sec.scrollIntoView({behavior:'smooth'}); }
  if(window.innerWidth<880) closeMobileMenu();
}
document.querySelectorAll('.sidebar button[data-p]').forEach(b=>{
  b.addEventListener('click',()=>activatePanel('p-'+b.dataset.p));
});
const mobileSidebar=qs('mobileSidebar'), overlay=qs('sidebarOverlay');
function openMobileMenu(){ mobileSidebar.classList.add('open'); overlay.classList.add('show'); document.body.classList.add('overlay-open'); }
function closeMobileMenu(){ mobileSidebar.classList.remove('open'); overlay.classList.remove('show'); document.body.classList.remove('overlay-open'); }
qs('menuToggle').addEventListener('click',openMobileMenu);
overlay.addEventListener('click',closeMobileMenu);

/* --- Grade Ordering --- */
function gradeRank(g){
  if(g.grade_no) return +g.grade_no;
  const n=(g.name_ar||'').trim();
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
async function loadGradesInto(ids){
  const rows=(await apiGet('get_grades')).data||[];
  rows.sort((a,b)=> gradeRank(a)-gradeRank(b));
  ids.forEach(id=>{
    const sel=qs(id); if(!sel) return;
    const prev=sel.value;
    sel.innerHTML='';
    rows.forEach(r=> sel.append(opt(r.id,r.name_ar)));
    if(prev && [...sel.options].some(o=>o.value===prev)) sel.value=prev;
  });
  updateSeedVisibility(rows);
}

/* --- Seed Button Logic --- */
function updateSeedVisibility(rows){
  const btn=qs('btnSeedGrades'), status=qs('seedStatus');
  if(!btn) return;
  const have = rows.filter(r=> gradeRank(r) >=1 && gradeRank(r) <=12).length;
  if(have >= 12){
    btn.style.display='none';
    status.textContent='جميع الصفوف (1-12) موجودة.';
  } else {
    btn.style.display='inline-flex';
    status.textContent = `عدد الصفوف الحالية: ${have} – يمكنك الضغط لإكمال الناقص.`;
  }
}
async function seedGrades(){
  if(!confirm('تشغيل عملية تهيئة الصفوف 1..12؟ (لن تُكرر الموجود)')) return;
  const btn=qs('btnSeedGrades'); btn.disabled=true; btn.textContent='... جاري التهيئة';
  try{
    const res=await apiGet('seed_grades');
    alert(res.message || (res.ok?'تم':'خطأ'));
    await loadGradesInto(['s1_grade','s2_grade','s4_grade','b_grade']);
  }catch(e){
    alert('خطأ: '+e.message);
  }finally{
    btn.disabled=false;
    if(btn.textContent.startsWith('...')) btn.textContent='تهيئة الصفوف (Seed)';
  }
}

/* --- Subjects (1) --- */
async function loadSubjectsFor(gSel,sSel){
  const gid=+qs(gSel).value;
  const rows=(await apiGet('get_subjects_by_grade',{grade_id:gid})).data||[];
  const sel=qs(sSel); sel.innerHTML=''; rows.forEach(r=> sel.append(opt(r.id,r.name_ar)));
}
async function addSubjectToGrade(){
  const gradeId=qs('s1_grade').value;
  const name=qs('s1_name').value.trim();
  if(!name){ alert('اكتب اسم المادة'); return; }
  const fd=new FormData(); fd.append('grade_id',gradeId); fd.append('name_ar',name);
  const j=await apiForm('add_subject_to_grade',fd);
  alert(j.ok?'تم حفظ المادة':'خطأ: '+(j.error||''));
  if(j.ok){
    qs('s1_name').value='';
    await loadGradesInto(['s1_grade','s2_grade','s4_grade','b_grade']);
    ['s2','s4','b'].forEach(p=>{
      const gSel=qs(p+'_grade'), sSel=qs(p+'_subject');
      if(gSel && sSel && gSel.value===gradeId){
        loadSubjectsFor(p+'_grade',p+'_subject').then(()=>{
          if(p==='s2') listTermsForGS();
          if(p==='b') reloadBrowse();
        });
      }
    });
  }
}

/* --- Terms (2) --- */
async function addTermForGS(){
  const fd=new FormData();
  fd.append('grade_id',qs('s2_grade').value);
  fd.append('subject_id',qs('s2_subject').value);
  fd.append('term_no',qs('s2_term').value);
  const j=await apiForm('add_term_for_grade_subject',fd);
  alert(j.ok?'تم حفظ الفصل':(j.error||'خطأ'));
  if(j.ok) listTermsForGS();
}
async function listTermsForGS(){
  const gid=+qs('s2_grade').value, sid=+qs('s2_subject').value;
  const j=await apiGet('get_terms_for_grade_subject',{grade_id:gid,subject_id:sid});
  const rows=j.data||[];
  html(qs('s2_list'), rows.length
    ? `<table><thead><tr><th>الفصل</th></tr></thead><tbody>${rows.map(r=>`<tr><td>${r.term_no}</td></tr>`).join('')}</tbody></table>`
    : '<div class="muted" style="padding:12px">لا فصول.</div>');
}

/* --- Exams (3) --- */
function resetExamForm(){
  qs('s4_title').value='';
  qs('s4_links').value='';
  qs('linksBadges').innerHTML='';
  qs('s4_files').value='';
  qs('fileCounter').textContent='لم يتم اختيار ملفات بعد.';
  setTodayDate();
}
function splitLinksToBadges(){
  const lines=qs('s4_links').value.split(/\n+/).map(l=>l.trim()).filter(Boolean);
  const box=qs('linksBadges'); box.innerHTML='';
  if(!lines.length){ box.innerHTML='<span class="muted">لا روابط.</span>'; return; }
  lines.forEach(l=>{
    const span=document.createElement('span');
    span.className='badge-inline';
    span.innerHTML=`🔗 <a href="${l}" target="_blank" rel="noopener">${l.length>40?l.slice(0,37)+'...':l}</a>`;
    box.appendChild(span);
  });
}
async function createExamItem(e){
  e.preventDefault();
  const fd=new FormData(qs('examForm'));
  const j=await apiForm('create_exam_item',fd);
  alert(j.ok?'تم حفظ الامتحان':(j.error||'خطأ'));
  if(j.ok){ resetExamForm(); reloadBrowse(); }
}

/* Drag & drop */
const dropArea=qs('dropArea');
if(dropArea){
  const fileInput=qs('s4_files');
  function updateCounter(){ const c=fileInput.files.length; qs('fileCounter').textContent = c?`تم اختيار ${c} ملف(ات).`:'لم يتم اختيار ملفات بعد.'; }

  // لم نعد بحاجة إلى click برمجياً لأن input يغطي المنطقة
  fileInput.addEventListener('change',updateCounter);

  ['dragenter','dragover'].forEach(ev=>{
    dropArea.addEventListener(ev,e=>{e.preventDefault();dropArea.classList.add('dragover');});
  });
  ['dragleave','drop'].forEach(ev=>{
    dropArea.addEventListener(ev,e=>{
      e.preventDefault();
      if(ev==='drop'){
        const dt=e.dataTransfer;
        if(dt && dt.files){
          try{
            const dtNew=new DataTransfer();
            [...fileInput.files].forEach(f=>dtNew.items.add(f));
            [...dt.files].forEach(f=>dtNew.items.add(f));
            fileInput.files=dtNew.files;
          }catch{
            try{ fileInput.files = dt.files; }catch{}
          }
          updateCounter();
        }
      }
      dropArea.classList.remove('dragover');
    });
  });
}

/* --- Browse --- */
async function reloadBrowse(){
  const params={
    grade_id:qs('b_grade').value,
    subject_id:qs('b_subject').value,
    term_no:qs('b_term').value,
    q:qs('b_q').value.trim()
  };
  const u=new URL(API,location.origin); u.searchParams.set('action','list_exams');
  Object.entries(params).forEach(([k,v])=>{ if(v!==''&&v!=null) u.searchParams.set(k,v); });
  const r=await fetch(u); const j=await r.json(); const rows=j.data||[];
  html(qs('b_list'), rows.length
    ? `<table><thead><tr><th>الصف</th><th>المادة</th><th>الفصل</th><th>العنوان</th><th>مرفقات</th><th>إجراءات</th></tr></thead><tbody>${
        rows.map(x=>`<tr>
          <td>${x.grade_name||''}</td>
          <td>${x.subject_name||''}</td>
          <td>${x.term_no||''}</td>
          <td>${x.title||''}${x.exam_date?`<div class="muted">${x.exam_date}</div>`:''}</td>
          <td>${(x.files||[]).map(f=>`<div>${icon(f.mime)} — <a target="_blank" href="${f.path}">فتح</a></div>`).join('') || '-'}</td>
          <td style="white-space:nowrap">
            <button class="btn small" type="button" onclick="previewExam(${x.id})">تعديل</button>
            <button class="btn small danger" type="button" onclick="deleteExam(${x.id})">حذف</button>
          </td>
        </tr>`).join('')
      }</tbody></table>`
    : '<div class="muted" style="padding:14px">لا نتائج.</div>');
}

/* --- Edit Dialog --- */
async function previewExam(id){
  const j=await apiGet('get_exam_item',{id});
  if(!j.ok){ alert(j.error||'خطأ'); return; }
  qs('edit_id').value=j.item.id;
  qs('dlg_title').textContent='معاينة / تعديل الامتحان';
  qs('dlg_body').textContent='العنوان: '+j.item.title+(j.item.exam_date?(' • التاريخ: '+j.item.exam_date):'');
  html(qs('dlg_files'), (j.files||[]).length
    ? j.files.map(f=>`<div style="margin:6px 0">${icon(f.mime_type)} — <a target="_blank" href="${f.file_path}">فتح</a>
        <button class="btn small danger" type="button" onclick="deleteFile(${f.id})">✕</button></div>`).join('')
    : '<div class="muted">لا توجد مرفقات.</div>');
  qs('edit_title').value=j.item.title;
  qs('edit_date').value=j.item.exam_date||'';
  qs('edit_files').value='';
  qs('edit_links').value='';
  dlg.showModal();
}
async function saveEdit(e){
  e.preventDefault();
  const fd=new FormData();
  fd.append('id',qs('edit_id').value);
  fd.append('title',qs('edit_title').value.trim());
  fd.append('exam_date',qs('edit_date').value);
  const j=await apiForm('update_exam_item',fd);
  alert(j.ok?'تم التعديل':(j.error||'خطأ'));
  if(j.ok){ dlg.close(); reloadBrowse(); }
}
function confirmDelete(){ deleteExam(+qs('edit_id').value); }
async function deleteExam(id){
  if(!confirm('حذف الامتحان بكافة مرفقاته؟')) return;
  const fd=new FormData(); fd.append('id',id);
  const j=await apiForm('delete_exam_item',fd);
  alert(j.ok?'حُذف':(j.error||'خطأ'));
  if(j.ok){ dlg.close(); reloadBrowse(); }
}
async function deleteFile(id){
  if(!confirm('حذف هذا الملف؟')) return;
  const fd=new FormData(); fd.append('id',id);
  const j=await apiForm('delete_exam_file',fd);
  if(j.ok) previewExam(+qs('edit_id').value); else alert(j.error||'خطأ');
}
async function uploadMoreFiles(){
  const inp = qs('edit_files');
  if(!inp.files.length){
    inp.click(); // افتح نافذة اختيار الملفات إن لم تُحدد
    return;
  }
  const files=inp.files;
  const fd=new FormData();
  fd.append('exam_id',qs('edit_id').value);
  for(const f of files) fd.append('files[]',f,f.name);
  const j=await apiForm('add_exam_files',fd);
  alert(j.ok?'تم رفع الملفات':(j.error||'خطأ'));
  if(j.ok){ previewExam(+qs('edit_id').value); reloadBrowse(); }
}
async function uploadMoreLinks(){
  const t=qs('edit_links').value.trim();
  if(!t){ alert('أدخل رابطاً واحداً'); return; }
  const fd=new FormData();
  fd.append('exam_id',qs('edit_id').value);
  fd.append('links',t);
  const j=await apiForm('add_exam_links',fd);
  alert(j.ok?'تم حفظ الروابط':(j.error||'خطأ'));
  if(j.ok){ previewExam(+qs('edit_id').value); reloadBrowse(); }
}

/* --- Init --- */
function setTodayDate(){
  const el=qs('s4_date');
  const today=new Date().toISOString().slice(0,10);
  if(el && !el.value) el.value=today;
}
(async function init(){
  await loadGradesInto(['s1_grade','s2_grade','s4_grade','b_grade']);
  await loadSubjectsFor('s2_grade','s2_subject'); listTermsForGS();
  // تمت إزالة تحميل عناصر المجموعات
  await loadSubjectsFor('s4_grade','s4_subject');
  await loadSubjectsFor('b_grade','b_subject'); reloadBrowse();
  setTodayDate();
  if(window.innerWidth<880) activatePanel('p-exams'); else activatePanel('p-subjects');
})();
</script>
</body>
</html>