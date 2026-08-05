<?php
/**
 * Template Name: Author bio
 * Template Post Type: page
 *
 * @package Coparentes
 */

get_header();

while (have_posts()) :
  the_post();
  ?>
  <main class="blog-article">
    <div class="container blog-article__shell">
      <a class="blog-article__back" href="<?php echo esc_url(coparentes_blog_url()); ?>">← Wróć do bloga</a>

      <article class="blog-author">
        <header class="blog-article__header">
          <p class="blog-author__eyebrow">Autorka</p>
          <h1 class="blog-article__title"><?php the_title(); ?></h1>
        </header>

        <div class="blog-article__body blog-author__body">
          <?php the_content(); ?>
        </div>

        <div class="blog-article__footer-nav">
          <a class="btn btn-outline-soft btn-small" href="<?php echo esc_url(coparentes_blog_url()); ?>">Wszystkie artykuły</a>
          <a class="btn btn-primary btn-small" href="<?php echo esc_url(home_url('/#download')); ?>">Pobierz Coparentes</a>
        </div>
      </article>
    </div>
  </main>
  <?php
endwhile;

get_footer();
