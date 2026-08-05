<?php
/**
 * Enqueue CSS/JS identical to the static site.
 *
 * @package Coparentes
 */

if (!defined('ABSPATH')) {
  exit;
}

add_action('wp_enqueue_scripts', function () {
  $uri = get_template_directory_uri();
  $ver = COPARENTES_THEME_VERSION;

  wp_enqueue_style(
    'coparentes-fonts',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
    [],
    null
  );

  if (coparentes_current_lang() === 'zh') {
    wp_enqueue_style(
      'coparentes-fonts-zh',
      'https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;600;700;800&display=swap',
      ['coparentes-fonts'],
      null
    );
  }

  wp_enqueue_style('coparentes-style', $uri . '/css/style.css', ['coparentes-fonts'], $ver . '-newsletter-btn-1');
  wp_enqueue_style('coparentes-mobile', $uri . '/css/mobile.css', ['coparentes-style'], $ver . '-footer-center-1');
  wp_enqueue_style('coparentes-lang', $uri . '/css/lang.css', ['coparentes-style'], $ver);

  if (is_home() || is_singular('post') || is_page_template('page-templates/page-author.php')) {
    wp_enqueue_style('coparentes-blog', $uri . '/css/blog.css', ['coparentes-style'], $ver . '-newsletter-float-1');
  }

  if (is_page_template('page-templates/page-legal.php') || is_page(['polityka-prywatnosci', 'regulamin'])) {
    wp_enqueue_style('coparentes-editorial', $uri . '/css/editorial.css', ['coparentes-style'], $ver);
  }

  wp_enqueue_script('coparentes-main', $uri . '/js/main.js', [], $ver . '-blog-scroll-1', true);
  wp_enqueue_script('coparentes-contact', $uri . '/js/contact-wp.js', [], $ver . '-contact-wp-1', true);

  $legal = coparentes_legal_urls_for_lang();

  wp_localize_script('coparentes-contact', 'CoparentesWP', [
    'contactUrl' => rest_url('coparentes/v1/contact'),
    'restNonce' => wp_create_nonce('wp_rest'),
    'lang' => coparentes_current_lang(),
    'legal' => [
      'terms' => $legal['terms'],
      'privacy' => $legal['privacy'],
    ],
    'homeUrl' => home_url('/'),
    'blogUrl' => coparentes_blog_url(),
  ]);

  if (is_home()) {
    wp_enqueue_script('coparentes-blog-filters', $uri . '/js/blog-filters-wp.js', [], $ver, true);
  }

  if (is_singular('post')) {
    wp_enqueue_script('coparentes-comments', $uri . '/js/comments-wp.js', [], $ver . '-comments-wp-1', true);
    wp_localize_script('coparentes-comments', 'CoparentesComments', [
      'listUrl' => rest_url('coparentes/v1/comments'),
      'createUrl' => rest_url('coparentes/v1/comments'),
      'restNonce' => wp_create_nonce('wp_rest'),
      'postId' => get_queried_object_id(),
    ]);
  }
});

/**
 * Favicons + MailerLite in head (same as static).
 */
add_action('wp_head', function () {
  $uri = get_template_directory_uri();
  echo '<link rel="icon" href="' . esc_url($uri . '/assets/favicon.svg') . '" type="image/svg+xml" />' . "\n";
  echo '<link rel="icon" href="' . esc_url($uri . '/assets/favicon.png') . '" type="image/png" sizes="32x32" />' . "\n";
  echo '<link rel="apple-touch-icon" href="' . esc_url($uri . '/assets/apple-touch-icon.png') . '" />' . "\n";
  ?>
  <!-- MailerLite Universal -->
  <script>
    (function(w,d,e,u,f,l,n){w[f]=w[f]||function(){(w[f].q=w[f].q||[])
    .push(arguments);},l=d.createElement(e),l.async=1,l.src=u,
    n=d.getElementsByTagName(e)[0],n.parentNode.insertBefore(l,n);})
    (window,document,'script','https://assets.mailerlite.com/js/universal.js','ml');
    ml('account', '2534336');
  </script>
  <!-- End MailerLite Universal -->
  <?php
}, 1);
