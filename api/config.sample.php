<?php
/**
 * Sample config — copy to config.php on the server and fill in real values.
 * config.php must NOT be committed to git.
 */
return [
  'db_host' => 'localhost',
  'db_name' => 'YOUR_DB_NAME',
  'db_user' => 'YOUR_DB_USER',
  'db_pass' => 'YOUR_DB_PASSWORD',
  'db_charset' => 'utf8mb4',

  // Password for /api/admin.php
  'admin_password' => 'CHANGE_ME_STRONG_PASSWORD',

  // Staff replies from admin panel (email stays private — never in public API)
  'staff_display_name' => 'Coparentes',
  'staff_email' => 'noreply@coparentes.ai',

  // Contact form — inbox for messages from the site modal
  'contact_to_email' => 'kontakt@coparentes.ai',
  'contact_from_email' => 'kontakt@coparentes.ai',

  // Optional salt for IP hashing (set a long random string)
  'ip_hash_salt' => 'CHANGE_ME_RANDOM_SALT',

  // Allowed article slugs (reject unknown targets)
  'allowed_slugs' => [
    'mediacja-okiem-mediatorki',
    'jak-stworzyc-dobra-ugode-pomediacyjna',
    'dlaczego-warto-korzystac-z-mediacji-rozwodowej',
  ],
];
