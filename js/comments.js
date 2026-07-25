/**
 * Blog comments — public UI only.
 * Never render or store author emails in the DOM / localStorage.
 */
(function () {
  const section = document.getElementById('blogComments');
  if (!section) return;

  const slug = section.dataset.articleSlug || '';
  const listEl = document.getElementById('blogCommentsList');
  const emptyEl = document.getElementById('blogCommentsEmpty');
  const form = document.getElementById('blogCommentForm');
  const statusEl = document.getElementById('blogCommentStatus');
  const submitBtn = document.getElementById('blogCommentSubmit');

  const API_URL = '../api/comments.php';

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function formatDate(iso) {
    const date = new Date(iso.replace(' ', 'T') + 'Z');
    if (Number.isNaN(date.getTime())) {
      const local = new Date(iso.replace(' ', 'T'));
      if (Number.isNaN(local.getTime())) return iso;
      return local.toLocaleDateString('pl-PL', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
      });
    }
    return date.toLocaleDateString('pl-PL', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  }

  /** Public fields only — ignore any unexpected email if API misbehaves. */
  function renderComment(comment) {
    const name = escapeHtml(comment.author_name || 'Anonim');
    const body = escapeHtml(comment.body || '').replace(/\n/g, '<br />');
    const created = escapeHtml(formatDate(comment.created_at || ''));
    let nameHtml = `<strong class="blog-comments__author">${name}</strong>`;

    if (comment.author_url) {
      const safeUrl = escapeHtml(comment.author_url);
      nameHtml = `<a class="blog-comments__author-link" href="${safeUrl}" rel="noopener noreferrer nofollow" target="_blank">${name}</a>`;
    }

    return `
      <article class="blog-comments__item">
        <header class="blog-comments__item-meta">
          ${nameHtml}
          <time datetime="${escapeHtml(comment.created_at || '')}">${created}</time>
        </header>
        <div class="blog-comments__item-body">${body}</div>
      </article>
    `;
  }

  function setStatus(message, type) {
    if (!statusEl) return;
    statusEl.hidden = !message;
    statusEl.textContent = message || '';
    statusEl.dataset.type = type || '';
  }

  async function loadComments() {
    if (!slug || !listEl) return;

    try {
      const res = await fetch(`${API_URL}?slug=${encodeURIComponent(slug)}`, {
        headers: { Accept: 'application/json' },
      });
      const data = await res.json();
      if (!res.ok || !data.ok) {
        throw new Error(data.error || 'Nie udało się wczytać komentarzy.');
      }

      const comments = Array.isArray(data.comments) ? data.comments : [];
      if (!comments.length) {
        listEl.innerHTML = '';
        if (emptyEl) emptyEl.hidden = false;
        return;
      }

      if (emptyEl) emptyEl.hidden = true;
      listEl.innerHTML = comments.map(renderComment).join('');
    } catch (error) {
      listEl.innerHTML = '';
      if (emptyEl) {
        emptyEl.hidden = false;
        emptyEl.textContent = 'Komentarze będą dostępne po konfiguracji serwera.';
      }
    }
  }

  async function onSubmit(event) {
    event.preventDefault();
    if (!form || !slug) return;

    setStatus('', '');
    const formData = new FormData(form);

    // Honeypot
    if ((formData.get('website') || '').toString().trim()) {
      setStatus('Dziękujemy. Komentarz czeka na moderację.', 'success');
      form.reset();
      return;
    }

    const payload = {
      article_slug: slug,
      author_name: (formData.get('author_name') || '').toString().trim(),
      author_email: (formData.get('author_email') || '').toString().trim(),
      author_url: (formData.get('author_url') || '').toString().trim(),
      body: (formData.get('body') || '').toString().trim(),
      website: '',
    };

    if (submitBtn) submitBtn.disabled = true;

    try {
      const res = await fetch(API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok || !data.ok) {
        throw new Error(data.error || 'Nie udało się wysłać komentarza.');
      }

      form.reset();
      setStatus(data.message || 'Dziękujemy. Komentarz czeka na moderację.', 'success');
    } catch (error) {
      setStatus(error.message || 'Nie udało się wysłać komentarza.', 'error');
    } finally {
      // Ensure email is not retained in JS beyond this submit cycle.
      payload.author_email = '';
      if (submitBtn) submitBtn.disabled = false;
    }
  }

  if (form) {
    form.addEventListener('submit', onSubmit);
  }

  loadComments();
})();
