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

  const cfg = window.CoparentesComments || {};
  const API_URL = cfg.listUrl || '/wp-json/coparentes/v1/comments';
  const postId = cfg.postId || 0;

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
  function renderComment(comment, isReply) {
    const name = escapeHtml(comment.author_name || 'Anonim');
    const body = escapeHtml(comment.body || '').replace(/\n/g, '<br />');
    const created = escapeHtml(formatDate(comment.created_at || ''));
    const isStaff = Boolean(comment.is_staff);
    let nameHtml = `<strong class="blog-comments__author">${name}</strong>`;

    if (comment.author_url && !isStaff) {
      const safeUrl = escapeHtml(comment.author_url);
      nameHtml = `<a class="blog-comments__author-link" href="${safeUrl}" rel="noopener noreferrer nofollow" target="_blank">${name}</a>`;
    }

    const badge = isStaff
      ? '<span class="blog-comments__badge">Coparentes</span>'
      : '';

    const classes = [
      'blog-comments__item',
      isReply ? 'blog-comments__item--reply' : '',
      isStaff ? 'blog-comments__item--staff' : '',
    ]
      .filter(Boolean)
      .join(' ');

    return `
      <article class="${classes}">
        <header class="blog-comments__item-meta">
          ${nameHtml}
          ${badge}
          <time datetime="${escapeHtml(comment.created_at || '')}">${created}</time>
        </header>
        <div class="blog-comments__item-body">${body}</div>
      </article>
    `;
  }

  function buildThreadHtml(comments) {
    const roots = [];
    const repliesByParent = new Map();

    comments.forEach((comment) => {
      const parentId = comment.parent_id == null ? null : Number(comment.parent_id);
      if (parentId) {
        if (!repliesByParent.has(parentId)) {
          repliesByParent.set(parentId, []);
        }
        repliesByParent.get(parentId).push(comment);
      } else {
        roots.push(comment);
      }
    });

    return roots
      .map((root) => {
        const replies = repliesByParent.get(Number(root.id)) || [];
        const repliesHtml = replies.map((reply) => renderComment(reply, true)).join('');
        return `
          <div class="blog-comments__thread">
            ${renderComment(root, false)}
            ${repliesHtml ? `<div class="blog-comments__replies">${repliesHtml}</div>` : ''}
          </div>
        `;
      })
      .join('');
  }

  function setStatus(message, type) {
    if (!statusEl) return;
    statusEl.hidden = !message;
    statusEl.textContent = message || '';
    statusEl.dataset.type = type || '';
  }

  async function loadComments() {
    if (!postId || !listEl) return;

    try {
      const res = await fetch(`${API_URL}?post_id=${encodeURIComponent(postId)}`, {
        headers: {
          Accept: 'application/json',
          ...(cfg.restNonce ? { 'X-WP-Nonce': cfg.restNonce } : {}),
        },
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
      listEl.innerHTML = buildThreadHtml(comments);
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
    if (!form || !postId) return;

    setStatus('', '');
    const formData = new FormData(form);

    // Honeypot
    if ((formData.get('website') || '').toString().trim()) {
      setStatus('Dziękujemy. Komentarz czeka na moderację.', 'success');
      form.reset();
      return;
    }

    const payload = {
      post_id: postId,
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
          ...(cfg.restNonce ? { 'X-WP-Nonce': cfg.restNonce } : {}),
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
