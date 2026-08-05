<?php
/**
 * Coparentes theme — 1:1 port of the static site.
 *
 * @package Coparentes
 */

if (!defined('ABSPATH')) {
  exit;
}

define('COPARENTES_THEME_VERSION', '1.1.0');

require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/i18n.php';
require_once get_template_directory() . '/inc/assets.php';
require_once get_template_directory() . '/inc/contact.php';
require_once get_template_directory() . '/inc/comments-api.php';
require_once get_template_directory() . '/inc/seed-content.php';
require_once get_template_directory() . '/inc/editable-landing.php';
