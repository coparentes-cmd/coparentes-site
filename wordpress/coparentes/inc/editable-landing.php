<?php
/**
 * Editable landing pages in WordPress admin (page content = full landing HTML).
 *
 * @package Coparentes
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Replace {{THEME_URI}} in content with the theme directory URL.
 */
function coparentes_expand_theme_uri(string $content): string
{
  return str_replace('{{THEME_URI}}', esc_url(get_template_directory_uri()), $content);
}

add_filter('the_content', function ($content) {
  if (!is_string($content) || strpos($content, '{{THEME_URI}}') === false) {
    return $content;
  }
  return coparentes_expand_theme_uri($content);
}, 8);

/**
 * Landing page IDs: front page + language landings.
 *
 * @return int[]
 */
function coparentes_landing_page_ids(): array
{
  $ids = [];
  $front = (int) get_option('page_on_front');
  if ($front > 0) {
    $ids[] = $front;
  }
  foreach (['en', 'de', 'es', 'fr', 'zh'] as $slug) {
    $page = get_page_by_path($slug);
    if ($page instanceof WP_Post) {
      $ids[] = (int) $page->ID;
    }
  }
  return array_values(array_unique(array_filter($ids)));
}

/**
 * Use classic editor for landing pages (easier whole-page HTML edit).
 */
add_filter('use_block_editor_for_post', function ($use, $post) {
  if (!$post instanceof WP_Post || $post->post_type !== 'page') {
    return $use;
  }
  if (in_array((int) $post->ID, coparentes_landing_page_ids(), true)) {
    return false;
  }
  $template = get_page_template_slug($post);
  if (is_string($template) && strpos($template, 'page-templates/page-lang-') === 0) {
    return false;
  }
  return $use;
}, 10, 2);

/**
 * Don't auto-wrap landing HTML with <p> tags.
 */
add_action('wp', function () {
  if (is_front_page() || is_page(['en', 'de', 'es', 'fr', 'zh'])) {
    remove_filter('the_content', 'wpautop');
  }
});

/**
 * Admin notice: how to edit the homepage.
 */
add_action('admin_notices', function () {
  if (!current_user_can('edit_pages')) {
    return;
  }
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if (!$screen || !in_array($screen->id, ['dashboard', 'edit-page', 'page'], true)) {
    return;
  }
  $front_id = (int) get_option('page_on_front');
  $edit = $front_id ? get_edit_post_link($front_id, 'raw') : admin_url('edit.php?post_type=page');
  echo '<div class="notice notice-info"><p>';
  echo '<strong>Coparentes:</strong> Teksty strony głównej edytujesz tu: ';
  echo '<a href="' . esc_url($edit ?: admin_url('edit.php?post_type=page')) . '"><strong>Strony → Start (strona główna)</strong></a>. ';
  echo 'Zmień tekst → Zapisz. Nie usuwaj klas CSS ani <code>{{THEME_URI}}</code> przy obrazkach.';
  echo '</p></div>';
});

/**
 * Meta box tip on landing edit screen.
 */
add_action('add_meta_boxes', function ($post_type, $post) {
  if ($post_type !== 'page' || !$post instanceof WP_Post) {
    return;
  }
  if (!in_array((int) $post->ID, coparentes_landing_page_ids(), true)) {
    return;
  }
  add_meta_box(
    'coparentes_landing_help',
    'Jak edytować landing Coparentes',
    function () {
      echo '<p>Tu jest <strong>cała treść landingu</strong> (HTML). Możesz zmieniać napisy, nagłówki, opisy.</p>';
      echo '<ul style="list-style:disc;margin-left:1.2em;">';
      echo '<li>Zmieniaj tylko tekst między znacznikami (np. w <code>&lt;h1&gt;…&lt;/h1&gt;</code>).</li>';
      echo '<li>Nie usuwaj klas typu <code>class="hero"</code> — od nich zależy wygląd.</li>';
      echo '<li>Ścieżki obrazków zostaw z <code>{{THEME_URI}}/assets/...</code>.</li>';
      echo '<li>Po zapisie odśwież stronę na froncie (Ctrl/Cmd+Shift+R).</li>';
      echo '</ul>';
    },
    'page',
    'side',
    'high'
  );
}, 10, 2);

/**
 * Push landing HTML into WP pages (force update content).
 */
function coparentes_sync_landing_pages(bool $force = true): void
{
  $map = [
    'start' => 'landing-pl.html',
    'en' => 'landing-en.html',
    'de' => 'landing-de.html',
    'es' => 'landing-es.html',
    'fr' => 'landing-fr.html',
    'zh' => 'landing-zh.html',
  ];

  $titles = [
    'start' => 'Coparentes — spokojne rodzicielstwo po rozstaniu',
    'en' => 'Coparentes — calm co-parenting after separation',
    'de' => 'Coparentes — ruhige Elternschaft nach der Trennung',
    'es' => 'Coparentes — crianza compartida tranquila tras la separación',
    'fr' => 'Coparentes — co-parentalité sereine après la séparation',
    'zh' => 'Coparentes — 分居后的平静共同育儿',
  ];

  foreach ($map as $slug => $file) {
    $html = coparentes_read_content_file($file);
    if ($html === '') {
      continue;
    }

    $page = null;
    $template = ($slug === 'start') ? '' : 'page-templates/page-lang-' . $slug . '.php';

    if ($slug === 'start') {
      $front_id = (int) get_option('page_on_front');
      if ($front_id > 0) {
        $page = get_post($front_id);
      }
      if (!$page instanceof WP_Post) {
        $page = get_page_by_path('start');
      }
      // Hostinger sometimes creates "Sample Page" / "Home"
      if (!$page instanceof WP_Post) {
        foreach (['home', 'strona-glowna', 'glowna'] as $alt) {
          $page = get_page_by_path($alt);
          if ($page instanceof WP_Post) {
            break;
          }
        }
      }
    } else {
      $page = get_page_by_path($slug);
    }

    if ($page instanceof WP_Post) {
      if ($force || trim((string) $page->post_content) === '') {
        wp_update_post([
          'ID' => $page->ID,
          'post_content' => $html,
          'post_title' => $titles[$slug] ?? $page->post_title,
        ]);
      }
      if ($template !== '') {
        update_post_meta($page->ID, '_wp_page_template', $template);
      }
      if ($slug === 'start') {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page->ID);
      }
      continue;
    }

    $new_id = coparentes_ensure_page([
      'slug' => $slug,
      'title' => $titles[$slug] ?? $slug,
      'content' => $html,
      'template' => $template,
    ]);
    if ($slug === 'start' && $new_id > 0) {
      update_option('show_on_front', 'page');
      update_option('page_on_front', $new_id);
    }
  }

  $blog = get_page_by_path('blog');
  if ($blog instanceof WP_Post) {
    update_option('page_for_posts', $blog->ID);
  }

  update_option('coparentes_landing_synced_v2', '1');
  update_option('coparentes_landing_synced_v111', '1');
}

/**
 * Admin bar shortcut: edit homepage.
 */
add_action('admin_bar_menu', function ($bar) {
  if (!current_user_can('edit_pages') || !is_admin_bar_showing()) {
    return;
  }
  $front_id = (int) get_option('page_on_front');
  if ($front_id <= 0) {
    return;
  }
  $bar->add_node([
    'id' => 'coparentes-edit-landing',
    'title' => 'Edytuj stronę główną',
    'href' => get_edit_post_link($front_id, 'raw'),
    'meta' => ['title' => 'Edytuj teksty landingu Coparentes'],
  ]);
}, 80);

/**
 * Dashboard widget with clear next steps.
 */
add_action('wp_dashboard_setup', function () {
  wp_add_dashboard_widget('coparentes_how_to_edit', 'Coparentes — jak edytować stronę', function () {
    $front_id = (int) get_option('page_on_front');
    $edit = $front_id ? get_edit_post_link($front_id, 'raw') : admin_url('edit.php?post_type=page');
    echo '<ol style="margin-left:1.2em;">';
    echo '<li><a href="' . esc_url($edit) . '"><strong>Edytuj stronę główną</strong></a> — zmień teksty → Aktualizuj</li>';
    echo '<li><a href="' . esc_url(admin_url('edit.php')) . '">Wpisy</a> — blog</li>';
    echo '<li><a href="' . esc_url(admin_url('edit.php?post_type=page')) . '">Strony</a> — polityka, regulamin, języki</li>';
    echo '<li><a href="' . esc_url(admin_url('tools.php?page=coparentes-seed')) . '">Narzędzia → Coparentes seed</a> — przywróć teksty landingu</li>';
    echo '</ol>';
    echo '<p>Po zapisie odśwież <a href="' . esc_url(home_url('/')) . '" target="_blank" rel="noopener">coparentes.ai</a>.</p>';
  });
});
