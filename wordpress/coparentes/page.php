<?php
/**
 * Default page template.
 *
 * @package Coparentes
 */

$template = get_page_template_slug();
if ($template && locate_template($template)) {
  include locate_template($template);
  return;
}

get_header();
?>
<main class="legal-page">
  <div class="legal-shell">
    <?php while (have_posts()) : the_post(); ?>
      <section class="legal-hero">
        <h1><?php the_title(); ?></h1>
      </section>
      <section class="legal-card">
        <?php the_content(); ?>
      </section>
    <?php endwhile; ?>
  </div>
</main>
<?php
get_footer();
