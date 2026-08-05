/**
 * Blog category filters — same UX as static blog.js filters.
 */
(function () {
  const filters = document.getElementById('blogFilters');
  const grid = document.getElementById('blogGrid');
  const empty = document.getElementById('blogEmpty');
  if (!filters || !grid) return;

  const cards = Array.from(grid.querySelectorAll('.blog-card'));
  const buttons = Array.from(filters.querySelectorAll('.blog-filter'));

  function apply(category) {
    let visible = 0;
    cards.forEach((card) => {
      const cats = (card.getAttribute('data-categories') || '').split('|').filter(Boolean);
      const show = category === 'Wszystkie' || cats.includes(category);
      card.hidden = !show;
      if (show) visible += 1;
    });
    if (empty) empty.hidden = visible > 0;
  }

  filters.addEventListener('click', (event) => {
    const btn = event.target.closest('.blog-filter');
    if (!btn) return;
    buttons.forEach((b) => b.classList.toggle('is-active', b === btn));
    apply(btn.getAttribute('data-category') || 'Wszystkie');
  });
})();
