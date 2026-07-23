document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const header = document.getElementById('siteHeader');
  const menuToggle = document.getElementById('menuToggle');
  const navList = document.getElementById('mobileNav');
  const menuBackdrop = document.getElementById('menuBackdrop');
  const navMenuLinks = Array.from(document.querySelectorAll('.nav-list a'));
  const sectionNavLinks = navMenuLinks.filter(link => {
    const href = link.getAttribute('href') || '';
    return href.startsWith('#') && href !== '#';
  });
  const revealEls = document.querySelectorAll('.reveal');
  const cards3d = document.querySelectorAll('.testimonial-card');
  const langSwitcher = document.getElementById('langSwitcher');
  const langButton = langSwitcher ? langSwitcher.querySelector('.lang-btn') : null;
  const scrollState = { y: 0 };

  const setScrolled = () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 8);
  };

  const lockBodyScroll = () => {
    if (body.classList.contains('body-lock')) return;
    scrollState.y = window.scrollY || window.pageYOffset || 0;
    body.classList.add('body-lock');
    body.style.position = 'fixed';
    body.style.top = `-${scrollState.y}px`;
    body.style.left = '0';
    body.style.right = '0';
    body.style.width = '100%';
  };

  const unlockBodyScroll = (restoreScroll = true) => {
    if (!body.classList.contains('body-lock')) return;
    const previousY = scrollState.y;
    body.classList.remove('body-lock');
    body.style.position = '';
    body.style.top = '';
    body.style.left = '';
    body.style.right = '';
    body.style.width = '';
    if (restoreScroll) {
      window.scrollTo({ top: previousY, left: 0, behavior: 'auto' });
    }
  };

  const closeLangSwitcher = () => {
    if (!langSwitcher) return;
    langSwitcher.classList.remove('is-open');
  };

  const closeMenu = (restoreScroll = true) => {
    if (!menuToggle || !navList || !menuBackdrop) return;
    menuToggle.classList.remove('is-open');
    menuToggle.setAttribute('aria-expanded', 'false');
    navList.classList.remove('is-open');
    menuBackdrop.classList.remove('is-open');
    closeLangSwitcher();
    unlockBodyScroll(restoreScroll);
  };

  const isBlogLink = (href) => {
    if (!href) return false;
    const path = href.split('#')[0];
    return /(^|\/)blog\/index\.html$/.test(path) || path === './index.html';
  };

  const leavesCurrentPage = (href) => {
    if (!href || href.startsWith('#')) return false;
    try {
      const targetUrl = new URL(href, window.location.href);
      const currentPath = window.location.pathname.replace(/\/$/, '');
      const targetPath = targetUrl.pathname.replace(/\/$/, '');
      return targetPath !== currentPath || !targetUrl.hash;
    } catch (error) {
      return true;
    }
  };

  const openMenu = () => {
    if (!menuToggle || !navList || !menuBackdrop) return;
    menuToggle.classList.add('is-open');
    menuToggle.setAttribute('aria-expanded', 'true');
    navList.classList.add('is-open');
    menuBackdrop.classList.add('is-open');
    lockBodyScroll();
  };

  if (menuToggle && navList && menuBackdrop) {
    menuToggle.addEventListener('click', () => {
      const isOpen = navList.classList.contains('is-open');
      isOpen ? closeMenu() : openMenu();
    });

    menuBackdrop.addEventListener('click', closeMenu);

    navMenuLinks.forEach(link => {
      link.addEventListener('click', (event) => {
        if (window.innerWidth > 1080) return;

        const href = link.getAttribute('href') || '';

        if (isBlogLink(href)) {
          closeMenu(false);

          try {
            const targetUrl = new URL(href, window.location.href);
            const currentPath = window.location.pathname.replace(/\/$/, '');
            const targetPath = targetUrl.pathname.replace(/\/$/, '');

            if (targetPath === currentPath) {
              event.preventDefault();
              window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
            }
          } catch (error) {
            // Allow default navigation.
          }

          return;
        }

        closeMenu(!leavesCurrentPage(href));
      });
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeLangSwitcher();
        closeMenu();
      }
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth > 1080) closeMenu();
    }, { passive: true });
  }

  document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
    anchor.addEventListener('click', (event) => {
      const href = anchor.getAttribute('href');
      if (!href || href === '#') return;

      let target = null;
      try {
        target = document.querySelector(href);
      } catch (error) {
        return;
      }

      if (!target) return;
      event.preventDefault();
      const headerHeight = header ? header.offsetHeight : 0;
      const top = target.getBoundingClientRect().top + window.scrollY - headerHeight + 2;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });

  if ('IntersectionObserver' in window && revealEls.length) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.14, rootMargin: '0px 0px -30px 0px' });

    revealEls.forEach(el => revealObserver.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('is-visible'));
  }

  if ('IntersectionObserver' in window && sectionNavLinks.length) {
    const sectionObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const id = entry.target.getAttribute('id');
        sectionNavLinks.forEach(link => {
          link.classList.toggle('is-active', link.getAttribute('href') === `#${id}`);
        });
      });
    }, {
      threshold: 0.45,
      rootMargin: `-${header ? header.offsetHeight : 0}px 0px -40% 0px`
    });

    document.querySelectorAll('main section[id]').forEach(section => sectionObserver.observe(section));
  }

  if (langSwitcher && langButton) {
    langButton.addEventListener('click', (event) => {
      event.stopPropagation();
      langSwitcher.classList.toggle('is-open');
    });

    langSwitcher.addEventListener('click', (event) => {
      event.stopPropagation();
    });

    document.addEventListener('click', () => {
      closeLangSwitcher();
    });
  }

  cards3d.forEach(card => {
    card.addEventListener('mousemove', (event) => {
      if (window.innerWidth < 921) return;
      const rect = card.getBoundingClientRect();
      const x = (event.clientX - rect.left) / rect.width - 0.5;
      const y = (event.clientY - rect.top) / rect.height - 0.5;
      card.style.transform = `translateY(-4px) rotateX(${-y * 4}deg) rotateY(${x * 4}deg)`;
    });

    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });

  setScrolled();
  window.addEventListener('scroll', setScrolled, { passive: true });


  const cookieConsent = document.getElementById('cookieConsent');
  const cookieFooterTriggers = document.querySelectorAll('[data-open-cookie-settings]');

  if (cookieConsent) {
    const COOKIE_STORAGE_KEY = 'coparentes_cookie_consent_v1';
    const cookieDetails = document.getElementById('cookieConsentDetails');
    const acceptAllButton = document.getElementById('cookieAcceptAll');
    const rejectOptionalButton = document.getElementById('cookieRejectOptional');
    const settingsButton = document.getElementById('cookieOpenSettings');
    const saveSelectionButton = document.getElementById('cookieSaveSelection');
    const categoryInputs = {
      functional: document.getElementById('cookieFunctional'),
      analytics: document.getElementById('cookieAnalytics'),
      marketing: document.getElementById('cookieMarketing')
    };

    const defaultConsent = {
      necessary: true,
      functional: false,
      analytics: false,
      marketing: false,
      version: '1.0'
    };

    const normalizeConsent = (value) => ({
      necessary: true,
      functional: Boolean(value && value.functional),
      analytics: Boolean(value && value.analytics),
      marketing: Boolean(value && value.marketing),
      version: '1.0',
      savedAt: value && value.savedAt ? value.savedAt : new Date().toISOString()
    });

    const loadStoredConsent = () => {
      try {
        const raw = window.localStorage.getItem(COOKIE_STORAGE_KEY);
        if (!raw) return null;
        return normalizeConsent(JSON.parse(raw));
      } catch (error) {
        return null;
      }
    };

    const storeConsent = (value) => {
      const consent = normalizeConsent(value);
      window.localStorage.setItem(COOKIE_STORAGE_KEY, JSON.stringify(consent));
      return consent;
    };

    const applyDeferredScripts = (consent) => {
      document.querySelectorAll('script[type="text/plain"][data-cookie-category]').forEach((script) => {
        if (script.dataset.activated === 'true') return;
        const category = script.dataset.cookieCategory;
        if (!consent[category]) return;
        const nextScript = document.createElement('script');
        Array.from(script.attributes).forEach((attribute) => {
          if (attribute.name === 'type' || attribute.name === 'data-cookie-category' || attribute.name === 'data-activated') return;
          nextScript.setAttribute(attribute.name, attribute.value);
        });
        if (script.src) nextScript.src = script.src;
        if (script.textContent) nextScript.textContent = script.textContent;
        nextScript.async = script.async;
        script.dataset.activated = 'true';
        script.parentNode.insertBefore(nextScript, script.nextSibling);
      });
    };

    const applyConsentState = (consent) => {
      document.documentElement.dataset.cookieNecessary = 'granted';
      document.documentElement.dataset.cookieFunctional = consent.functional ? 'granted' : 'denied';
      document.documentElement.dataset.cookieAnalytics = consent.analytics ? 'granted' : 'denied';
      document.documentElement.dataset.cookieMarketing = consent.marketing ? 'granted' : 'denied';
      window.dispatchEvent(new CustomEvent('cookieConsentUpdated', { detail: consent }));
      applyDeferredScripts(consent);
    };

    const syncInputs = (consent) => {
      if (categoryInputs.functional) categoryInputs.functional.checked = Boolean(consent.functional);
      if (categoryInputs.analytics) categoryInputs.analytics.checked = Boolean(consent.analytics);
      if (categoryInputs.marketing) categoryInputs.marketing.checked = Boolean(consent.marketing);
    };

    const setDetailsVisibility = (expanded) => {
      cookieConsent.classList.toggle('is-expanded', expanded);
      if (cookieDetails) cookieDetails.hidden = !expanded;
      if (settingsButton) settingsButton.hidden = expanded;
      if (saveSelectionButton) saveSelectionButton.hidden = !expanded;
    };

    const showBanner = (expanded = false) => {
      cookieConsent.removeAttribute('hidden');
      cookieConsent.classList.add('is-visible');
      cookieConsent.setAttribute('aria-hidden', 'false');
      setDetailsVisibility(expanded);
    };

    const hideBanner = () => {
      cookieConsent.classList.remove('is-visible');
      cookieConsent.setAttribute('hidden', '');
      cookieConsent.setAttribute('aria-hidden', 'true');
      setDetailsVisibility(false);
    };

    const saveAndApply = (value) => {
      const consent = storeConsent(value);
      applyConsentState(consent);
      hideBanner();
    };

    const openSettings = () => {
      const storedConsent = loadStoredConsent() || defaultConsent;
      syncInputs(storedConsent);
      showBanner(true);
    };

    if (acceptAllButton) {
      acceptAllButton.addEventListener('click', () => {
        saveAndApply({ necessary: true, functional: true, analytics: true, marketing: true });
      });
    }

    if (rejectOptionalButton) {
      rejectOptionalButton.addEventListener('click', () => {
        saveAndApply({ necessary: true, functional: false, analytics: false, marketing: false });
      });
    }

    if (settingsButton) {
      settingsButton.addEventListener('click', () => {
        const storedConsent = loadStoredConsent() || defaultConsent;
        syncInputs(storedConsent);
        setDetailsVisibility(true);
      });
    }

    if (saveSelectionButton) {
      saveSelectionButton.addEventListener('click', () => {
        saveAndApply({
          necessary: true,
          functional: categoryInputs.functional ? categoryInputs.functional.checked : false,
          analytics: categoryInputs.analytics ? categoryInputs.analytics.checked : false,
          marketing: categoryInputs.marketing ? categoryInputs.marketing.checked : false
        });
      });
    }

    cookieFooterTriggers.forEach((trigger) => {
      trigger.addEventListener('click', () => {
        openSettings();
      });
    });

    const storedConsent = loadStoredConsent();
    if (storedConsent) {
      applyConsentState(storedConsent);
      hideBanner();
    } else {
      syncInputs(defaultConsent);
      showBanner(false);
    }
  }

});
