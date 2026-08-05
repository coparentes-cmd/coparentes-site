<?php
/**
 * Front page — PL landing, exact markup from index.html.
 *
 * @package Coparentes
 */

get_header();
?>
  <main>
    <?php get_template_part('template-parts/landing', 'pl'); ?>
  </main>
<?php
get_footer();
