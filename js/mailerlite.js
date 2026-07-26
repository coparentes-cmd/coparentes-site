/**
 * MailerLite universal loader for click-to-show forms.
 * Account ID comes from MailerLite → Forms → Overview → JavaScript snippet:
 *   ml('account', 'YOUR_ACCOUNT_ID');
 */
(function (w, d) {
  var ACCOUNT_ID = 'REPLACE_MAILERLITE_ACCOUNT_ID';
  if (!ACCOUNT_ID || ACCOUNT_ID.indexOf('REPLACE_') === 0) {
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
