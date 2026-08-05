<?php
/**
 * Comments REST API shaped like the static site API (no public emails).
 *
 * @package Coparentes
 */

if (!defined('ABSPATH')) {
  exit;
}

add_action('rest_api_init', function () {
  register_rest_route('coparentes/v1', '/comments', [
    [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => 'coparentes_list_comments',
      'args' => [
        'post_id' => ['required' => true, 'type' => 'integer'],
      ],
    ],
    [
      'methods' => 'POST',
      'permission_callback' => '__return_true',
      'callback' => 'coparentes_create_comment',
    ],
  ]);
});

/**
 * @param WP_Comment $comment Comment.
 * @return array<string,mixed>
 */
function coparentes_public_comment_dto(WP_Comment $comment): array
{
  $is_staff = user_can((int) $comment->user_id, 'edit_posts');
  return [
    'id' => (int) $comment->comment_ID,
    'parent_id' => (int) $comment->comment_parent ?: null,
    'author_name' => $comment->comment_author,
    'author_url' => $is_staff ? '' : $comment->comment_author_url,
    'body' => $comment->comment_content,
    'is_staff' => $is_staff,
    'created_at' => gmdate('Y-m-d H:i:s', strtotime($comment->comment_date_gmt . ' UTC')),
  ];
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function coparentes_list_comments(WP_REST_Request $request): WP_REST_Response
{
  $post_id = (int) $request->get_param('post_id');
  if ($post_id <= 0 || get_post_status($post_id) !== 'publish') {
    return new WP_REST_Response(['ok' => false, 'error' => 'Nieprawidłowy artykuł.'], 400);
  }

  $comments = get_comments([
    'post_id' => $post_id,
    'status' => 'approve',
    'orderby' => 'comment_date_gmt',
    'order' => 'ASC',
    'number' => 200,
  ]);

  $items = array_map('coparentes_public_comment_dto', $comments);

  return new WP_REST_Response(['ok' => true, 'comments' => $items], 200);
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function coparentes_create_comment(WP_REST_Request $request): WP_REST_Response
{
  $data = $request->get_json_params();
  if (!is_array($data)) {
    $data = $request->get_params();
  }

  $honeypot = trim((string) ($data['website'] ?? ''));
  if ($honeypot !== '') {
    return new WP_REST_Response(['ok' => true, 'queued' => true], 200);
  }

  $post_id = (int) ($data['post_id'] ?? 0);
  if ($post_id <= 0 || get_post_status($post_id) !== 'publish') {
    return new WP_REST_Response(['ok' => false, 'error' => 'Nieprawidłowy artykuł.'], 400);
  }

  if (!comments_open($post_id)) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Komentarze są wyłączone.'], 400);
  }

  $author_name = trim((string) ($data['author_name'] ?? ''));
  $author_email = trim((string) ($data['author_email'] ?? ''));
  $author_url = trim((string) ($data['author_url'] ?? ''));
  $body = trim((string) ($data['body'] ?? ''));

  if ($author_name === '' || mb_strlen($author_name) > 80) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Podaj imię (max 80 znaków).'], 400);
  }
  if ($author_email === '' || !is_email($author_email)) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Podaj prawidłowy adres e-mail.'], 400);
  }
  if ($body === '' || mb_strlen($body) > 4000) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Napisz komentarz (max 4000 znaków).'], 400);
  }
  if ($author_url !== '' && !filter_var($author_url, FILTER_VALIDATE_URL)) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Nieprawidłowy adres URL.'], 400);
  }

  $comment_data = [
    'comment_post_ID' => $post_id,
    'comment_author' => $author_name,
    'comment_author_email' => $author_email,
    'comment_author_url' => $author_url,
    'comment_content' => $body,
    'comment_type' => 'comment',
    'comment_parent' => 0,
    'user_id' => 0,
    'comment_approved' => 0, // moderation — same as previous flow
  ];

  $comment_id = wp_new_comment($comment_data, true);
  if (is_wp_error($comment_id)) {
    return new WP_REST_Response(['ok' => false, 'error' => $comment_id->get_error_message()], 400);
  }

  return new WP_REST_Response([
    'ok' => true,
    'queued' => true,
    'message' => 'Komentarz został wysłany i czeka na moderację.',
  ], 200);
}
