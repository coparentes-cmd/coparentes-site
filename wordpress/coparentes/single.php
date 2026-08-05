<?php
/**
 * Single blog post — markup 1:1 with blog article pages.
 *
 * @package Coparentes
 */

get_header();

while (have_posts()) :
  the_post();
  $categories = get_the_category();
  $author_slug = (string) get_post_meta(get_the_ID(), '_coparentes_author_slug', true);
  $author_label = (string) get_post_meta(get_the_ID(), '_coparentes_author_label', true);
  if ($author_label === '' && $author_slug !== '') {
    $author_label = 'Autorka ' . get_the_title(get_page_by_path($author_slug));
  }
  $author_url = $author_slug !== '' ? coparentes_page_url($author_slug) : '';
  ?>
  <main class="blog-article">
    <div class="container blog-article__shell">
      <a class="blog-article__back" href="<?php echo esc_url(coparentes_blog_url()); ?>">← Wróć do bloga</a>

      <article <?php post_class(); ?>>
        <header class="blog-article__header">
          <div class="blog-article__badges">
            <?php foreach ($categories as $cat) : ?>
              <span class="<?php echo esc_attr(coparentes_category_badge_class($cat->name)); ?>"><?php echo esc_html($cat->name); ?></span>
            <?php endforeach; ?>
          </div>
          <h1 class="blog-article__title"><?php the_title(); ?></h1>
          <p class="blog-article__meta">
            <time datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(date_i18n('j F Y', get_the_time('U'))); ?></time>
            <?php if ($author_url && $author_label) : ?>
              <span class="blog-article__meta-sep" aria-hidden="true">·</span>
              <a class="blog-article__author" href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author_label); ?></a>
            <?php endif; ?>
          </p>
        </header>

        <div class="blog-article__body">
          <?php the_content(); ?>
        </div>

        <section class="blog-comments" id="blogComments" data-article-slug="<?php echo esc_attr(get_post_field('post_name')); ?>" aria-labelledby="blogCommentsTitle">
          <h2 class="blog-comments__title" id="blogCommentsTitle">Komentarze</h2>
          <p class="blog-comments__hint">Twój adres e-mail nie zostanie opublikowany. Wymagane pola są oznaczone *</p>

          <div class="blog-comments__list" id="blogCommentsList" aria-live="polite"></div>
          <p class="blog-comments__empty" id="blogCommentsEmpty">Brak komentarzy. Bądź pierwszą osobą, która doda wpis.</p>

          <form class="blog-comments__form" id="blogCommentForm" novalidate>
            <h3 class="blog-comments__form-title">Dodaj komentarz</h3>

            <div class="blog-comments__field blog-comments__hp" aria-hidden="true">
              <label for="commentWebsiteWp">Website</label>
              <input type="text" id="commentWebsiteWp" name="website" tabindex="-1" autocomplete="off" />
            </div>

            <div class="blog-comments__field">
              <label for="commentBodyWp">Komentarz *</label>
              <textarea id="commentBodyWp" name="body" required maxlength="4000" placeholder="Napisz komentarz"></textarea>
            </div>

            <div class="blog-comments__field">
              <label for="commentNameWp">Imię *</label>
              <input id="commentNameWp" name="author_name" type="text" required maxlength="80" autocomplete="name" />
            </div>

            <div class="blog-comments__field">
              <label for="commentEmailWp">E-mail *</label>
              <input id="commentEmailWp" name="author_email" type="email" required maxlength="190" autocomplete="email" />
            </div>

            <div class="blog-comments__field">
              <label for="commentUrlWp">URL</label>
              <input id="commentUrlWp" name="author_url" type="url" maxlength="255" autocomplete="url" placeholder="https://" />
            </div>

            <div class="blog-comments__actions">
              <button class="btn btn-primary btn-small" type="submit" id="blogCommentSubmit">Dodaj komentarz</button>
              <p class="blog-comments__status" id="blogCommentStatus" hidden></p>
            </div>
          </form>
        </section>

        <div class="blog-article__footer-nav">
          <a class="btn btn-outline-soft btn-small" href="<?php echo esc_url(coparentes_blog_url()); ?>">Wszystkie artykuły</a>
          <a class="btn btn-primary btn-small" href="<?php echo esc_url(home_url('/#download')); ?>">Pobierz Coparentes</a>
        </div>
      </article>
    </div>
  </main>
  <?php
endwhile;

get_footer();
