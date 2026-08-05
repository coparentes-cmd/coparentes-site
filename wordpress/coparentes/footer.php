<?php
/**
 * Site footer + cookie banner (markup 1:1 with static site).
 *
 * @package Coparentes
 */

$legal = coparentes_legal_urls_for_lang();
$privacy = $legal['privacy'];
$terms = $legal['terms'];
$blog = coparentes_blog_url();
$lang = coparentes_current_lang();
$home = ($lang === 'pl') ? home_url('/') : home_url('/' . $lang . '/');
$is_lang_landing = is_front_page() || is_page(['en', 'de', 'es', 'fr', 'zh']);

$f_privacy = coparentes_str('footer', 'privacy', 'Polityka prywatności');
$f_terms = coparentes_str('footer', 'terms', 'Regulamin');
$f_blog = coparentes_str('footer', 'blog', '');
$f_contact = coparentes_str('footer', 'contact', 'Kontakt');
$f_newsletter = coparentes_str('footer', 'newsletter', 'Zapisz się do newslettera');
$f_cookies = coparentes_str('footer', 'cookies', 'Ustawienia cookies');

$c_title = coparentes_str('cookie', 'title', 'Szanujemy Twoją prywatność');
$c_text = coparentes_str('cookie', 'text', 'Używamy niezbędnych plików cookies, aby strona działała prawidłowo. Za Twoją zgodą możemy włączyć także cookies funkcjonalne, analityczne i marketingowe. Szczegóły znajdziesz w Polityce prywatności i cookies oraz Regulaminie.');
$c_accept = coparentes_str('cookie', 'accept', 'Akceptuję wszystkie');
$c_reject = coparentes_str('cookie', 'reject', 'Odrzuć opcjonalne');
$c_settings = coparentes_str('cookie', 'settings', 'Ustawienia');
?>
  <footer class="site-footer" id="contact">
    <div class="container footer-top">
      <div class="footer-brand-block">
        <a href="<?php echo esc_url($is_lang_landing ? '#hero' : $home . '#hero'); ?>" class="brand footer-brand">
          <span class="brand-mark footer-mark" aria-hidden="true"><img src="<?php echo esc_url(coparentes_asset('assets/logo/footer-logo-brand.svg')); ?>?v=fulllegal-logos-1" alt="" /></span>
          <span class="brand-text brand-text-light">Coparentes</span>
        </a>
      </div>

      <div class="footer-links">
        <div>
          <a href="<?php echo esc_url($privacy); ?>"><?php echo esc_html($f_privacy); ?></a>
          <?php if ($f_terms !== '') : ?>
          <a href="<?php echo esc_url($terms); ?>"><?php echo esc_html($f_terms); ?></a>
          <?php endif; ?>
          <?php if ($lang === 'pl' && $f_blog !== '') : ?>
          <a href="<?php echo esc_url($blog); ?>"><?php echo esc_html($f_blog); ?></a>
          <?php endif; ?>
          <a href="#contact"><?php echo esc_html($f_contact); ?></a>
          <button type="button" class="ml-onclick-form footer-link-btn" onclick="ml('show', 'HbaiDg', true)"><?php echo esc_html($f_newsletter); ?></button>
          <button type="button" class="footer-link-btn" data-open-cookie-settings><?php echo esc_html($f_cookies); ?></button>
        </div>
      </div>

      <div class="footer-social">
        <a href="https://www.facebook.com/profile.php?id=61589611806475" target="_blank" rel="noopener noreferrer" aria-label="Facebook Coparentes"><img src="<?php echo esc_url(coparentes_asset('assets/icons/facebook.svg')); ?>" alt="" aria-hidden="true" /></a>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom-inner">
        <div class="pattern-row left-pattern"><img src="<?php echo esc_url(coparentes_asset('assets/illustrations/footer-pattern-left.svg')); ?>" alt="" aria-hidden="true" /></div>
        <p>© 2026 Coparentes. Wszelkie prawa zastrzeżone.</p>
        <div class="pattern-row right-pattern"><img src="<?php echo esc_url(coparentes_asset('assets/illustrations/footer-pattern-right.svg')); ?>" alt="" aria-hidden="true" /></div>
      </div>
    </div>
  </footer>

  <div class="cookie-consent" id="cookieConsent" aria-live="polite" hidden>
    <div class="cookie-consent__card" role="dialog" aria-labelledby="cookieConsentTitle">
      <div class="cookie-consent__top">
        <div class="cookie-consent__copy">
          <h3 id="cookieConsentTitle"><?php echo esc_html($c_title); ?></h3>
          <p><?php echo esc_html($c_text); ?></p>
          <div class="cookie-consent__links">
            <a href="<?php echo esc_url($privacy); ?>"><?php echo esc_html($f_privacy); ?></a>
            <?php if ($f_terms !== '') : ?>
            <a href="<?php echo esc_url($terms); ?>"><?php echo esc_html($f_terms); ?></a>
            <?php endif; ?>
          </div>
        </div>
        <div class="cookie-consent__actions">
          <button type="button" class="btn btn-small btn-primary" id="cookieAcceptAll"><?php echo esc_html($c_accept); ?></button>
          <button type="button" class="btn btn-small btn-outline-soft" id="cookieRejectOptional"><?php echo esc_html($c_reject); ?></button>
          <button type="button" class="btn btn-small btn-ghost" id="cookieOpenSettings"><?php echo esc_html($c_settings); ?></button>
        </div>
      </div>
      <div class="cookie-consent__details" id="cookieConsentDetails" hidden>
        <div class="cookie-consent__grid">
          <article class="cookie-consent__option">
            <div>
              <strong>Niezbędne</strong>
              <p>Zapewniają podstawowe działanie strony, bezpieczeństwo oraz zapis Twoich preferencji cookies.</p>
            </div>
            <span class="cookie-consent__badge">Zawsze aktywne</span>
          </article>
          <article class="cookie-consent__option">
            <div>
              <strong>Funkcjonalne</strong>
              <p>Pozwalają zapamiętać ustawienia i ułatwiają korzystanie ze strony.</p>
            </div>
            <label class="cookie-switch" aria-label="Funkcjonalne">
              <input type="checkbox" id="cookieFunctional" />
              <span class="cookie-switch__slider"></span>
            </label>
          </article>
          <article class="cookie-consent__option">
            <div>
              <strong>Analityczne</strong>
              <p>Pomagają mierzyć ruch i ulepszać stronę na podstawie statystyk odwiedzin.</p>
            </div>
            <label class="cookie-switch" aria-label="Analityczne">
              <input type="checkbox" id="cookieAnalytics" />
              <span class="cookie-switch__slider"></span>
            </label>
          </article>
          <article class="cookie-consent__option">
            <div>
              <strong>Marketingowe</strong>
              <p>Mogą służyć do dopasowania treści promocyjnych i mierzenia skuteczności kampanii.</p>
            </div>
            <label class="cookie-switch" aria-label="Marketingowe">
              <input type="checkbox" id="cookieMarketing" />
              <span class="cookie-switch__slider"></span>
            </label>
          </article>
        </div>
        <div class="cookie-consent__save">
          <button type="button" class="btn btn-small btn-primary" id="cookieSaveSelection" hidden>Zapisz wybór</button>
        </div>
      </div>
    </div>
  </div>

  <?php if (is_home() || is_singular('post') || is_page_template('page-templates/page-author.php')) : ?>
  <button
    type="button"
    class="ml-onclick-form blog-newsletter-float"
    onclick="ml('show', 'HbaiDg', true)"
    aria-label="Zapisz się do newslettera"
  >
    <span class="blog-newsletter-float__full">Zapisz się do newslettera</span>
    <span class="blog-newsletter-float__short">Newsletter</span>
  </button>
  <?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
