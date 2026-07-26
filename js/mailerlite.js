/**
 * MailerLite universal loader for click-to-show forms (footer newsletter).
 *
 * Paste your account ID from MailerLite → Forms → Overview → JavaScript:
 *   ml('account', 'YOUR_ACCOUNT_ID');
 */
(function (w, d) {
  // Queue-safe stub so footer onclick does not throw before/without account setup.
  w.ml =
    w.ml ||
    function () {
      (w.ml.q = w.ml.q || []).push(arguments);
    };

  var ACCOUNT_ID = '2534336';
  if (!ACCOUNT_ID) {
    return;
  }

  (function (w, d, e, u, f, l, n) {
    w[f] =
      w[f] ||
      function () {
        (w[f].q = w[f].q || []).push(arguments);
      };
    l = d.createElement(e);
    l.async = 1;
    l.src = u;
    n = d.getElementsByTagName(e)[0];
    n.parentNode.insertBefore(l, n);
  })(w, d, 'script', 'https://assets.mailerlite.com/js/universal.js', 'ml');

  w.ml('account', ACCOUNT_ID);
})(window, document);
