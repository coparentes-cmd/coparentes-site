/**
 * Renders a single blog article card.
 * @param {{ title: string, excerpt: string, date: string, category: string, imageUrl: string, slug: string }} post
 * @returns {string}
 */
export function renderBlogCard(post) {
  const formattedDate = new Date(post.date).toLocaleDateString('pl-PL', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });

  const categoryClass = post.category
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/\s+/g, '-');

  return `
    <article class="blog-card reveal">
      <a class="blog-card__link" href="./${post.slug}.html">
        <div class="blog-card__media">
          <img src="${post.imageUrl}" alt="" loading="lazy" width="640" height="360" />
        </div>
        <div class="blog-card__body">
          <span class="blog-card__badge blog-card__badge--${categoryClass}">${post.category}</span>
          <h3 class="blog-card__title">${post.title}</h3>
          <p class="blog-card__excerpt">${post.excerpt}</p>
          <div class="blog-card__footer">
            <time class="blog-card__date" datetime="${post.date}">${formattedDate}</time>
            <span class="blog-card__more" aria-hidden="true">Czytaj więcej →</span>
          </div>
        </div>
      </a>
    </article>
  `;
}
