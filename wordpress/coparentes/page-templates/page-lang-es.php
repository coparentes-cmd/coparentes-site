<?php
/**
 * Template Name: Landing es
 * Template Post Type: page
 *
 * @package Coparentes
 */

get_header();
?>
  <main>
    <?php
    while (have_posts()) :
      the_post();
      the_content();
    endwhile;
    ?>
  </main>
<?php
get_footer();
