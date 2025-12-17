<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../config/db.php';

function ok($data = []) { echo json_encode(['ok'=>true] + $data, JSON_UNESCAPED_UNICODE); exit; }
function bad($msg, $code=400) { http_response_code($code); echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE); exit; }

$action = $_GET['action'] ?? '';
const YEAR_ALL = 'ALL';
define('MAX_UPLOAD_BYTES', 25 * 1024 * 1024);

/* ضبط جذور المشروع/الويب ومسارات الرفع:
   - APP_ROOT: جذر المشروع (examapp)
   - PUBLIC_ROOT: مجلد public
   - WEB_BASE: البادئة على الويب للمشروع (مثال: /oman/examapp)
   - UPLOAD_ROOT: مجلد حفظ الملفات داخل المشروع (examapp/uploads/exams) */
define('APP_ROOT', str_replace('\\','/', dirname(__DIR__)));        // /httpdocs/oman/examapp
define('PUBLIC_ROOT', str_replace('\\','/', __DIR__));               // /httpdocs/oman/examapp/public
$__script = $_SERVER['SCRIPT_NAME'] ?? '';
$__web_base = rtrim(str_replace('\\','/', dirname(dirname($__script))), '/'); // /oman/examapp
define('WEB_BASE', $__web_base);                                     // قد تكون '' إذا على الجذر
define('UPLOAD_ROOT', APP_ROOT . '/uploads/exams');                  // /httpdocs/oman/examapp/uploads/exams

function ensure_upload_dir(string $dir): void {
  if (!is_dir($dir) && !@mkdir($dir, 0775, true)) bad('Upload dir not creatable: '.$dir, 500);
  if (!is_writable($dir)) bad('Upload dir not writable: '.$dir, 500);
}
/* يحوّل مسار ملف مطلق إلى مسار ويب يبدأ بـ WEB_BASE (مثال: /oman/examapp/uploads/...) */
function rel_from_abs(string $abs): string {
  $abs = str_replace('\\','/',$abs);
  $app = rtrim(APP_ROOT,'/');
  if (strpos($abs, $app) === 0) {
    $suffix = substr($abs, strlen($app)); // /uploads/...
    return (WEB_BASE?:'') . $suffix;      // /oman/examapp/uploads/...
  }
  // احتياط: لو كان داخل DOCUMENT_ROOT الخام
  $doc = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
  if ($doc && strpos($abs, $doc) === 0) {
    return substr($abs, strlen($doc));    // /uploads/...
  }
  return '/'.ltrim(basename($abs),'/');
}
/* هل المسار يشير لمجلد الرفع المسموح؟ يدعم النمطين:
   - /oman/examapp/uploads/...
   - /uploads/... (قديم) */
function is_local_upload_path(string $path): bool {
  $path = str_replace('\\','/',$path);
  $p1 = rtrim(WEB_BASE,'/') . '/uploads/';
  if ($p1 === '/uploads/' || $p1 === 'uploads/') $p1 = '/uploads/';
  return (WEB_BASE && strpos($path, $p1) === 0) || (strpos($path, '/uploads/') === 0);
}
/* يحوّل المسار النسبي/الويب إلى مسار مطلق على السيرفر للحذف الفيزيائي */
function abs_from_rel(string $rel): string {
  $rel = str_replace('\\','/',$rel);
  $p1 = rtrim(WEB_BASE,'/') . '/uploads/';
  if ($p1 === '/uploads/' || $p1 === 'uploads/') $p1 = '/uploads/';
  // جديد: /oman/examapp/uploads/...
  if (WEB_BASE && strpos($rel, rtrim(WEB_BASE,'/').'/uploads/') === 0) {
    return APP_ROOT . substr($rel, strlen(WEB_BASE));
  }
  // قديم: /uploads/...
  $doc = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
  if ($doc && strpos($rel, '/uploads/') === 0) {
    return $doc . $rel;
  }
  // fallback: إن أرسل مسار نسبي غريب
  return APP_ROOT . '/uploads/' . ltrim($rel,'/');
}
function parse_links_text(?string $text): array {
  $out = [];
  foreach (preg_split('/\r\n|\r|\n/', (string)$text) as $line) {
    $u = trim($line);
    if ($u === '') continue;
    if (!preg_match('~^https?://~i', $u)) continue;
    if (filter_var($u, FILTER_VALIDATE_URL)) $out[] = $u;
  }
  return array_values(array_unique($out));
}

/* ======================= GET ======================= */

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($action === 'ping') ok(['msg'=>'pong']);

  if ($action === 'get_grades') {
    $rows = $pdo->query("SELECT id,name_ar,sort_order FROM grades WHERE is_active=1 ORDER BY sort_order,name_ar")->fetchAll();
    ok(['data'=>$rows]);
  }

  if ($action === 'get_subjects_by_grade') {
    $grade_id = (int)($_GET['grade_id'] ?? 0);
    if (!$grade_id) bad('grade_id required');
    $st = $pdo->prepare("SELECT s.id,s.name_ar
                         FROM grade_subjects gs JOIN subjects s ON s.id=gs.subject_id
                         WHERE gs.grade_id=? AND s.is_active=1
                         ORDER BY s.name_ar");
    $st->execute([$grade_id]);
    ok(['data'=>$st->fetchAll()]);
  }

  if ($action === 'get_terms_for_grade_subject') {
    $grade_id = (int)($_GET['grade_id'] ?? 0);
    $subject_id = (int)($_GET['subject_id'] ?? 0);
    if (!$grade_id || !$subject_id) bad('grade_id & subject_id required');
    $st = $pdo->prepare("SELECT gst.id AS gst_id, t.id AS term_id, t.term_no
                         FROM grade_subjects gs
                         JOIN grade_subject_terms gst ON gst.grade_subject_id=gs.id
                         JOIN terms t ON t.id=gst.term_id
                         WHERE gs.grade_id=? AND gs.subject_id=?
                         ORDER BY t.term_no");
    $st->execute([$grade_id,$subject_id]);
    ok(['data'=>$st->fetchAll()]);
  }

  if ($action === 'get_groups_for_gst') {
    $gst_id = (int)($_GET['gst_id'] ?? 0);
    if (!$gst_id) bad('gst_id required');
    $st = $pdo->prepare("SELECT id,group_no,COALESCE(title, CONCAT('مجموعة ',group_no)) AS title
                         FROM exam_groups WHERE gst_id=? ORDER BY group_no");
    $st->execute([$gst_id]);
    ok(['data'=>$st->fetchAll()]);
  }

  if ($action === 'get_exam_item') {
    $id = (int)($_GET['id'] ?? 0); if(!$id) bad('id required');
    $item = $pdo->prepare("SELECT id,title,exam_date FROM exam_items WHERE id=?");
    $item->execute([$id]); $it=$item->fetch(); if(!$it) bad('not found',404);
    $files=$pdo->prepare("SELECT id,file_path,mime_type,file_size FROM exam_files WHERE exam_id=? ORDER BY id");
    $files->execute([$id]);
    ok(['item'=>$it,'files'=>$files->fetchAll()]);
  }

  if ($action === 'list_exams') {
    $grade_id   = isset($_GET['grade_id']) ? (int)$_GET['grade_id'] : null;
    $subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : null;
    $term_no    = isset($_GET['term_no']) ? (int)$_GET['term_no'] : null;
    $q          = isset($_GET['q']) ? trim((string)$_GET['q']) : null;

    $sql = "SELECT ei.id,ei.title,ei.exam_date,
                   eg.group_no,
                   g.name_ar AS grade_name, s.name_ar AS subject_name,
                   t.term_no
            FROM exam_items ei
            JOIN exam_groups eg ON eg.id=ei.group_id
            JOIN grade_subject_terms gst ON gst.id=eg.gst_id
            JOIN grade_subjects gs ON gs.id=gst.grade_subject_id
            JOIN grades g ON g.id=gs.grade_id
            JOIN subjects s ON s.id=gs.subject_id
            JOIN terms t ON t.id=gst.term_id
            WHERE 1=1";
    $par=[];
    if (!is_null($grade_id))   { $sql.=" AND gs.grade_id=?";   $par[]=$grade_id; }
    if (!is_null($subject_id)) { $sql.=" AND gs.subject_id=?"; $par[]=$subject_id; }
    if (!is_null($term_no))    { $sql.=" AND t.term_no=?";     $par[]=$term_no; }
    if (!is_null($q) && $q!==''){ $sql.=" AND ei.title LIKE ?"; $par[]='%'.$q.'%'; }
    $sql.=" ORDER BY g.sort_order,s.name_ar,t.term_no,eg.group_no,ei.id DESC";

    $stmt=$pdo->prepare($sql); $stmt->execute($par);
    $rows=$stmt->fetchAll();

    // الملفات
    $ids = array_column($rows,'id'); $filesMap=[];
    if ($ids) {
      $in = implode(',',array_fill(0,count($ids),'?'));
      $st = $pdo->prepare("SELECT exam_id,file_path,mime_type FROM exam_files WHERE exam_id IN ($in) ORDER BY id");
      $st->execute($ids);
      foreach ($st->fetchAll() as $f) $filesMap[$f['exam_id']][] = ['path'=>$f['file_path'],'mime'=>$f['mime_type']];
    }
    foreach ($rows as &$r) $r['files'] = $filesMap[$r['id']] ?? [];

    ok(['data'=>$rows]);
  }

  bad('no action',404);
}

/* ======================= POST ======================= */
/* 1) مادة */
if ($action === 'add_subject_to_grade') {
  $grade_id = (int)($_POST['grade_id'] ?? 0);
  $name = trim((string)($_POST['name_ar'] ?? ''));
  if (!$grade_id || $name==='') bad('grade_id & name_ar required');

  try {
    $pdo->beginTransaction();
    $ins = $pdo->prepare("INSERT INTO subjects (name_ar) VALUES (?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
    $ins->execute([$name]);
    $subject_id = (int)$pdo->lastInsertId();
    $pdo->prepare("INSERT IGNORE INTO grade_subjects (grade_id,subject_id) VALUES (?,?)")->execute([$grade_id,$subject_id]);
    $pdo->commit();
    ok(['subject_id'=>$subject_id]);
  } catch (Throwable $e) { $pdo->rollBack(); bad('DB Error: '.$e->getMessage(),500); }
}

/* 2) فصل */
if ($action === 'add_term_for_grade_subject') {
  $grade_id   = (int)($_POST['grade_id'] ?? 0);
  $subject_id = (int)($_POST['subject_id'] ?? 0);
  $term_no    = (int)($_POST['term_no'] ?? 0);
  if (!$grade_id || !$subject_id || !in_array($term_no,[1,2],true)) bad('grade_id, subject_id, term_no(1|2) required');

  try {
    $pdo->beginTransaction();
    $st=$pdo->prepare("SELECT id FROM grade_subjects WHERE grade_id=? AND subject_id=?");
    $st->execute([$grade_id,$subject_id]);
    $gs_id = (int)($st->fetchColumn() ?: 0);
    if(!$gs_id){
      $pdo->prepare("INSERT INTO grade_subjects (grade_id,subject_id) VALUES (?,?)")->execute([$grade_id,$subject_id]);
      $gs_id=(int)$pdo->lastInsertId();
    }
    $pdo->prepare("INSERT INTO terms (term_no,school_year) VALUES (?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)")
        ->execute([$term_no, YEAR_ALL]);
    $term_id = (int)$pdo->lastInsertId();
    $pdo->prepare("INSERT IGNORE INTO grade_subject_terms (grade_subject_id,term_id) VALUES (?,?)")->execute([$gs_id,$term_id]);
    $pdo->commit();
    ok(['term_id'=>$term_id]);
  } catch (Throwable $e) { $pdo->rollBack(); bad('DB Error: '.$e->getMessage(),500); }
}

/* 3) مجموعة (تبقى كما هي لمن يستخدمها يدوياً) */
if ($action === 'add_exam_group') {
  $grade_id   = (int)($_POST['grade_id'] ?? 0);
  $subject_id = (int)($_POST['subject_id'] ?? 0);
  $term_no    = (int)($_POST['term_no'] ?? 0);
  $group_no   = (int)($_POST['group_no'] ?? 0);
  if (!$grade_id || !$subject_id || !in_array($term_no,[1,2],true) || !in_array($group_no,[1,2,3],true))
    bad('grade_id, subject_id, term_no, group_no(1|2|3) required');

  try {
    $pdo->beginTransaction();
    $st=$pdo->prepare("
      SELECT gst.id AS gst_id
      FROM grade_subjects gs
      JOIN grade_subject_terms gst ON gst.grade_subject_id=gs.id
      JOIN terms t ON t.id=gst.term_id
      WHERE gs.grade_id=? AND gs.subject_id=? AND t.term_no=? AND t.school_year=? LIMIT 1");
    $st->execute([$grade_id,$subject_id,$term_no,YEAR_ALL]);
    $row=$st->fetch();
    if(!$row) bad('أضف الفصل أولاً لهذا الصف/المادة.');
    $gst_id=(int)$row['gst_id'];
    $pdo->prepare("INSERT INTO exam_groups (gst_id,group_no,title) VALUES (?,?,?)
                   ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), title=VALUES(title)")
        ->execute([$gst_id,$group_no,'مجموعة '.$group_no]);
    $gid=(int)$pdo->lastInsertId();
    $pdo->commit();
    ok(['group_id'=>$gid]);
  } catch (Throwable $e) { $pdo->rollBack(); bad('DB Error: '.$e->getMessage(),500); }
}

/* 4) إنشاء امتحان + ملفات + روابط (بدون إجبار اختيار مجموعة؛ افتراضياً 1) */
if ($action === 'create_exam_item') {
  $grade_id   = (int)($_POST['grade_id'] ?? 0);
  $subject_id = (int)($_POST['subject_id'] ?? 0);
  $term_no    = (int)($_POST['term_no'] ?? 0);
  $group_no_in = isset($_POST['group_no']) ? (int)$_POST['group_no'] : null;
  $group_no   = in_array($group_no_in, [1,2,3], true) ? $group_no_in : 1;

  $title      = trim((string)($_POST['title'] ?? ''));
  $exam_date  = $_POST['exam_date'] ?? null;
  $links_text = $_POST['links'] ?? '';

  if (!$grade_id || !$subject_id || !in_array($term_no,[1,2],true) || $title==='') {
    bad('required: grade_id, subject_id, term_no(1|2), title');
  }

  $uploadDir = rtrim(UPLOAD_ROOT,'/').'/'.date('Y/m');
  ensure_upload_dir($uploadDir);

  try {
    $pdo->beginTransaction();

    // احصل على gst_id لزوج الصف/المادة/الفصل
    $st=$pdo->prepare("
      SELECT gst.id AS gst_id
      FROM grade_subjects gs
      JOIN grade_subject_terms gst ON gst.grade_subject_id=gs.id
      JOIN terms t ON t.id=gst.term_id
      WHERE gs.grade_id=? AND gs.subject_id=? AND t.term_no=? AND t.school_year=? LIMIT 1");
    $st->execute([$grade_id,$subject_id,$term_no,YEAR_ALL]);
    $r=$st->fetch();
    if(!$r) bad('أضف الفصل أولاً لهذا الصف/المادة.');
    $gst_id=(int)$r['gst_id'];

    // جلب/إنشاء مجموعة (group_no) تلقائياً
    $group_id = null;
    $st=$pdo->prepare("SELECT id FROM exam_groups WHERE gst_id=? AND group_no=? LIMIT 1");
    $st->execute([$gst_id,$group_no]);
    $g=$st->fetch();
    if($g){
      $group_id=(int)$g['id'];
    } else {
      $st=$pdo->prepare("INSERT INTO exam_groups (gst_id,group_no,title) VALUES (?,?,?)");
      $st->execute([$gst_id,$group_no,'مجموعة '.$group_no]);
      $group_id=(int)$pdo->lastInsertId();
    }

    // إنشاء الامتحان
    $pdo->prepare("INSERT INTO exam_items (group_id,title,exam_date) VALUES (?,?,?)")
        ->execute([$group_id,$title,$exam_date ?: null]);
    $exam_id=(int)$pdo->lastInsertId();

    // ملفات مرفوعة
    $saved=[];
    if (!empty($_FILES['files']['name'][0])) {
      $allowed_ext = ['pdf','jpg','jpeg','png','webp'];
      foreach ($_FILES['files']['name'] as $i=>$name) {
        if ($_FILES['files']['error'][$i]!==UPLOAD_ERR_OK) continue;
        $tmp  = $_FILES['files']['tmp_name'][$i];
        if (!is_uploaded_file($tmp)) continue;
        $size = (int)($_FILES['files']['size'][$i] ?? 0);
        if ($size > MAX_UPLOAD_BYTES) bad('File too large', 400);
        $type = $_FILES['files']['type'][$i] ?? null;
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION) ?: 'bin');
        if (!in_array($ext,$allowed_ext,true)) continue;

        $safe = 'exam_'.$exam_id.'_'.bin2hex(random_bytes(6)).'.'.$ext;
        $dest = rtrim($uploadDir,'/').'/'.$safe;
        if (!@move_uploaded_file($tmp,$dest)) bad('Failed to move upload to '.$dest, 500);

        $rel = rel_from_abs($dest); // مثال: /oman/examapp/uploads/...
        $pdo->prepare("INSERT INTO exam_files (exam_id,file_path,mime_type,file_size) VALUES (?,?,?,?)")
            ->execute([$exam_id,$rel,$type,$size]);
        $saved[]=['path'=>$rel,'mime'=>$type,'size'=>$size];
      }
    }

    // روابط خارجية
    $links = parse_links_text($links_text);
    foreach ($links as $url) {
      $pdo->prepare("INSERT INTO exam_files (exam_id,file_path,mime_type,file_size) VALUES (?,?,?,NULL)")
          ->execute([$exam_id,$url,'link']);
      $saved[]=['path'=>$url,'mime'=>'link','size'=>null];
    }

    $pdo->commit();
    ok(['exam_id'=>$exam_id,'files'=>$saved]);
  } catch (Throwable $e) { $pdo->rollBack(); bad('Upload/DB Error: '.$e->getMessage(),500); }
}

/* 5) إضافة ملفات جديدة لامتحان موجود */
if ($action === 'add_exam_files') {
  $exam_id = (int)($_POST['exam_id'] ?? 0);
  if (!$exam_id) bad('exam_id required');
  $chk = $pdo->prepare("SELECT id FROM exam_items WHERE id=?");
  $chk->execute([$exam_id]);
  if (!$chk->fetchColumn()) bad('exam not found', 404);

  $uploadDir = rtrim(UPLOAD_ROOT,'/').'/'.date('Y/m');
  ensure_upload_dir($uploadDir);

  $saved=[];
  $allowed_ext = ['pdf','jpg','jpeg','png','webp'];
  if (empty($_FILES['files']['name'][0])) bad('no files uploaded');

  try {
    $pdo->beginTransaction();
    foreach ($_FILES['files']['name'] as $i=>$name) {
      if ($_FILES['files']['error'][$i]!==UPLOAD_ERR_OK) continue;
      $tmp  = $_FILES['files']['tmp_name'][$i];
      if (!is_uploaded_file($tmp)) continue;
      $size = (int)($_FILES['files']['size'][$i] ?? 0);
      if ($size > MAX_UPLOAD_BYTES) bad('File too large', 400);
      $type = $_FILES['files']['type'][$i] ?? null;
      $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION) ?: 'bin');
      if (!in_array($ext,$allowed_ext,true)) continue;
      $safe = 'exam_'.$exam_id.'_'.bin2hex(random_bytes(6)).'.'.$ext;
      $dest = rtrim($uploadDir,'/').'/'.$safe;
      if (!@move_uploaded_file($tmp,$dest)) bad('Failed to move upload', 500);
      $rel = rel_from_abs($dest);
      $pdo->prepare("INSERT INTO exam_files (exam_id,file_path,mime_type,file_size) VALUES (?,?,?,?)")
          ->execute([$exam_id,$rel,$type,$size]);
      $saved[]=['path'=>$rel,'mime'=>$type,'size'=>$size];
    }
    $pdo->commit();
    ok(['exam_id'=>$exam_id,'files'=>$saved]);
  } catch (Throwable $e) { $pdo->rollBack(); bad('Upload/DB Error: '.$e->getMessage(),500); }
}

/* 6) إضافة روابط خارجية جديدة لامتحان موجود */
if ($action === 'add_exam_links') {
  $exam_id = (int)($_POST['exam_id'] ?? 0);
  $links_text = $_POST['links'] ?? '';
  if (!$exam_id) bad('exam_id required');

  $chk = $pdo->prepare("SELECT id FROM exam_items WHERE id=?");
  $chk->execute([$exam_id]);
  if (!$chk->fetchColumn()) bad('exam not found', 404);

  $links = parse_links_text($links_text);
  if (!$links) bad('no valid links');

  try {
    $pdo->beginTransaction();
    foreach ($links as $url) {
      $pdo->prepare("INSERT INTO exam_files (exam_id,file_path,mime_type,file_size) VALUES (?,?,?,NULL)")
          ->execute([$exam_id,$url,'link']);
    }
    $pdo->commit();
    ok(['exam_id'=>$exam_id,'count'=>count($links)]);
  } catch (Throwable $e) { $pdo->rollBack(); bad('DB Error: '.$e->getMessage(),500); }
}

/* ====== تعديل / حذف ====== */
if ($action === 'update_exam_item') {
  $id = (int)($_POST['id'] ?? 0);
  $title = trim((string)($_POST['title'] ?? ''));
  $exam_date = $_POST['exam_date'] ?? null;
  if(!$id || $title==='') bad('id & title required');
  $pdo->prepare("UPDATE exam_items SET title=?, exam_date=? WHERE id=?")->execute([$title,$exam_date ?: null,$id]);
  ok();
}

if ($action === 'delete_exam_item') {
  $id = (int)($_POST['id'] ?? 0);
  if(!$id) bad('id required');
  $fs=$pdo->prepare("SELECT file_path FROM exam_files WHERE exam_id=?");
  $fs->execute([$id]);
  foreach ($fs->fetchAll(PDO::FETCH_COLUMN) as $rel) {
    if ($rel) {
      if (is_local_upload_path($rel)) {
        $abs = abs_from_rel($rel);
        if (is_file($abs)) @unlink($abs);
      }
    }
  }
  $pdo->prepare("DELETE FROM exam_items WHERE id=?")->execute([$id]); // FK يمسح exam_files إن كان ON DELETE CASCADE، وإن لم يوجد سيبقى آمن
  $pdo->prepare("DELETE FROM exam_files WHERE exam_id=?")->execute([$id]); // تأكيد مسح السجلات المرفقة
  ok();
}

if ($action === 'delete_exam_file') {
  $id = (int)($_POST['id'] ?? 0);
  if(!$id) bad('id required');
  $st=$pdo->prepare("SELECT file_path FROM exam_files WHERE id=?");
  $st->execute([$id]);
  $rel=$st->fetchColumn();
  if ($rel) {
    if (is_local_upload_path($rel)) {
      $abs=abs_from_rel($rel);
      if (is_file($abs)) @unlink($abs);
    }
  }
  $pdo->prepare("DELETE FROM exam_files WHERE id=?")->execute([$id]);
  ok();
}

bad('unknown action',404);