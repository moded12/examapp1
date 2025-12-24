<?php
declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_NAME = 'oman_exams';
$DB_USER = 'oman_exams';
$DB_PASS = 'Tvvcrtv1610@'; // كما قدمتَ

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'error'=>'DB Connection failed: '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
  exit;
}