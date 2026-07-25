(function () {
  const blogPosts = [
    {
      slug: 'dlaczego-warto-korzystac-z-mediacji-rozwodowej',
      title: 'Dlaczego warto korzystać z mediacji rozwodowej?',
      excerpt:
        'W tym tekście pokazuję, ile realnie trwa droga sądowa w Polsce, ile możecie zaoszczędzić dzięki ugodzie i dlaczego mediacja bywa skuteczna nawet w 80% przypadków. Jeśli zastanawiacie się, czy jest dla Was sens – te liczby mogą Was zaskoczyć.',
      date: '2026-07-25',
      categories: ['Rozwód', 'Ugoda mediacyjna'],
      imageUrl: '../assets/blog/cover-rozwod-1.svg',
    },
    {
      slug: 'jak-stworzyc-dobra-ugode-pomediacyjna',
      title: 'Jak stworzyć dobrą ugodę pomediacyjną?',
      excerpt:
        'Ugoda mediacyjna to nie tylko formalność – to dokument, który realnie decyduje o Waszej codzienności po rozstaniu. Na co zwrócić uwagę, żeby ugoda była konkretna, realistyczna i faktycznie działała.',
      date: '2026-07-25',
      categories: ['Ugoda mediacyjna', 'Rozwód'],
      imageUrl: '../assets/blog/cover-mediacja-2.svg',
    },
    {
      slug: 'mediacja-okiem-mediatorki',
      title: 'Mediacja okiem mediatorki',
      excerpt:
        'Rozstanie to jeden z trudniejszych momentów w życiu, a mediacja może pomóc przejść przez niego z mniejszą ilością bólu i chaosu. Czym jest mediacja, jak znaleźć mediatora i czego spodziewać się na pierwszym spotkaniu.',
      date: '2026-07-25',
      categories: ['Ugoda mediacyjna', 'Rozwód'],
      imageUrl: '../assets/blog/cover-mediacja-1.svg',
    },
  ];

  const blogCategories = ['Wszystkie', 'Rozwód', 'Dzieci', 'Ugoda mediacyjna'];

  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }

  const scrollBlogToTop = () => {
    window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
  };

  scrollBlogToTop();
  window.addEventListener('pageshow', scrollBlogToTop);

  let activeCategory = 'Wszystkie';

  function getPostCategories(post) {
    if (Array.isArray(post.categories) && post.categories.length) {
      return post.categories;
    }
    if (typeof post.category === 'string' && post.category) {
      return [post.category];
    }
    return [];
  }

  function toCategoryClass(category) {
    return category
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/\s+/g, '-');
  }

  function formatDate(date) {
    return new Date(date).toLocaleDateString('pl-PL', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  }

  function renderBlogCard(post) {
    const categories = getPostCategories(post);
    const badges = categories
      .map(
        (category) =>
          `<span class="blog-card__badge blog-card__badge--${toCategoryClass(category)}">${category}</span>`,
      )
      .join('');

    return `
      <article class="blog-card reveal is-visible">
        <a class="blog-card__link" href="./${post.slug}.html">
          <div class="blog-card__media">
            <img src="${post.imageUrl}" alt="" loading="lazy" width="640" height="360" />
          </div>
          <div class="blog-card__body">
            <div class="blog-card__badges">${badges}</div>
            <h3 class="blog-card__title">${post.title}</h3>
            <p class="blog-card__excerpt">${post.excerpt}</p>
            <div class="blog-card__footer">
              <time class="blog-card__date" datetime="${post.date}">${formatDate(post.date)}</time>
              <span class="blog-card__more" aria-hidden="true">Czytaj więcej →</span>
            </div>
          </div>
        </a>
      </article>
    `;
  }

  function getFilteredPosts() {
    if (activeCategory === 'Wszystkie') return blogPosts;
    return blogPosts.filter((post) => getPostCategories(post).includes(activeCategory));
  }

  function renderFilters(filtersEl) {
    if (!filtersEl) return;

    filtersEl.innerHTML = blogCategories
      .map(
        (category) => `
          <button
            type="button"
            class="blog-filter${category === activeCategory ? ' is-active' : ''}"
            data-category="${category}"
          >${category}</button>
        `,
      )
      .join('');
  }

  function renderGrid(gridEl, emptyEl) {
    if (!gridEl || !emptyEl) return;

    const posts = getFilteredPosts();

    if (!posts.length) {
      gridEl.innerHTML = '';
      emptyEl.hidden = false;
      return;
    }

    emptyEl.hidden = true;
    gridEl.innerHTML = posts.map((post) => renderBlogCard(post)).join('');
  }

  function bindFilters(filtersEl, gridEl, emptyEl) {
    if (!filtersEl) return;

    filtersEl.addEventListener('click', (event) => {
      const button = event.target.closest('.blog-filter');
      if (!button) return;

      activeCategory = button.dataset.category || 'Wszystkie';
      renderFilters(filtersEl);
      renderGrid(gridEl, emptyEl);
    });
  }

  function init() {
    scrollBlogToTop();

    const gridEl = document.getElementById('blogGrid');
    const filtersEl = document.getElementById('blogFilters');
    const emptyEl = document.getElementById('blogEmpty');

    renderFilters(filtersEl);
    renderGrid(gridEl, emptyEl);
    bindFilters(filtersEl, gridEl, emptyEl);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
