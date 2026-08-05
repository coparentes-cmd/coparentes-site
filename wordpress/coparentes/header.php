<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php wp_head(); ?>
</head>
<?php
$lang = coparentes_current_lang();
$is_lang_landing = is_front_page() || is_page(['en', 'de', 'es', 'fr', 'zh']);
$is_front = is_front_page() && !is_home();
$is_blog = is_home() || is_singular('post') || is_page_template('page-templates/page-author.php');
$home = ($lang === 'pl') ? home_url('/') : home_url('/' . $lang . '/');
$blog = coparentes_blog_url();

$nav_features = coparentes_str('nav', 'features', 'Funkcje');
$nav_how = coparentes_str('nav', 'how-it-works', 'Jak działa?');
$nav_audience = coparentes_str('nav', 'audience', 'Dla kogo?');
$nav_testimonials = coparentes_str('nav', 'testimonials', 'Opinie');
$nav_blog = coparentes_str('nav', 'blog', 'Blog');
$nav_cta = coparentes_str('nav', 'cta', 'Pobierz aplikację');
$show_blog_nav = ($lang === 'pl'); // static non-PL landings had no Blog link

$lang_pages = [
  'pl' => ['label' => 'Polski', 'code' => 'PL', 'url' => home_url('/')],
  'en' => ['label' => 'English', 'code' => 'EN', 'url' => home_url('/en/')],
  'de' => ['label' => 'Deutsch', 'code' => 'DE', 'url' => home_url('/de/')],
  'es' => ['label' => 'Español', 'code' => 'ES', 'url' => home_url('/es/')],
  'fr' => ['label' => 'Français', 'code' => 'FR', 'url' => home_url('/fr/')],
  'zh' => ['label' => '中文', 'code' => 'ZH', 'url' => home_url('/zh/')],
];
$current = $lang_pages[$lang] ?? $lang_pages['pl'];
$hash_base = $is_lang_landing ? '' : $home;
?>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
  <header class="site-header" id="siteHeader">
    <div class="container header-inner">
      <a href="<?php echo esc_url($is_lang_landing ? '#hero' : $home . '#hero'); ?>" class="brand" aria-label="Coparentes">
        <span class="brand-mark" aria-hidden="true"><img src="<?php echo esc_url(coparentes_asset('assets/logo/header-logo-brand.svg')); ?>?v=fulllegal-logos-1" alt="" /></span>
        <span class="brand-text">Coparentes</span>
      </a>

      <nav class="main-nav" aria-label="Główna nawigacja">
        <button class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="mobileNav" aria-label="Otwórz menu">
          <span></span><span></span><span></span>
        </button>
        <div class="menu-backdrop" id="menuBackdrop"></div>
        <ul class="nav-list" id="mobileNav">
          <li><a href="<?php echo esc_url(($is_lang_landing ? '' : $home) . '#features'); ?>"><?php echo esc_html($nav_features); ?></a></li>
          <li><a href="<?php echo esc_url(($is_lang_landing ? '' : $home) . '#how-it-works'); ?>"><?php echo esc_html($nav_how); ?></a></li>
          <?php if ($show_blog_nav) : ?>
          <li>
            <a href="<?php echo esc_url($blog); ?>"<?php echo $is_blog ? ' class="is-active" aria-current="page"' : ''; ?>><?php echo esc_html($nav_blog ?: 'Blog'); ?></a>
          </li>
          <?php endif; ?>
          <?php if ($is_lang_landing || $is_front) : ?>
          <li><a href="<?php echo esc_url(($is_lang_landing ? '' : $home) . '#audience'); ?>"><?php echo esc_html($nav_audience); ?></a></li>
          <li><a href="<?php echo esc_url(($is_lang_landing ? '' : $home) . '#testimonials'); ?>"><?php echo esc_html($nav_testimonials); ?></a></li>
          <?php endif; ?>
          <li class="nav-lang-item">
            <div class="lang-switcher" id="langSwitcher">
              <button class="lang-btn" type="button" aria-label="<?php echo esc_attr($current['label']); ?>">
                <span class="lang-flag"><img src="<?php echo esc_url(coparentes_asset('assets/icons/flags/' . $lang . '.svg')); ?>" alt="" aria-hidden="true" /></span>
                <span class="lang-label"><?php echo esc_html($current['code']); ?></span>
                <img class="lang-arrow" src="<?php echo esc_url(coparentes_asset('assets/icons/chevron-down.svg')); ?>" alt="" aria-hidden="true" />
              </button>
              <div class="lang-dropdown">
                <?php foreach ($lang_pages as $code => $item) : ?>
                  <a class="lang-option<?php echo $code === $lang ? ' active' : ''; ?>" href="<?php echo esc_url($item['url']); ?>">
                    <span class="lf"><img src="<?php echo esc_url(coparentes_asset('assets/icons/flags/' . $code . '.svg')); ?>" alt="" aria-hidden="true" /></span>
                    <span class="ln"><?php echo esc_html($item['label']); ?></span>
                    <span class="lc"><?php echo esc_html($item['code']); ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          </li>
          <li class="nav-cta"><a href="<?php echo esc_url(($is_lang_landing ? '' : $home) . '#download'); ?>" class="btn btn-primary btn-small"><?php echo esc_html($nav_cta); ?></a></li>
        </ul>
      </nav>
    </div>
  </header>
