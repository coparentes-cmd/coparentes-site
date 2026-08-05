<?php
/**
 * Front page — content editable in WP: Strony → Start.
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
