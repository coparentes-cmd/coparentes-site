<?php
/**
 * Plugin Name: Coparentes Nav CTA — Wypróbuj
 * Description: Zamienia przycisk nawigacji „Pobierz aplikację” na „Wypróbuj” z linkiem do https://getcoparentes.app. Nie zmienia layoutu.
 * Version: 1.0.1
 * Author: Coparentes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Rewrite the header CTA link/label in rendered HTML (PL pages).
 *
 * @param string $html Full page HTML.
 * @return string
 */
function coparentes_nav_cta_rewrite($html)
{
    if (!is_string($html) || $html === '') {
        return $html;
    }

    $replacement = '<li class="nav-cta"><a href="https://getcoparentes.app" class="btn btn-primary btn-small">Wypróbuj</a></li>';

    // Use ~ delimiter so "#" inside #download does not break the pattern.
    $updated = preg_replace(
        '~<li class="nav-cta"><a href="(?:#download|https://coparentes\.ai/#download)" class="btn btn-primary btn-small">Pobierz aplikację</a></li>~u',
        $replacement,
        $html
    );

    // Never return null (preg error) — that would blank the page.
    return is_string($updated) ? $updated : $html;
}

add_action('template_redirect', static function () {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }

    ob_start('coparentes_nav_cta_rewrite');
}, 0);
