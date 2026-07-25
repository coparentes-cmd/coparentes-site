<?php
declare(strict_types=1);

require __DIR__ . '/lib.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
  if ($method === 'GET') {
    handle_list_comments();
  }
  if ($method === 'POST') {
    handle_create_comment();
  }
  comments_json_error(405, 'Niedozwolona metoda.');
} catch (PDOException $e) {
  comments_json_error(500, 'Błąd połączenia z bazą danych. Sprawdź config.php (host, nazwa bazy, user, hasło).');
} catch (Throwable $e) {
  // Do not echo emails, passwords, or request payloads.
  comments_json_error(500, 'Nie udało się obsłużyć żądania.');
}

function handle_list_comments(): void
{
  $slug = trim((string) ($_GET['slug'] ?? ''));
  if ($slug === '' || !comments_is_allowed_slug($slug)) {
    comments_json_error(400, 'Nieprawidłowy artykuł.');
  }

  $stmt = comments_pdo()->prepare(
    'SELECT id, author_name, author_url, body, created_at
     FROM comments
     WHERE article_slug = :slug AND status = :status
     ORDER BY created_at ASC, id ASC'
  );
  $stmt->execute([
    ':slug' => $slug,
    ':status' => 'approved',
  ]);

  $rows = $stmt->fetchAll();
  $comments = array_map('public_comment_dto', $rows);
  comments_json_ok(['comments' => $comments]);
}

function handle_create_comment(): void
{
  $data = comments_read_json_body();

  // Honeypot — bots fill this; humans should leave empty.
  $honeypot = trim((string) ($data['website'] ?? $data['company'] ?? ''));
  if ($honeypot !== '') {
    // Fake success to avoid leaking filter details.
    comments_json_ok(['queued' => true]);
  }

  $slug = trim((string) ($data['article_slug'] ?? ''));
  $name = trim((string) ($data['author_name'] ?? ''));
  $email = trim((string) ($data['author_email'] ?? ''));
  $urlRaw = isset($data['author_url']) ? (string) $data['author_url'] : '';
  $body = trim((string) ($data['body'] ?? ''));

  if ($slug === '' || !comments_is_allowed_slug($slug)) {
    comments_json_error(400, 'Nieprawidłowy artykuł.');
  }
  if ($name === '' || mb_strlen($name) > 80) {
    comments_json_error(400, 'Podaj imię (max 80 znaków).');
  }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
    // Generic message — do not echo submitted email.
    comments_json_error(400, 'Podaj prawidłowy adres e-mail.');
  }
  if ($body === '' || mb_strlen($body) > 4000) {
    comments_json_error(400, 'Napisz komentarz (max 4000 znaków).');
  }

  try {
    $url = comments_normalize_url($urlRaw === '' ? null : $urlRaw);
  } catch (InvalidArgumentException $e) {
    comments_json_error(400, 'Nieprawidłowy adres URL.');
  }

  $ipHash = comments_client_ip_hash();
  $pdo = comments_pdo();

  // Simple rate limit: max 5 submissions / hour per IP hash.
  if ($ipHash) {
    $rate = $pdo->prepare(
      'SELECT COUNT(*) AS c FROM comments
       WHERE ip_hash = :ip AND created_at >= (NOW() - INTERVAL 1 HOUR)'
    );
    $rate->execute([':ip' => $ipHash]);
    $count = (int) ($rate->fetch()['c'] ?? 0);
    if ($count >= 5) {
      comments_json_error(429, 'Zbyt wiele prób. Spróbuj ponownie później.');
    }
  }

  $insert = $pdo->prepare(
    'INSERT INTO comments (article_slug, author_name, author_email, author_url, body, status, ip_hash)
     VALUES (:slug, :name, :email, :url, :body, :status, :ip)'
  );
  $insert->execute([
    ':slug' => $slug,
    ':name' => $name,
    ':email' => $email,
    ':url' => $url,
    ':body' => $body,
    ':status' => 'pending',
    ':ip' => $ipHash,
  ]);

  // Never return the email or the inserted private row.
  comments_json_ok([
    'queued' => true,
    'message' => 'Dziękujemy. Komentarz czeka na moderację.',
  ]);
}
