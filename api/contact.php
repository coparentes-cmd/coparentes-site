<?php
declare(strict_types=1);

require __DIR__ . '/lib.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
  if ($method === 'POST') {
    handle_contact_submit();
  }
  comments_json_error(405, 'Niedozwolona metoda.');
} catch (Throwable $e) {
  comments_json_error(500, 'Nie udało się wysłać wiadomości.');
}

function handle_contact_submit(): void
{
  $data = comments_read_json_body();

  // Honeypot — bots fill this; humans should leave empty.
  $honeypot = trim((string) ($data['website'] ?? $data['company'] ?? ''));
  if ($honeypot !== '') {
    comments_json_ok(['sent' => true]);
  }

  $name = trim((string) ($data['name'] ?? ''));
  $email = trim((string) ($data['email'] ?? ''));
  $message = trim((string) ($data['message'] ?? ''));

  if ($name === '' || strlen($name) > 80) {
    comments_json_error(400, 'Podaj imię (max 80 znaków).');
  }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
    comments_json_error(400, 'Podaj prawidłowy adres e-mail.');
  }
  if ($message === '' || strlen($message) > 4000) {
    comments_json_error(400, 'Napisz wiadomość (max 4000 znaków).');
  }

  $cfg = comments_config();
  $to = trim((string) ($cfg['contact_to_email'] ?? ''));
  $from = trim((string) ($cfg['contact_from_email'] ?? $to));

  if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
    comments_json_error(500, 'Brak skonfigurowanego adresu kontaktowego.');
  }
  if ($from === '' || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
    $from = $to;
  }

  $ipHash = comments_client_ip_hash();
  if ($ipHash && !contact_rate_limit_ok($ipHash)) {
    comments_json_error(429, 'Zbyt wiele prób. Spróbuj ponownie później.');
  }

  $subject = 'Wiadomość z formularza Coparentes — ' . $name;
  $body = "Imię: {$name}\nE-mail: {$email}\n\nWiadomość:\n{$message}\n";

  $headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'From: Coparentes <' . $from . '>',
    'Reply-To: ' . $email,
    'X-Mailer: Coparentes-Contact',
  ];

  $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
  $ok = @mail($to, $encodedSubject, $body, implode("\r\n", $headers));

  if (!$ok) {
    comments_json_error(500, 'Nie udało się wysłać wiadomości. Spróbuj ponownie później.');
  }

  comments_json_ok([
    'sent' => true,
    'message' => 'Dziękujemy. Wiadomość została wysłana.',
  ]);
}

function contact_rate_limit_ok(string $ipHash): bool
{
  $dir = sys_get_temp_dir() . '/coparentes_contact_rate';
  if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
    return true;
  }

  $file = $dir . '/' . preg_replace('/[^a-f0-9]/', '', $ipHash) . '.json';
  $now = time();
  $window = 3600;
  $max = 5;
  $timestamps = [];

  if (is_file($file)) {
    $raw = @file_get_contents($file);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    if (is_array($decoded)) {
      foreach ($decoded as $ts) {
        if (is_int($ts) && ($now - $ts) < $window) {
          $timestamps[] = $ts;
        }
      }
    }
  }

  if (count($timestamps) >= $max) {
    return false;
  }

  $timestamps[] = $now;
  @file_put_contents($file, json_encode($timestamps), LOCK_EX);
  return true;
}
