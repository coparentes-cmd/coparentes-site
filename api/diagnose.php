<?php
declare(strict_types=1);

/**
 * Temporary diagnostics for comments setup.
 * Open: /api/diagnose.php?key=TWOJE_HASLO_ADMINA
 * Delete this file after setup works.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$configPath = __DIR__ . '/config.php';
$result = [
  'php_version' => PHP_VERSION,
  'config_exists' => is_file($configPath),
  'pdo_mysql' => in_array('mysql', PDO::getAvailableDrivers(), true),
];

if (!$result['config_exists']) {
  echo json_encode(['ok' => false, 'error' => 'Brak config.php', 'checks' => $result], JSON_UNESCAPED_UNICODE);
  exit;
}

try {
  /** @var array $config */
  $config = require $configPath;
} catch (Throwable $e) {
  echo json_encode([
    'ok' => false,
    'error' => 'config.php ma błąd składni',
    'checks' => $result,
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$key = (string) ($_GET['key'] ?? '');
$adminPassword = (string) ($config['admin_password'] ?? '');
if ($adminPassword === '' || !hash_equals($adminPassword, $key)) {
  http_response_code(401);
  echo json_encode(['ok' => false, 'error' => 'Podaj poprawne ?key=HASLO_ADMINA'], JSON_UNESCAPED_UNICODE);
  exit;
}

$result['db_host'] = (string) ($config['db_host'] ?? '');
$result['db_name'] = (string) ($config['db_name'] ?? '');
$result['db_user'] = (string) ($config['db_user'] ?? '');
$result['db_pass_set'] = isset($config['db_pass']) && $config['db_pass'] !== '';

try {
  $dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $config['db_host'],
    $config['db_name'],
    $config['db_charset'] ?? 'utf8mb4'
  );
  $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
  $result['db_connected'] = true;

  $table = $pdo->query("SHOW TABLES LIKE 'comments'")->fetch();
  $result['table_comments_exists'] = (bool) $table;

  if ($result['table_comments_exists']) {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM comments')->fetchColumn();
    $result['comments_count'] = $count;
  }

  echo json_encode(['ok' => true, 'checks' => $result], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  $result['db_connected'] = false;
  // Safe: SQLSTATE only, no password / DSN with pass.
  $result['db_error_code'] = $e instanceof PDOException ? $e->getCode() : 'error';
  $result['db_error'] = $e instanceof PDOException
    ? 'Połączenie PDO nieudane (sprawdź host/user/hasło/nazwę bazy)'
    : 'Inny błąd połączenia';

  echo json_encode(['ok' => false, 'checks' => $result], JSON_UNESCAPED_UNICODE);
}
