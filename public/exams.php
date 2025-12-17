<?php
/**
 * Enhanced Exams API with automatic fallback & debug mode.
 * Activate debug: /public/exams.php?debug=1
 */
declare(strict_types=1);

// Toggle debug via query param (DON'T leave enabled in production)
const BASE_DEBUG = false;
$debug = BASE_DEBUG || isset($_GET['debug']);

header('Content-Type: application/json; charset=utf-8');

$startedAt = microtime(true);

function outJson(int $status, array $payload, bool $debug): void {
    http_response_code($status);
    if (!$debug) {
        // Remove sensitive detail keys when not debugging
        unset($payload['trace'], $payload['sql_debug']);
    }
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    require __DIR__ . '/../config/db.php'; // expects $pdo
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new RuntimeException('PDO instance $pdo not found in config/db.php');
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    outJson(500, [
        'ok' => false,
        'error' => 'DB connection failed',
        'detail' => $debug ? $e->getMessage() : null
    ], $debug);
}

/**
 * Helper to read int param or null
 */
function intParam(string $k): ?int {
    if (!isset($_GET[$k]) || $_GET[$k] === '') return null;
    return is_numeric($_GET[$k]) ? (int)$_GET[$k] : null;
}

/* Collect Inputs */
$grade_id   = intParam('grade_id');
$subject_id = intParam('subject_id');
$term_no    = intParam('term_no');
$group_no   = intParam('group_no');
$qRaw       = isset($_GET['q']) ? trim($_GET['q']) : '';
$q          = $qRaw === '' ? null : $qRaw;

$allowedSort = ['latest','oldest'];
$sort = (isset($_GET['sort']) && in_array($_GET['sort'],$allowedSort,true)) ? $_GET['sort'] : 'latest';

$page = max(1, (int)($_GET['page'] ?? 1));
$per  = (int)($_GET['per_page'] ?? 20);
if ($per < 1)  $per = 20;
if ($per > 100) $per = 100;

/* Detect if column exam_date exists (to avoid SQL error) */
$hasExamDate = false;
try {
    $colCheck = $pdo->prepare("SHOW COLUMNS FROM exam_items LIKE 'exam_date'");
    $colCheck->execute();
    $hasExamDate = (bool)$colCheck->fetch();
} catch (Throwable $ignore) {
    // ignore; fallback false
}

/* Build SQL */
$sqlFrom = " FROM exam_items ei
  JOIN exam_groups eg ON eg.id = ei.group_id
  JOIN grade_subject_terms gst ON gst.id = eg.gst_id
  JOIN grade_subjects gs ON gs.id = gst.grade_subject_id
  JOIN grades g ON g.id = gs.grade_id
  JOIN subjects s ON s.id = gs.subject_id
  JOIN terms t ON t.id = gst.term_id
  WHERE 1=1";

$params = [];
if (!is_null($grade_id)) { $sqlFrom .= " AND gs.grade_id=?"; $params[] = $grade_id; }
if (!is_null($subject_id)) { $sqlFrom .= " AND gs.subject_id=?"; $params[] = $subject_id; }
if (!is_null($term_no)) { $sqlFrom .= " AND t.term_no=?"; $params[] = $term_no; }
if (!is_null($group_no)) { $sqlFrom .= " AND eg.group_no=?"; $params[] = $group_no; }
if (!is_null($q)) { $sqlFrom .= " AND ei.title LIKE ?"; $params[] = '%'.$q.'%'; }

/* Determine ordering */
if ($hasExamDate) {
    $orderClause = ($sort === 'oldest')
        ? " ORDER BY ei.exam_date ASC, ei.id ASC"
        : " ORDER BY ei.exam_date DESC, ei.id DESC";
} else {
    // Fallback if exam_date column missing
    $orderClause = ($sort === 'oldest')
        ? " ORDER BY ei.id ASC"
        : " ORDER BY ei.id DESC";
}

$sqlCount = "SELECT COUNT(*) ".$sqlFrom;

/* Pagination */
$page = max(1,$page);
$offset = ($page - 1)*$per;

$selectDate = $hasExamDate ? "ei.exam_date," : "NULL AS exam_date,";

$sqlData = "SELECT ei.id, ei.title, $selectDate
    gs.grade_id, g.name_ar AS grade_name,
    gs.subject_id, s.name_ar AS subject_name,
    t.term_no, eg.group_no
    ".$sqlFrom.$orderClause." LIMIT $per OFFSET $offset";

try {
    $stCount = $pdo->prepare($sqlCount);
    $stCount->execute($params);
    $total = (int)$stCount->fetchColumn();
    $pages = $total > 0 ? (int)ceil($total/$per) : 1;
    if ($page > $pages) {
        $page = $pages;
        $offset = ($page - 1)*$per;
        $sqlData = preg_replace('/LIMIT\\s+\\d+\\s+OFFSET\\s+\\d+/i', "LIMIT $per OFFSET $offset", $sqlData);
    }

    $stData = $pdo->prepare($sqlData);
    $stData->execute($params);
    $rows = $stData->fetchAll();

    $ids = array_column($rows,'id');
    $filesMap = [];
    if ($ids) {
        $in = implode(',', array_fill(0,count($ids),'?'));
        $sqlFiles = "SELECT exam_id, file_path, mime_type, file_size FROM exam_files WHERE exam_id IN ($in) ORDER BY id";
        $fSt = $pdo->prepare($sqlFiles);
        $fSt->execute($ids);
        while($f = $fSt->fetch()){
            $filesMap[$f['exam_id']][] = [
                'path'        => $f['file_path'],
                'mime'        => $f['mime_type'],
                'size'        => (int)$f['file_size'],
                'is_external' => preg_match('#^https?://#i',$f['file_path']) === 1
            ];
        }
    }

    foreach($rows as &$r){
        $files = $filesMap[$r['id']] ?? [];
        $extCount = 0;
        foreach($files as $one){ if($one['mime']==='link') $extCount++; }
        $r['files'] = $files;
        $r['attachments_count'] = count($files);
        $r['external_links_count'] = $extCount;
        $r['primary_file'] = $files[0]['path'] ?? null;
    }
    unset($r);

    $duration = (int)((microtime(true)-$startedAt)*1000);

    outJson(200, [
        'ok'=>true,
        'page'=>$page,
        'per_page'=>$per,
        'total'=>$total,
        'pages'=>$pages,
        'count'=>count($rows),
        'meta'=>[
            'generated_at'=>gmdate('c'),
            'sort'=>$sort,
            'has_exam_date'=>$hasExamDate,
            'query_time_ms'=>$duration
        ],
        'data'=>$rows,
        'sql_debug'=>$debug?[
            'sqlCount'=>$sqlCount,
            'sqlData'=>$sqlData,
            'params'=>$params
        ]:null
    ], $debug);

} catch (Throwable $e) {
    outJson(500, [
        'ok'=>false,
        'error'=>'query failed',
        'detail'=>$debug ? $e->getMessage() : null,
        'trace'=>$debug ? $e->getTraceAsString() : null
    ], $debug);
}