<?php
/**
 * One-time content seed from static HTML extracts (exact copy).
 *
 * @package Coparentes
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Seed pages, posts, categories, and reading settings.
 */
function coparentes_seed_content(): void
{
  if (get_option('coparentes_seeded_v1') === '1') {
    return;
  }

  $home_id = coparentes_ensure_page([
    'slug' => 'start',
    'title' => 'Coparentes — spokojne rodzicielstwo po rozstaniu',
    'content' => coparentes_read_content_file('landing-pl.html'),
    'template' => '',
  ]);

  $blog_id = coparentes_ensure_page([
    'slug' => 'blog',
    'title' => 'Blog',
    'content' => '',
    'template' => '',
  ]);

  update_option('show_on_front', 'page');
  update_option('page_on_front', $home_id);
  update_option('page_for_posts', $blog_id);

  // Legal pages
  foreach (
    [
      [
        'slug' => 'polityka-prywatnosci',
        'title' => 'Polityka prywatności i cookies',
        'file' => 'page-polityka-prywatnosci.html',
        'template' => 'page-templates/page-legal.php',
        'excerpt' => 'Polityka prywatności i cookies strony Coparentes.ai.',
      ],
      [
        'slug' => 'regulamin',
        'title' => 'Regulamin strony internetowej',
        'file' => 'page-regulamin.html',
        'template' => 'page-templates/page-legal.php',
        'excerpt' => 'Regulamin strony internetowej strony Coparentes.ai.',
      ],
    ] as $page
  ) {
    $html = coparentes_read_content_file($page['file']);
    coparentes_ensure_page([
      'slug' => $page['slug'],
      'title' => $page['title'],
      'content' => $html,
      'template' => $page['template'],
      'excerpt' => $page['excerpt'],
    ]);
  }

  // Language landing pages (exact translated HTML)
  foreach (['en', 'de', 'es', 'fr', 'zh'] as $lang) {
    $titles = [
      'en' => 'Coparentes — calm co-parenting after separation',
      'de' => 'Coparentes — ruhige Elternschaft nach der Trennung',
      'es' => 'Coparentes — crianza compartida tranquila tras la separación',
      'fr' => 'Coparentes — co-parentalité sereine après la séparation',
      'zh' => 'Coparentes — 分居后的平静共同育儿',
    ];
    coparentes_ensure_page([
      'slug' => $lang,
      'title' => $titles[$lang],
      'content' => coparentes_read_content_file("landing-{$lang}.html"),
      'template' => "page-templates/page-lang-{$lang}.php",
    ]);
  }

  if (function_exists('coparentes_sync_landing_pages')) {
    coparentes_sync_landing_pages(true);
  }

  // Translated legal pages (exact copy from static HTML)
  $legal_i18n_path = get_template_directory() . '/content/legal-i18n.json';
  if (is_readable($legal_i18n_path)) {
    $legal_i18n = json_decode((string) file_get_contents($legal_i18n_path), true);
    if (is_array($legal_i18n)) {
      foreach ($legal_i18n as $page) {
        $html = coparentes_read_content_file($page['file'] ?? '');
        coparentes_ensure_page([
          'slug' => $page['slug'],
          'title' => $page['title'],
          'content' => $html,
          'template' => 'page-templates/page-legal.php',
          'excerpt' => $page['excerpt'] ?? '',
        ]);
      }
    }
  }

  // Categories
  $category_map = [];
  foreach (['Rozwód', 'Dzieci', 'Ugoda mediacyjna'] as $cat_name) {
    $existing = get_term_by('name', $cat_name, 'category');
    if ($existing && !is_wp_error($existing)) {
      $category_map[$cat_name] = (int) $existing->term_id;
      continue;
    }
    $created = wp_insert_term($cat_name, 'category', [
      'slug' => sanitize_title($cat_name),
    ]);
    if (!is_wp_error($created)) {
      $category_map[$cat_name] = (int) $created['term_id'];
    }
  }

  $manifest_path = get_template_directory() . '/content/manifest.json';
  if (!is_readable($manifest_path)) {
    update_option('coparentes_seeded_v1', '1');
    return;
  }

  $manifest = json_decode((string) file_get_contents($manifest_path), true);
  if (!is_array($manifest)) {
    update_option('coparentes_seeded_v1', '1');
    return;
  }

  // Author pages first
  foreach ($manifest as $item) {
    if (($item['type'] ?? '') !== 'author') {
      continue;
    }
    $body = coparentes_read_content_file('post-' . $item['slug'] . '.html');
    $content = '<p class="blog-author__eyebrow">Autorka</p>' . "\n" . $body;
    // Author template wraps title; store only body paragraphs
    $content = $body;
    coparentes_ensure_page([
      'slug' => $item['slug'],
      'title' => $item['title'],
      'content' => $content,
      'template' => 'page-templates/page-author.php',
      'excerpt' => $item['description'] ?? '',
    ]);
  }

  // Blog posts
  foreach ($manifest as $item) {
    if (($item['type'] ?? '') === 'author') {
      continue;
    }

    $existing = get_page_by_path($item['slug'], OBJECT, 'post');
    if ($existing instanceof WP_Post) {
      continue;
    }

    $body = coparentes_read_content_file('post-' . $item['slug'] . '.html');
    $post_id = wp_insert_post([
      'post_type' => 'post',
      'post_status' => 'publish',
      'post_name' => $item['slug'],
      'post_title' => $item['title'],
      'post_content' => $body,
      'post_excerpt' => $item['excerpt'] ?? '',
      'post_date' => ($item['date'] ?? '2026-07-25') . ' 10:00:00',
      'comment_status' => 'open',
    ], true);

    if (is_wp_error($post_id) || !$post_id) {
      continue;
    }

    $term_ids = [];
    foreach ($item['categories'] ?? [] as $cat_name) {
      if (isset($category_map[$cat_name])) {
        $term_ids[] = $category_map[$cat_name];
      }
    }
    if ($term_ids) {
      wp_set_post_terms($post_id, $term_ids, 'category');
    }

    if (!empty($item['author_slug'])) {
      update_post_meta($post_id, '_coparentes_author_slug', sanitize_title($item['author_slug']));
      update_post_meta($post_id, '_coparentes_author_label', sanitize_text_field($item['author_label'] ?? ''));
    }

    if (!empty($item['image'])) {
      update_post_meta($post_id, '_coparentes_cover', sanitize_file_name($item['image']));
    }

    if (!empty($item['description'])) {
      update_post_meta($post_id, '_yoast_wpseo_metadesc', sanitize_text_field($item['description']));
    }
  }

  // Permalink structure
  if (get_option('permalink_structure') === '') {
    update_option('permalink_structure', '/%postname%/');
    flush_rewrite_rules();
  }

  update_option('coparentes_seeded_v1', '1');
}

/**
 * @param array{slug:string,title:string,content:string,template?:string,excerpt?:string} $args Args.
 * @return int Page ID.
 */
function coparentes_ensure_page(array $args): int
{
  $existing = get_page_by_path($args['slug']);
  if ($existing instanceof WP_Post) {
    if (!empty($args['template'])) {
      update_post_meta($existing->ID, '_wp_page_template', $args['template']);
    }
    return (int) $existing->ID;
  }

  $page_id = wp_insert_post([
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_name' => $args['slug'],
    'post_title' => $args['title'],
    'post_content' => $args['content'],
    'post_excerpt' => $args['excerpt'] ?? '',
  ], true);

  if (is_wp_error($page_id) || !$page_id) {
    return 0;
  }

  if (!empty($args['template'])) {
    update_post_meta($page_id, '_wp_page_template', $args['template']);
  }

  return (int) $page_id;
}

/**
 * @param string $filename File under content/.
 */
function coparentes_read_content_file(string $filename): string
{
  $path = get_template_directory() . '/content/' . $filename;
  if (!is_readable($path)) {
    return '';
  }
  return (string) file_get_contents($path);
}

/**
 * Admin tool: re-run seed (Tools → Coparentes seed).
 */
add_action('admin_menu', function () {
  add_management_page(
    'Coparentes seed',
    'Coparentes seed',
    'manage_options',
    'coparentes-seed',
    function () {
      if (!current_user_can('manage_options')) {
        return;
      }
      if (isset($_POST['coparentes_reseed']) && check_admin_referer('coparentes_reseed')) {
        delete_option('coparentes_seeded_v1');
        delete_option('coparentes_landing_synced_v2');
        coparentes_seed_content();
        if (function_exists('coparentes_sync_landing_pages')) {
          coparentes_sync_landing_pages(true);
        }
        echo '<div class="updated"><p>Seed uruchomiony. Landingi zsynchronizowane do Stron (możesz je edytować).</p></div>';
      }
      if (isset($_POST['coparentes_sync_landing']) && check_admin_referer('coparentes_reseed')) {
        if (function_exists('coparentes_sync_landing_pages')) {
          coparentes_sync_landing_pages(true);
        }
        echo '<div class="updated"><p>Zsynchronizowano treści landingu do stron WP (nadpisano zawartość Start / en / de / es / fr / zh).</p></div>';
      }
      echo '<div class="wrap"><h1>Coparentes — seed treści</h1>';
      echo '<p>Przy aktywacji motywu treści z HTML są wgrywane automatycznie.</p>';
      echo '<p><strong>Edycja strony głównej:</strong> Strony → Start — tam jest cała treść landingu.</p>';
      echo '<form method="post">';
      wp_nonce_field('coparentes_reseed');
      echo '<p><button class="button button-primary" name="coparentes_reseed" value="1">Uruchom seed</button> ';
      echo '<button class="button" name="coparentes_sync_landing" value="1">Wgraj ponownie teksty landingu do stron</button></p>';
      echo '</form></div>';
    }
  );
});

/**
 * Cover image URL for a post (theme assets, exact graphics).
 */
function coparentes_post_cover_url(int $post_id): string
{
  $file = get_post_meta($post_id, '_coparentes_cover', true);
  if (!is_string($file) || $file === '') {
    return coparentes_asset('assets/blog/cover-rozwod-1.svg');
  }
  return coparentes_asset('assets/blog/' . ltrim($file, '/'));
}

/**
 * Category badge CSS modifier.
 */
function coparentes_category_badge_class(string $name): string
{
  $map = [
    'Rozwód' => 'rozwod',
    'Dzieci' => 'dzieci',
    'Ugoda mediacyjna' => 'ugoda-mediacyjna',
  ];
  $slug = $map[$name] ?? sanitize_title($name);
  return 'blog-card__badge blog-card__badge--' . $slug;
}
