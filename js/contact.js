/**
 * Contact form modal — opens from footer "Kontakt" links.
 * Posts to /api/contact.php (never exposes recipient email in the UI).
 */
(function () {
  const API_URL = '/api/contact.php';

  const LEGAL = {
    pl: { terms: '/regulamin.html', privacy: '/polityka-prywatnosci.html' },
    en: { terms: '/en/terms-of-service.html', privacy: '/en/privacy-policy.html' },
    de: { terms: '/de/nutzungsbedingungen.html', privacy: '/de/datenschutz.html' },
    es: { terms: '/es/terminos-del-servicio.html', privacy: '/es/politica-de-privacidad.html' },
    fr: { terms: '/fr/conditions-dutilisation.html', privacy: '/fr/politique-de-confidentialite.html' },
    zh: { terms: '/zh/service-terms.html', privacy: '/zh/privacy-policy.html' },
  };

  const STRINGS = {
    pl: {
      title: 'Kontakt',
      name: 'Imię *',
      email: 'E-mail *',
      message: 'Wiadomość *',
      submit: 'Wyślij',
      close: 'Zamknij',
      success: 'Dziękujemy. Wiadomość została wysłana.',
      error: 'Nie udało się wysłać wiadomości.',
      requiredName: 'Podaj imię.',
      requiredEmail: 'Podaj prawidłowy adres e-mail.',
      requiredMessage: 'Napisz wiadomość.',
      requiredConsent: 'Zaznacz akceptację Regulaminu i RODO.',
      consentBefore: 'Akceptuję ',
      consentMid: ' i ',
      consentAfter: '.',
      termsLabel: 'Regulamin',
      rodoLabel: 'RODO',
    },
    en: {
      title: 'Contact',
      name: 'Name *',
      email: 'Email *',
      message: 'Message *',
      submit: 'Send',
      close: 'Close',
      success: 'Thank you. Your message has been sent.',
      error: 'Could not send the message.',
      requiredName: 'Please enter your name.',
      requiredEmail: 'Please enter a valid email.',
      requiredMessage: 'Please enter a message.',
      requiredConsent: 'Please accept the Terms and GDPR notice.',
      consentBefore: 'I accept the ',
      consentMid: ' and ',
      consentAfter: '.',
      termsLabel: 'Regulamin',
      rodoLabel: 'RODO',
    },
    de: {
      title: 'Kontakt',
      name: 'Name *',
      email: 'E-Mail *',
      message: 'Nachricht *',
      submit: 'Senden',
      close: 'Schließen',
      success: 'Danke. Deine Nachricht wurde gesendet.',
      error: 'Nachricht konnte nicht gesendet werden.',
      requiredName: 'Bitte Namen eingeben.',
      requiredEmail: 'Bitte gültige E-Mail eingeben.',
      requiredMessage: 'Bitte Nachricht eingeben.',
      requiredConsent: 'Bitte Regulamin und RODO akzeptieren.',
      consentBefore: 'Ich akzeptiere ',
      consentMid: ' und ',
      consentAfter: '.',
      termsLabel: 'Regulamin',
      rodoLabel: 'RODO',
    },
    es: {
      title: 'Contacto',
      name: 'Nombre *',
      email: 'Correo *',
      message: 'Mensaje *',
      submit: 'Enviar',
      close: 'Cerrar',
      success: 'Gracias. Tu mensaje ha sido enviado.',
      error: 'No se pudo enviar el mensaje.',
      requiredName: 'Introduce tu nombre.',
      requiredEmail: 'Introduce un correo válido.',
      requiredMessage: 'Escribe un mensaje.',
      requiredConsent: 'Acepta el Regulamin y el RODO.',
      consentBefore: 'Acepto el ',
      consentMid: ' y el ',
      consentAfter: '.',
      termsLabel: 'Regulamin',
      rodoLabel: 'RODO',
    },
    fr: {
      title: 'Contact',
      name: 'Prénom *',
      email: 'E-mail *',
      message: 'Message *',
      submit: 'Envoyer',
      close: 'Fermer',
      success: 'Merci. Votre message a été envoyé.',
      error: 'Impossible d’envoyer le message.',
      requiredName: 'Indiquez votre prénom.',
      requiredEmail: 'Indiquez un e-mail valide.',
      requiredMessage: 'Écrivez un message.',
      requiredConsent: 'Veuillez accepter le Regulamin et le RODO.',
      consentBefore: 'J’accepte le ',
      consentMid: ' et le ',
      consentAfter: '.',
      termsLabel: 'Regulamin',
      rodoLabel: 'RODO',
    },
    zh: {
      title: '联系',
      name: '姓名 *',
      email: '邮箱 *',
      message: '留言 *',
      submit: '发送',
      close: '关闭',
      success: '谢谢。您的消息已发送。',
      error: '无法发送消息。',
      requiredName: '请填写姓名。',
      requiredEmail: '请填写有效邮箱。',
      requiredMessage: '请填写留言。',
      requiredConsent: '请接受 Regulamin 与 RODO。',
      consentBefore: '我接受 ',
      consentMid: ' 和 ',
      consentAfter: '。',
      termsLabel: 'Regulamin',
      rodoLabel: 'RODO',
    },
  };

  function detectLang() {
    const htmlLang = (document.documentElement.lang || '').toLowerCase().slice(0, 2);
    if (STRINGS[htmlLang]) return htmlLang;
    const path = window.location.pathname || '';
    if (path.includes('/en/')) return 'en';
    if (path.includes('/de/')) return 'de';
    if (path.includes('/es/')) return 'es';
    if (path.includes('/fr/')) return 'fr';
    if (path.includes('/zh/')) return 'zh';
    return 'pl';
  }

  const lang = detectLang();
  const t = STRINGS[lang] || STRINGS.pl;
  const legal = LEGAL[lang] || LEGAL.pl;
  let root = null;
  let previouslyFocused = null;

  function isContactLink(el) {
    if (!(el instanceof Element)) return false;
    if (el.matches('[data-open-contact]')) return true;
    if (el.tagName === 'A') {
      const href = el.getAttribute('href') || '';
      return href === '#contact' || href.endsWith('#contact');
    }
    return false;
  }

  function syncSubmitEnabled() {
    const submitBtn = document.getElementById('contactSubmit');
    const consent = document.getElementById('contactConsent');
    if (!submitBtn || !consent) return;
    submitBtn.disabled = !consent.checked;
  }

  function ensureModal() {
    if (root) return root;

    root = document.createElement('div');
    root.className = 'contact-modal';
    root.id = 'contactModal';
    root.hidden = true;
    root.setAttribute('aria-hidden', 'true');
    root.innerHTML = `
      <div class="contact-modal__backdrop" data-contact-close tabindex="-1"></div>
      <div class="contact-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="contactModalTitle">
        <button type="button" class="contact-modal__close" data-contact-close aria-label="${t.close}">×</button>
        <h2 id="contactModalTitle" class="contact-modal__title">${t.title}</h2>
        <form class="contact-modal__form" id="contactForm" novalidate>
          <div class="contact-modal__hp" aria-hidden="true">
            <label for="contactWebsite">Website</label>
            <input type="text" id="contactWebsite" name="website" tabindex="-1" autocomplete="off" />
          </div>
          <div class="contact-modal__field">
            <label for="contactName">${t.name}</label>
            <input id="contactName" name="name" type="text" required maxlength="80" autocomplete="name" />
          </div>
          <div class="contact-modal__field">
            <label for="contactEmail">${t.email}</label>
            <input id="contactEmail" name="email" type="email" required maxlength="190" autocomplete="email" />
          </div>
          <div class="contact-modal__field">
            <label for="contactMessage">${t.message}</label>
            <textarea id="contactMessage" name="message" required maxlength="4000" rows="5"></textarea>
          </div>
          <label class="contact-modal__consent" for="contactConsent">
            <input id="contactConsent" name="consent" type="checkbox" value="1" required />
            <span>${t.consentBefore}<a href="${legal.terms}" target="_blank" rel="noopener noreferrer">${t.termsLabel}</a>${t.consentMid}<a href="${legal.privacy}" target="_blank" rel="noopener noreferrer">${t.rodoLabel}</a>${t.consentAfter}</span>
          </label>
          <div class="contact-modal__actions">
            <button class="btn btn-primary btn-small" type="submit" id="contactSubmit" disabled>${t.submit}</button>
            <p class="contact-modal__status" id="contactStatus" hidden></p>
          </div>
        </form>
      </div>
    `;

    document.body.appendChild(root);

    root.addEventListener('click', (event) => {
      const target = event.target;
      if (target instanceof Element && target.closest('[data-contact-close]')) {
        closeModal();
      }
    });

    const form = root.querySelector('#contactForm');
    const consent = root.querySelector('#contactConsent');
    if (form) {
      form.addEventListener('submit', onSubmit);
    }
    if (consent) {
      consent.addEventListener('change', syncSubmitEnabled);
    }

    return root;
  }

  function setStatus(message, type) {
    const status = document.getElementById('contactStatus');
    if (!status) return;
    status.hidden = !message;
    status.textContent = message || '';
    status.dataset.type = type || '';
  }

  function openModal() {
    const modal = ensureModal();
    previouslyFocused = document.activeElement;
    modal.hidden = false;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('contact-modal-open');
    setStatus('', '');
    syncSubmitEnabled();
    const nameInput = document.getElementById('contactName');
    if (nameInput) nameInput.focus();
  }

  function closeModal() {
    if (!root) return;
    root.hidden = true;
    root.classList.remove('is-open');
    root.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('contact-modal-open');
    if (previouslyFocused instanceof HTMLElement) {
      previouslyFocused.focus();
    }
  }

  async function onSubmit(event) {
    event.preventDefault();
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    setStatus('', '');
    const formData = new FormData(form);

    if ((formData.get('website') || '').toString().trim()) {
      setStatus(t.success, 'success');
      form.reset();
      syncSubmitEnabled();
      return;
    }

    const name = (formData.get('name') || '').toString().trim();
    const email = (formData.get('email') || '').toString().trim();
    const message = (formData.get('message') || '').toString().trim();
    const consent = formData.get('consent') === '1';

    if (!name) {
      setStatus(t.requiredName, 'error');
      return;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setStatus(t.requiredEmail, 'error');
      return;
    }
    if (!message) {
      setStatus(t.requiredMessage, 'error');
      return;
    }
    if (!consent) {
      setStatus(t.requiredConsent, 'error');
      syncSubmitEnabled();
      return;
    }

    const payload = { name, email, message, consent: true, website: '' };
    const submitBtn = document.getElementById('contactSubmit');
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
        throw new Error(data.error || t.error);
      }
      form.reset();
      setStatus(data.message || t.success, 'success');
    } catch (error) {
      setStatus(error.message || t.error, 'error');
    } finally {
      payload.email = '';
      syncSubmitEnabled();
    }
  }

  document.addEventListener('click', (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    // Allow legal links inside the modal to work normally.
    if (target.closest('.contact-modal__consent a')) return;
    const link = target.closest('a, button');
    if (!link || !isContactLink(link)) return;
    event.preventDefault();
    openModal();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && root && root.classList.contains('is-open')) {
      closeModal();
    }
  });

  if (window.location.hash === '#contact') {
    openModal();
  }
})();
