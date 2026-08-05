<?php
/**
 * Template Name: Legal page
 * Template Post Type: page
 *
 * @package Coparentes
 */

get_header();
?>
<main class="legal-page">
  <div class="legal-shell">
    <?php
    while (have_posts()) :
      the_post();
      the_content();
    endwhile;
    ?>
  </div>
</main>
<?php
get_footer();
