import { blogPosts, blogCategories } from './blogPosts.js';
import { renderBlogCard } from './BlogCard.js';

const gridEl = document.getElementById('blogGrid');
const filtersEl = document.getElementById('blogFilters');
const emptyEl = document.getElementById('blogEmpty');

let activeCategory = 'Wszystkie';

function getFilteredPosts() {
  if (activeCategory === 'Wszystkie') return blogPosts;
  return blogPosts.filter((post) => post.category === activeCategory);
}

function renderFilters() {
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

function renderGrid() {
  if (!gridEl || !emptyEl) return;

  const posts = getFilteredPosts();

  if (!posts.length) {
    gridEl.innerHTML = '';
    emptyEl.hidden = false;
    return;
  }

  emptyEl.hidden = true;
  gridEl.innerHTML = posts.map((post) => renderBlogCard(post)).join('');

  gridEl.querySelectorAll('.reveal').forEach((el, index) => {
    el.classList.add(index % 3 === 1 ? 'delay-1' : index % 3 === 2 ? 'delay-2' : '');
    el.classList.add('is-visible');
  });
}

function bindFilters() {
  if (!filtersEl) return;

  filtersEl.addEventListener('click', (event) => {
    const button = event.target.closest('.blog-filter');
    if (!button) return;

    activeCategory = button.dataset.category || 'Wszystkie';
    renderFilters();
    renderGrid();
  });
}

document.addEventListener('DOMContentLoaded', () => {
  renderFilters();
  renderGrid();
  bindFilters();
});
