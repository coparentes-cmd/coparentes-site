<?php
/**
 * Theme supports and helpers.
 *
 * @package Coparentes
 */

if (!defined('ABSPATH')) {
  exit;
}

add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', [
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
    'style',
    'script',
  ]);

  register_nav_menus([
    'primary' => __('Menu główne', 'coparentes'),
  ]);
});

/**
 * Theme asset URL helper.
 */
function coparentes_asset(string $relative): string
{
  return get_template_directory_uri() . '/' . ltrim($relative, '/');
}

/**
 * Home URL with optional hash (landing sections).
 */
function coparentes_home_hash(string $hash = ''): string
{
  $url = home_url('/');
  if ($hash !== '') {
    $url .= '#' . ltrim($hash, '#');
  }
  return $url;
}

/**
 * Resolve a seeded page by slug, fallback to path.
 */
function coparentes_page_url(string $slug): string
{
  $page = get_page_by_path($slug);
  if ($page instanceof WP_Post) {
    return get_permalink($page);
  }
  return home_url('/' . trim($slug, '/') . '/');
}

/**
 * Blog posts index URL.
 */
function coparentes_blog_url(): string
{
  $posts_page_id = (int) get_option('page_for_posts');
  if ($posts_page_id > 0) {
    return get_permalink($posts_page_id) ?: home_url('/blog/');
  }
  return home_url('/blog/');
}

/**
 * Current UI language code for switcher / contact strings.
 */
function coparentes_current_lang(): string
{
  if (is_page('en') || is_page_template('page-templates/page-lang-en.php')) {
    return 'en';
  }
  if (is_page('de') || is_page_template('page-templates/page-lang-de.php')) {
    return 'de';
  }
  if (is_page('es') || is_page_template('page-templates/page-lang-es.php')) {
    return 'es';
  }
  if (is_page('fr') || is_page_template('page-templates/page-lang-fr.php')) {
    return 'fr';
  }
  if (is_page('zh') || is_page_template('page-templates/page-lang-zh.php')) {
    return 'zh';
  }
  return 'pl';
}

/**
 * Body classes matching static site.
 */
add_filter('body_class', function (array $classes): array {
  if (is_front_page()) {
    $classes[] = 'landing-page';
  }
  if (is_home()) {
    $classes[] = 'blog-page';
  }
  if (is_singular('post')) {
    $classes[] = 'blog-article-page';
  }
  if (is_page() && get_page_template_slug() === 'page-templates/page-author.php') {
    $classes[] = 'blog-article-page';
  }
  if (is_page(['polityka-prywatnosci', 'regulamin'])) {
    $classes[] = 'legal-page-body';
  }
  return $classes;
});

/**
 * Pretty permalinks reminder is in README — flush on switch.
 */
add_action('after_switch_theme', function () {
  flush_rewrite_rules();
  if (function_exists('coparentes_seed_content')) {
    coparentes_seed_content();
  }
});
