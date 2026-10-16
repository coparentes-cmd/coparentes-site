<?php
/**
 * Plugin Name: Coparentes Security Hardening
 * Description: Nagłówki bezpieczeństwa + ukrycie publicznych użytkowników. Nie zmienia treści ani wyglądu strony.
 * Version: 1.0.0
 * Author: Coparentes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Security headers (no content/layout impact).
 */
add_action('send_headers', static function () {
    if (headers_sent()) {
        return;
    }

    // Hide PHP version when the SAPI allows it.
    if (function_exists('header_remove')) {
        header_remove('X-Powered-By');
    }

    header('X-Content-Type-Options: nosniff', true);
    header('X-Frame-Options: SAMEORIGIN', true);
    header('Referrer-Policy: strict-origin-when-cross-origin', true);
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()', true);

    // HSTS only on HTTPS; no preload (reversible).
    $is_ssl = is_ssl()
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    if ($is_ssl) {
        header('Strict-Transport-Security: max-age=15552000; includeSubDomains', true);
    }
}, 1);

/**
 * Do not advertise WordPress version in <head>.
 */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

/**
 * Block public REST user listing / single-user lookup.
 * Authenticated users (admin, editor) keep access.
 */
add_filter('rest_endpoints', static function ($endpoints) {
    if (is_user_logged_in()) {
        return $endpoints;
    }

    foreach (array_keys($endpoints) as $route) {
        if (strpos($route, '/wp/v2/users') === 0) {
            unset($endpoints[$route]);
        }
    }

    return $endpoints;
});

/**
 * Stop ?author=N username enumeration redirects for guests.
 */
add_action('init', static function () {
    if (is_admin() || is_user_logged_in()) {
        return;
    }

    if (!isset($_GET['author'])) {
        return;
    }

    wp_safe_redirect(home_url('/'), 301);
    exit;
}, 1);

/**
 * Author archives → home (no public author URL / slug exposure).
 */
add_action('template_redirect', static function () {
    if (is_author() && !is_user_logged_in()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}, 1);
