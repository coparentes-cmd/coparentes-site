<?php
/**
 * UI strings matching the static multilingual landings.
 *
 * @package Coparentes
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * @return array<string,mixed>
 */
function coparentes_i18n(): array
{
  static $cache = null;
  if ($cache !== null) {
    return $cache;
  }
  $path = get_template_directory() . '/inc/i18n-strings.json';
  $data = [];
  if (is_readable($path)) {
    $decoded = json_decode((string) file_get_contents($path), true);
    if (is_array($decoded)) {
      $data = $decoded;
    }
  }
  $lang = coparentes_current_lang();
  $cache = $data[$lang] ?? $data['pl'] ?? [];
  return $cache;
}

/**
 * @param string $group Group key.
 * @param string $key Key.
 * @param string $fallback Fallback.
 */
function coparentes_str(string $group, string $key, string $fallback = ''): string
{
  $i18n = coparentes_i18n();
  $value = $i18n[$group][$key] ?? null;
  return is_string($value) && $value !== '' ? $value : $fallback;
}

/**
 * Privacy / terms URLs for current language.
 *
 * @return array{privacy:string,terms:string}
 */
function coparentes_legal_urls_for_lang(): array
{
  $map = [
    'pl' => ['privacy' => 'polityka-prywatnosci', 'terms' => 'regulamin'],
    'en' => ['privacy' => 'privacy-policy', 'terms' => 'terms-of-service'],
    'de' => ['privacy' => 'datenschutz', 'terms' => 'nutzungsbedingungen'],
    'es' => ['privacy' => 'politica-de-privacidad', 'terms' => 'terminos-del-servicio'],
    'fr' => ['privacy' => 'politique-de-confidentialite', 'terms' => 'conditions-dutilisation'],
    'zh' => ['privacy' => 'zh-privacy-policy', 'terms' => 'zh-service-terms'],
  ];
  $lang = coparentes_current_lang();
  $slugs = $map[$lang] ?? $map['pl'];
  return [
    'privacy' => coparentes_page_url($slugs['privacy']),
    'terms' => coparentes_page_url($slugs['terms']),
  ];
}
