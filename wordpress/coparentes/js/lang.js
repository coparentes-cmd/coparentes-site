/* ============================================
   LANGUAGE SWITCHER – coparentes
   ============================================ */
document.addEventListener('DOMContentLoaded', () => {
  const switcher = document.getElementById('langSwitcher');
  if (!switcher) return;

  const btn      = switcher.querySelector('.lang-btn');
  const dropdown = switcher.querySelector('.lang-dropdown');

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    switcher.classList.toggle('open');
    btn.setAttribute('aria-expanded', String(switcher.classList.contains('open')));
  });

  document.addEventListener('click', () => {
    switcher.classList.remove('open');
    btn.setAttribute('aria-expanded', 'false');
  });

  dropdown.addEventListener('click', (e) => e.stopPropagation());
});
