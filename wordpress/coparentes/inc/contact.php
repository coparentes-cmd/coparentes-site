<?php
/**
 * Contact form REST endpoint (replaces api/contact.php).
 *
 * @package Coparentes
 */

if (!defined('ABSPATH')) {
  exit;
}

add_action('rest_api_init', function () {
  register_rest_route('coparentes/v1', '/contact', [
    'methods' => 'POST',
    'permission_callback' => '__return_true',
    'callback' => 'coparentes_handle_contact',
  ]);
});

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function coparentes_handle_contact(WP_REST_Request $request): WP_REST_Response
{
  $data = $request->get_json_params();
  if (!is_array($data)) {
    $data = $request->get_params();
  }

  // Honeypot
  $honeypot = trim((string) ($data['website'] ?? $data['company'] ?? ''));
  if ($honeypot !== '') {
    return new WP_REST_Response(['ok' => true, 'sent' => true, 'message' => 'Dziękujemy. Wiadomość została wysłana.'], 200);
  }

  $name = trim((string) ($data['name'] ?? ''));
  $email = trim((string) ($data['email'] ?? ''));
  $message = trim((string) ($data['message'] ?? ''));
  $consent = $data['consent'] ?? false;

  if ($name === '' || mb_strlen($name) > 80) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Podaj imię (max 80 znaków).'], 400);
  }
  if ($email === '' || !is_email($email) || strlen($email) > 190) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Podaj prawidłowy adres e-mail.'], 400);
  }
  if ($message === '' || mb_strlen($message) > 4000) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Napisz wiadomość (max 4000 znaków).'], 400);
  }
  if (!($consent === true || $consent === 1 || $consent === '1')) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Wymagana akceptacja Regulaminu i RODO.'], 400);
  }

  $to = get_option('coparentes_contact_to_email', get_option('admin_email'));
  $to = is_email($to) ? $to : get_option('admin_email');

  $subject = sprintf('[Coparentes] Wiadomość od %s', $name);
  $body = "Imię: {$name}\nE-mail: {$email}\n\nWiadomość:\n{$message}\n";
  $headers = [
    'Content-Type: text/plain; charset=UTF-8',
    'Reply-To: ' . $name . ' <' . $email . '>',
  ];

  $sent = wp_mail($to, $subject, $body, $headers);
  if (!$sent) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Nie udało się wysłać wiadomości.'], 500);
  }

  return new WP_REST_Response([
    'ok' => true,
    'sent' => true,
    'message' => 'Dziękujemy. Wiadomość została wysłana.',
  ], 200);
}

/**
 * Settings: contact recipient email.
 */
add_action('admin_init', function () {
  register_setting('general', 'coparentes_contact_to_email', [
    'type' => 'string',
    'sanitize_callback' => 'sanitize_email',
    'default' => '',
  ]);

  add_settings_field(
    'coparentes_contact_to_email',
    __('Coparentes — e-mail kontaktowy', 'coparentes'),
    function () {
      $value = get_option('coparentes_contact_to_email', '');
      echo '<input type="email" name="coparentes_contact_to_email" value="' . esc_attr((string) $value) . '" class="regular-text" placeholder="kontakt@coparentes.ai" />';
      echo '<p class="description">' . esc_html__('Na ten adres trafiają wiadomości z formularza Kontakt. Puste = e-mail administratora.', 'coparentes') . '</p>';
    },
    'general'
  );
});
