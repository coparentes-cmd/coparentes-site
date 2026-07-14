/* ============================================
   COPARENTES – MAIN JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {

  /* ---- NAVBAR: scroll effect ---- */
  const navbar = document.getElementById('navbar');
  const onScroll = () => {
    if (window.scrollY > 20) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---- NAVBAR: mobile toggle ---- */
  const navToggle = document.getElementById('navToggle');
  const navLinks  = document.getElementById('navLinks');

  navToggle.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    // Animate hamburger
    const spans = navToggle.querySelectorAll('span');
    const isOpen = navLinks.classList.contains('open');
    navToggle.setAttribute('aria-expanded', String(isOpen));
    if (isOpen) {
      spans[0].style.transform = 'translateY(7px) rotate(45deg)';
      spans[1].style.opacity   = '0';
      spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
    } else {
      spans[0].style.transform = '';
      spans[1].style.opacity   = '';
      spans[2].style.transform = '';
    }
  });

  // Close nav on link click
  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('open');
      navToggle.setAttribute('aria-expanded', 'false');
      const spans = navToggle.querySelectorAll('span');
      spans[0].style.transform = '';
      spans[1].style.opacity   = '';
      spans[2].style.transform = '';
    });
  });

  // Close nav on outside click
  document.addEventListener('click', (e) => {
    if (!navbar.contains(e.target)) {
      navLinks.classList.remove('open');
      navToggle.setAttribute('aria-expanded', 'false');
      const spans = navToggle.querySelectorAll('span');
      spans[0].style.transform = '';
      spans[1].style.opacity   = '';
      spans[2].style.transform = '';
    }
  });

  /* ---- INTERSECTION OBSERVER: fade-in animations ---- */
  const fadeEls = document.querySelectorAll('.fade-in, .fade-in-delay');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -40px 0px'
  });

  fadeEls.forEach(el => observer.observe(el));

  /* ---- STAGGERED fade-in for grid children ---- */
  const staggerGroups = [
    '.features-grid .feature-card',
    '.reviews-grid .review-card',
    '.preview-grid .preview-card',
    '.ai-feature-row .ai-feat',
  ];

  staggerGroups.forEach(selector => {
    const items = document.querySelectorAll(selector);
    items.forEach((item, i) => {
      item.style.transitionDelay = `${i * 0.1}s`;
    });
  });

  /* ---- SMOOTH SCROLL ---- */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (!href || href === '#') return;

      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        const navH = parseInt(getComputedStyle(document.documentElement)
          .getPropertyValue('--nav-h'));
        const top = target.getBoundingClientRect().top + window.scrollY - navH;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    });
  });

  /* ---- ACTIVE NAV LINK on scroll ---- */
  const sections = document.querySelectorAll('section[id]');
  const navAnchs = document.querySelectorAll('.nav-links a[href^="#"]');

  const sectionObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.getAttribute('id');
        navAnchs.forEach(a => {
          a.style.color = '';
          if (a.getAttribute('href') === '#' + id) {
            a.style.color = 'var(--green)';
          }
        });
      }
    });
  }, {
    threshold: 0.4,
    rootMargin: `-${navbar.offsetHeight}px 0px 0px 0px`
  });

  sections.forEach(s => sectionObserver.observe(s));

  /* ---- PHONE MOCKUP: subtle float animation (desktop only) ---- */
  const phoneCenter = document.querySelector('.phone-center');
  const phoneLeft   = document.querySelector('.phone-left');
  const phoneRight  = document.querySelector('.phone-right');
  const isMobile    = () => window.innerWidth <= 768;

  if (phoneCenter && !isMobile()) {
    let t = 0;
    let rafId;
    const animatePhones = () => {
      t += 0.015;
      if (!isMobile()) {
        if (phoneCenter) phoneCenter.style.transform = `translateX(-50%) translateY(${Math.sin(t) * 6}px)`;
        if (phoneLeft)   phoneLeft.style.transform   = `rotate(-6deg) translateY(${Math.sin(t + 1) * 5}px)`;
        if (phoneRight)  phoneRight.style.transform  = `rotate(6deg) translateY(${Math.sin(t + 2) * 5}px)`;
      } else {
        /* Na mobile zerujemy transform żeby nie kolidował z CSS */
        if (phoneCenter) phoneCenter.style.transform = '';
        if (phoneLeft)   phoneLeft.style.transform   = '';
        if (phoneRight)  phoneRight.style.transform  = '';
      }
      rafId = requestAnimationFrame(animatePhones);
    };
    animatePhones();

    /* Zatrzymaj/wznów przy resize */
    window.addEventListener('resize', () => {
      if (isMobile()) {
        if (phoneCenter) phoneCenter.style.transform = '';
      }
    }, { passive: true });
  }

  /* ---- HOME HEARTS: pulse animation ---- */
  const hearts = document.querySelectorAll('.home-heart');
  hearts.forEach((h, i) => {
    h.style.animation = `heartPulse 1.6s ease-in-out ${i * 0.3}s infinite`;
  });

  // Inject keyframes
  const styleTag = document.createElement('style');
  styleTag.textContent = `
    @keyframes heartPulse {
      0%, 100% { transform: scale(1); opacity: 0.6; }
      50%       { transform: scale(1.3); opacity: 1; }
    }
    @keyframes floatUp {
      0%, 100% { transform: translateY(0); }
      50%       { transform: translateY(-8px); }
    }
  `;
  document.head.appendChild(styleTag);

  /* ---- COUNTER ANIMATION for finance section ---- */
  function animateCounter(el, start, end, duration) {
    let startTime = null;
    const step = (timestamp) => {
      if (!startTime) startTime = timestamp;
      const progress = Math.min((timestamp - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(start + (end - start) * eased) + ' zł';
      if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }

  const balanceNum = document.querySelector('.balance-num');
  if (balanceNum) {
    const counterObs = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        animateCounter(balanceNum, 0, 200, 1200);
        counterObs.disconnect();
      }
    }, { threshold: 0.5 });
    counterObs.observe(balanceNum);
  }

  /* ---- TYPING EFFECT for hero headline ---- */
  // Subtle blinking cursor on last word
  const heroH1 = document.querySelector('.hero-text h1');
  if (heroH1) {
    heroH1.style.opacity = '0';
    heroH1.style.transition = 'opacity 0.6s ease 0.3s';
    setTimeout(() => { heroH1.style.opacity = '1'; }, 100);
  }

  /* ---- CALENDAR: interactive day hover ---- */
  document.querySelectorAll('.bc-day:not(.bc-weekday)').forEach(day => {
    day.addEventListener('mouseenter', function () {
      this.style.filter = 'brightness(0.92)';
      this.style.cursor = 'pointer';
    });
    day.addEventListener('mouseleave', function () {
      this.style.filter = '';
    });
  });

  /* ---- REVIEW CARDS: parallax subtle hover ---- */
  document.querySelectorAll('.review-card').forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = (e.clientX - rect.left) / rect.width  - 0.5;
      const y = (e.clientY - rect.top)  / rect.height - 0.5;
      card.style.transform = `translateY(-4px) rotateX(${-y * 3}deg) rotateY(${x * 3}deg)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
      card.style.transition = 'transform 0.4s ease, box-shadow 0.3s ease';
    });
  });

  /* ---- FEATURE CARDS hover icons ---- */
  document.querySelectorAll('.feature-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
      const icon = card.querySelector('.feat-icon');
      if (icon) {
        icon.style.transform = 'scale(1.1) rotate(-4deg)';
        icon.style.transition = 'transform 0.3s ease';
      }
    });
    card.addEventListener('mouseleave', () => {
      const icon = card.querySelector('.feat-icon');
      if (icon) icon.style.transform = '';
    });
  });

  /* ---- STORE BUTTONS: click ripple ---- */
  document.querySelectorAll('.store-btn, .btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        width: ${size}px;
        height: ${size}px;
        left: ${e.clientX - rect.left - size/2}px;
        top: ${e.clientY - rect.top - size/2}px;
        background: rgba(255,255,255,0.25);
        transform: scale(0);
        animation: ripple 0.5s ease-out;
        pointer-events: none;
      `;
      this.style.position = 'relative';
      this.style.overflow = 'hidden';
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 500);
    });
  });

  // Ripple keyframe
  const rippleStyle = document.createElement('style');
  rippleStyle.textContent = `
    @keyframes ripple {
      to { transform: scale(2.5); opacity: 0; }
    }
  `;
  document.head.appendChild(rippleStyle);

  /* ---- COOKIE SETTINGS FLOATING BUTTON: remove injected control ---- */
  const cookieSettingsPatterns = [
    'ustawianie cookies',
    'ustawienia cookies',
    'ustawienia plików cookie',
    'cookie settings',
    'cookie preferences',
    'cookie-einstellungen',
    'configuración de cookies',
    'paramètres des cookies',
  ];
  const cookieSettingsSelectors = [
    '#ot-sdk-btn-floating',
    '#ot-sdk-btn',
    '.ot-sdk-show-settings',
    '.cky-btn-revisit',
    '.cc-revoke',
    '.cookie-settings',
    '.cookie-preferences',
    '[aria-label*="cookie" i]',
    '[title*="cookie" i]',
  ];

  const matchesCookieSettingsText = (el) => {
    const value = [
      el.textContent,
      el.getAttribute('aria-label'),
      el.getAttribute('title'),
      el.getAttribute('id'),
      el.className,
    ].join(' ').toLowerCase();

    return cookieSettingsPatterns.some(pattern => value.includes(pattern));
  };

  const isFloatingControl = (el) => {
    const style = window.getComputedStyle(el);
    return style.position === 'fixed' || style.position === 'sticky';
  };

  const removeCookieSettingsButtons = () => {
    const candidates = [
      ...document.querySelectorAll('button, a, [role="button"], ' + cookieSettingsSelectors.join(', ')),
    ];

    candidates.forEach(el => {
      if (matchesCookieSettingsText(el) && isFloatingControl(el)) {
        el.remove();
      }
    });
  };

  removeCookieSettingsButtons();
  const cookieSettingsObserver = new MutationObserver(removeCookieSettingsButtons);
  cookieSettingsObserver.observe(document.body, { childList: true, subtree: true });

  /* ---- SCROLL PROGRESS BAR ---- */
  const progressBar = document.createElement('div');
  progressBar.style.cssText = `
    position: fixed;
    top: 0; left: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--green), var(--blue));
    z-index: 9999;
    width: 0%;
    transition: width 0.1s linear;
  `;
  document.body.appendChild(progressBar);

  window.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    const total = document.documentElement.scrollHeight - window.innerHeight;
    progressBar.style.width = `${(scrolled / total) * 100}%`;
  }, { passive: true });

  console.log('✅ coparentes loaded successfully');
});
