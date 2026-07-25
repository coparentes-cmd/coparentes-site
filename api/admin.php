<?php
declare(strict_types=1);

require __DIR__ . '/lib.php';

comments_start_admin_session();

$wantsJson = (
  (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
  || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
  || (isset($_GET['format']) && $_GET['format'] === 'json')
);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
  if ($method === 'POST') {
    $data = comments_read_json_body();
    $action = (string) ($data['action'] ?? $_POST['action'] ?? '');

    if ($action === 'login') {
      handle_admin_login($data);
    }
    if ($action === 'logout') {
      handle_admin_logout();
    }

    comments_require_admin();

    if ($action === 'approve' || $action === 'reject' || $action === 'delete') {
      handle_admin_moderate($action, $data);
    }

    comments_json_error(400, 'Nieznana akcja.');
  }

  if ($method === 'GET') {
    if (!comments_admin_logged_in()) {
      if ($wantsJson) {
        comments_json_error(401, 'Wymagane logowanie administratora.');
      }
      render_admin_login_page();
      exit;
    }

    if ($wantsJson || (isset($_GET['format']) && $_GET['format'] === 'json')) {
      $status = (string) ($_GET['status'] ?? 'pending');
      if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
        $status = 'pending';
      }
      $stmt = comments_pdo()->prepare(
        'SELECT id, article_slug, author_name, author_email, author_url, body, status, created_at
         FROM comments
         WHERE status = :status
         ORDER BY created_at DESC, id DESC
         LIMIT 200'
      );
      $stmt->execute([':status' => $status]);
      $rows = array_map('admin_comment_dto', $stmt->fetchAll());
      comments_json_ok(['comments' => $rows]);
    }

    render_admin_dashboard();
    exit;
  }

  comments_json_error(405, 'Niedozwolona metoda.');
} catch (Throwable $e) {
  comments_json_error(500, 'Nie udało się obsłużyć żądania.');
}

function handle_admin_login(array $data): void
{
  $cfg = comments_config();
  $password = (string) ($data['password'] ?? '');
  $expected = (string) ($cfg['admin_password'] ?? '');

  if ($expected === '' || !hash_equals($expected, $password)) {
    comments_json_error(401, 'Nieprawidłowe hasło.');
  }

  comments_start_admin_session();
  session_regenerate_id(true);
  $_SESSION['comments_admin'] = true;
  comments_json_ok(['logged_in' => true]);
}

function handle_admin_logout(): void
{
  comments_start_admin_session();
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
  }
  session_destroy();
  comments_json_ok(['logged_in' => false]);
}

function handle_admin_moderate(string $action, array $data): void
{
  $id = (int) ($data['id'] ?? 0);
  if ($id <= 0) {
    comments_json_error(400, 'Nieprawidłowy identyfikator.');
  }

  $pdo = comments_pdo();

  if ($action === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM comments WHERE id = :id');
    $stmt->execute([':id' => $id]);
    comments_json_ok(['deleted' => true]);
  }

  $status = $action === 'approve' ? 'approved' : 'rejected';
  $stmt = $pdo->prepare('UPDATE comments SET status = :status WHERE id = :id');
  $stmt->execute([':status' => $status, ':id' => $id]);
  comments_json_ok(['updated' => true, 'status' => $status]);
}

function h(string $value): string
{
  return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function render_admin_login_page(): void
{
  header('Content-Type: text/html; charset=utf-8');
  echo '<!DOCTYPE html><html lang="pl"><head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />';
  echo '<title>Moderacja komentarzy — Coparentes</title>';
  echo '<style>body{font-family:Inter,system-ui,sans-serif;max-width:420px;margin:48px auto;padding:0 16px;color:#121722}input,button{width:100%;padding:12px;margin:8px 0;font:inherit}button{background:#0080FF;color:#fff;border:0;border-radius:12px;cursor:pointer}.msg{color:#d84f4c;min-height:1.2em}</style>';
  echo '</head><body><h1>Moderacja komentarzy</h1><p>Zaloguj się, aby zatwierdzać komentarze. Adresy e-mail są widoczne tylko tutaj.</p>';
  echo '<form id="loginForm"><label>Hasło admina<input type="password" name="password" required autocomplete="current-password" /></label>';
  echo '<button type="submit">Zaloguj</button><p class="msg" id="msg"></p></form>';
  echo '<script>
  document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const password = new FormData(e.target).get("password");
    const msg = document.getElementById("msg");
    msg.textContent = "";
    try {
      const res = await fetch(location.pathname, {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept": "application/json" },
        body: JSON.stringify({ action: "login", password })
      });
      const data = await res.json();
      if (!res.ok || !data.ok) throw new Error(data.error || "Błąd logowania");
      location.reload();
    } catch (err) {
      msg.textContent = err.message || "Błąd logowania";
    }
  });
  </script></body></html>';
}

function render_admin_dashboard(): void
{
  $status = (string) ($_GET['status'] ?? 'pending');
  if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
    $status = 'pending';
  }

  $stmt = comments_pdo()->prepare(
    'SELECT id, article_slug, author_name, author_email, author_url, body, status, created_at
     FROM comments
     WHERE status = :status
     ORDER BY created_at DESC, id DESC
     LIMIT 200'
  );
  $stmt->execute([':status' => $status]);
  $rows = $stmt->fetchAll();

  header('Content-Type: text/html; charset=utf-8');
  echo '<!DOCTYPE html><html lang="pl"><head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />';
  echo '<title>Moderacja komentarzy — Coparentes</title>';
  echo '<style>
  body{font-family:Inter,system-ui,sans-serif;max-width:920px;margin:32px auto;padding:0 16px;color:#121722;background:#f7f9fc}
  .top{display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;margin-bottom:20px}
  .tabs a{margin-right:10px;text-decoration:none;color:#0080FF;font-weight:600}
  .tabs a.active{text-decoration:underline}
  .card{background:#fff;border:1px solid rgba(18,23,34,.08);border-radius:16px;padding:16px 18px;margin:0 0 14px}
  .meta{color:#5d6678;font-size:.92rem;margin:0 0 8px}
  .email{font-family:ui-monospace,monospace;font-size:.9rem;background:#f3f6fb;padding:2px 6px;border-radius:6px}
  .actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
  button{border:0;border-radius:10px;padding:10px 14px;font:inherit;cursor:pointer}
  .approve{background:#00C896;color:#fff}.reject{background:#ff6b68;color:#fff}.delete{background:#121722;color:#fff}.logout{background:#e8eef7}
  </style></head><body>';
  echo '<div class="top"><div><h1>Moderacja komentarzy</h1><p class="meta">E-maile są widoczne wyłącznie po zalogowaniu.</p></div>';
  echo '<button class="logout" type="button" id="logoutBtn">Wyloguj</button></div>';
  echo '<div class="tabs">';
  foreach (['pending' => 'Oczekujące', 'approved' => 'Opublikowane', 'rejected' => 'Odrzucone'] as $key => $label) {
    $cls = $key === $status ? ' class="active"' : '';
    echo '<a' . $cls . ' href="?status=' . h($key) . '">' . h($label) . '</a>';
  }
  echo '</div>';

  if (!$rows) {
    echo '<p>Brak komentarzy w tej kategorii.</p>';
  }

  foreach ($rows as $row) {
    echo '<article class="card" data-id="' . (int) $row['id'] . '">';
    echo '<p class="meta">' . h((string) $row['created_at']) . ' · ' . h((string) $row['article_slug']) . ' · ' . h((string) $row['status']) . '</p>';
    echo '<p><strong>' . h((string) $row['author_name']) . '</strong> ';
    echo '<span class="email">' . h((string) $row['author_email']) . '</span></p>';
    if (!empty($row['author_url'])) {
      echo '<p><a href="' . h((string) $row['author_url']) . '" rel="noopener noreferrer nofollow" target="_blank">' . h((string) $row['author_url']) . '</a></p>';
    }
    echo '<p>' . nl2br(h((string) $row['body'])) . '</p>';
    echo '<div class="actions">';
    if ($row['status'] !== 'approved') {
      echo '<button class="approve" type="button" data-action="approve">Zatwierdź</button>';
    }
    if ($row['status'] !== 'rejected') {
      echo '<button class="reject" type="button" data-action="reject">Odrzuć</button>';
    }
    echo '<button class="delete" type="button" data-action="delete">Usuń</button>';
    echo '</div></article>';
  }

  echo '<script>
  async function postAction(action, id) {
    const res = await fetch(location.pathname, {
      method: "POST",
      headers: { "Content-Type": "application/json", "Accept": "application/json" },
      body: JSON.stringify({ action, id })
    });
    const data = await res.json();
    if (!res.ok || !data.ok) throw new Error(data.error || "Błąd");
  }
  document.getElementById("logoutBtn").addEventListener("click", async () => {
    await postAction("logout");
    location.reload();
  });
  document.querySelectorAll(".card .actions button").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const card = btn.closest(".card");
      const id = Number(card.dataset.id);
      const action = btn.dataset.action;
      try {
        await postAction(action, id);
        card.remove();
      } catch (err) {
        alert(err.message || "Błąd");
      }
    });
  });
  </script></body></html>';
}
