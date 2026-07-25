<?php
declare(strict_types=1);

/**
 * Shared helpers for comments API.
 * Email addresses must never appear in public DTOs / responses.
 */

function comments_config(): array
{
  static $config = null;
  if ($config !== null) {
    return $config;
  }

  $path = __DIR__ . '/config.php';
  if (!is_file($path)) {
    comments_json_error(500, 'Brak pliku konfiguracyjnego API.');
  }

  /** @var array $loaded */
  $loaded = require $path;
  $config = $loaded;
  return $config;
}

function comments_pdo(): PDO
{
  static $pdo = null;
  if ($pdo instanceof PDO) {
    return $pdo;
  }

  $cfg = comments_config();
  $dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $cfg['db_host'],
    $cfg['db_name'],
    $cfg['db_charset'] ?? 'utf8mb4'
  );

  $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);

  return $pdo;
}

function comments_json_headers(): void
{
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store');
  header('X-Content-Type-Options: nosniff');
}

function comments_json_error(int $status, string $message): void
{
  http_response_code($status);
  comments_json_headers();
  echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
  exit;
}

function comments_json_ok(array $payload = []): void
{
  comments_json_headers();
  echo json_encode(['ok' => true] + $payload, JSON_UNESCAPED_UNICODE);
  exit;
}

/** Public DTO — NEVER includes author_email. */
function public_comment_dto(array $row): array
{
  return [
    'id' => (int) $row['id'],
    'author_name' => (string) $row['author_name'],
    'author_url' => $row['author_url'] !== null && $row['author_url'] !== ''
      ? (string) $row['author_url']
      : null,
    'body' => (string) $row['body'],
    'created_at' => (string) $row['created_at'],
  ];
}

/** Admin DTO — email only for authenticated moderators. */
function admin_comment_dto(array $row): array
{
  return [
    'id' => (int) $row['id'],
    'article_slug' => (string) $row['article_slug'],
    'author_name' => (string) $row['author_name'],
    'author_email' => (string) $row['author_email'],
    'author_url' => $row['author_url'] !== null && $row['author_url'] !== ''
      ? (string) $row['author_url']
      : null,
    'body' => (string) $row['body'],
    'status' => (string) $row['status'],
    'created_at' => (string) $row['created_at'],
  ];
}

function comments_read_json_body(): array
{
  $raw = file_get_contents('php://input');
  if ($raw === false || trim($raw) === '') {
    return $_POST ?: [];
  }

  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function comments_client_ip_hash(): ?string
{
  $cfg = comments_config();
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';
  if ($ip === '') {
    return null;
  }
  $salt = (string) ($cfg['ip_hash_salt'] ?? 'coparentes');
  return hash('sha256', $salt . '|' . $ip);
}

function comments_is_allowed_slug(string $slug): bool
{
  $cfg = comments_config();
  $allowed = $cfg['allowed_slugs'] ?? [];
  return in_array($slug, $allowed, true);
}

function comments_normalize_url(?string $url): ?string
{
  if ($url === null) {
    return null;
  }
  $url = trim($url);
  if ($url === '') {
    return null;
  }
  if (!preg_match('#^https?://#i', $url)) {
    $url = 'https://' . $url;
  }
  if (!filter_var($url, FILTER_VALIDATE_URL)) {
    throw new InvalidArgumentException('invalid_url');
  }
  $parts = parse_url($url);
  if (!isset($parts['scheme']) || !in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
    throw new InvalidArgumentException('invalid_url');
  }
  return $url;
}

function comments_start_admin_session(): void
{
  if (session_status() === PHP_SESSION_ACTIVE) {
    return;
  }

  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Strict',
  ]);
  session_start();
}

function comments_admin_logged_in(): bool
{
  comments_start_admin_session();
  return !empty($_SESSION['comments_admin']);
}

function comments_require_admin(): void
{
  if (!comments_admin_logged_in()) {
    comments_json_error(401, 'Wymagane logowanie administratora.');
  }
}
