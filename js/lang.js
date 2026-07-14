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
  });

  document.addEventListener('click', () => {
    switcher.classList.remove('open');
  });

  dropdown.addEventListener('click', (e) => e.stopPropagation());
});
