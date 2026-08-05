<?php
/**
 * Fallback index.
 *
 * @package Coparentes
 */

if (is_front_page()) {
  get_template_part('front-page');
  return;
}

get_header();
?>
<main class="blog-page__main">
  <div class="container" style="padding: 4rem 0;">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <article <?php post_class(); ?>>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php the_excerpt(); ?>
      </article>
    <?php endwhile; else : ?>
      <p><?php esc_html_e('Brak treści.', 'coparentes'); ?></p>
    <?php endif; ?>
  </div>
</main>
<?php
get_footer();
