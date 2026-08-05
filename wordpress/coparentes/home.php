<?php
/**
 * Blog index — layout 1:1 with blog/index.html.
 *
 * @package Coparentes
 */

get_header();
?>
  <main class="blog-page__main" id="blogPage">
    <header class="blog-hero reveal is-visible">
      <div class="blog-hero__art-frame" aria-hidden="true">
        <img
          class="blog-hero__art"
          src="<?php echo esc_url(coparentes_asset('assets/blog/blog-hero-bg.png')); ?>?v=blog-hero-wide-1"
          alt=""
          width="1920"
          height="817"
          decoding="async"
          fetchpriority="high"
        />
      </div>
      <div class="blog-hero__inner">
        <h1 class="blog-hero__subtitle">Spokojne wskazówki o rozwodzie, mediacji i wspólnym rodzicielstwie — bez oceniania, z myślą o dziecku i o Waszym dobrostanie.</h1>
      </div>
    </header>

    <div class="container blog-page__content">
      <div class="blog-filters" id="blogFilters" role="toolbar" aria-label="Filtruj artykuły po kategorii">
        <button type="button" class="blog-filter is-active" data-category="Wszystkie">Wszystkie</button>
        <button type="button" class="blog-filter" data-category="Rozwód">Rozwód</button>
        <button type="button" class="blog-filter" data-category="Dzieci">Dzieci</button>
        <button type="button" class="blog-filter" data-category="Ugoda mediacyjna">Ugoda mediacyjna</button>
      </div>

      <div class="blog-grid" id="blogGrid" aria-live="polite">
        <?php if (have_posts()) : ?>
          <?php while (have_posts()) : the_post(); ?>
            <?php
            $categories = get_the_category();
            $cat_names = array_map(static function ($c) {
              return $c->name;
            }, $categories ?: []);
            $data_cats = esc_attr(implode('|', $cat_names));
            ?>
            <article class="blog-card reveal is-visible" data-categories="<?php echo $data_cats; ?>">
              <a class="blog-card__link" href="<?php the_permalink(); ?>">
                <div class="blog-card__media">
                  <img src="<?php echo esc_url(coparentes_post_cover_url(get_the_ID())); ?>" alt="" loading="lazy" width="640" height="360" />
                </div>
                <div class="blog-card__body">
                  <div class="blog-card__badges">
                    <?php foreach ($cat_names as $cat_name) : ?>
                      <span class="<?php echo esc_attr(coparentes_category_badge_class($cat_name)); ?>"><?php echo esc_html($cat_name); ?></span>
                    <?php endforeach; ?>
                  </div>
                  <h3 class="blog-card__title"><?php the_title(); ?></h3>
                  <p class="blog-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                  <div class="blog-card__footer">
                    <time class="blog-card__date" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(date_i18n('j F Y', get_the_time('U'))); ?></time>
                    <span class="blog-card__more" aria-hidden="true">Czytaj więcej →</span>
                  </div>
                </div>
              </a>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>

      <p class="blog-empty" id="blogEmpty" hidden>Brak artykułów w tej kategorii. Wybierz inną zakładkę powyżej.</p>
    </div>
  </main>
<?php
get_footer();
