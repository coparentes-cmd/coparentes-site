CREATE TABLE IF NOT EXISTS comments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  parent_id INT UNSIGNED NULL DEFAULT NULL,
  article_slug VARCHAR(120) NOT NULL,
  author_name VARCHAR(80) NOT NULL,
  author_email VARCHAR(190) NOT NULL,
  author_url VARCHAR(255) NULL,
  body TEXT NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  is_staff TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_hash CHAR(64) NULL,
  PRIMARY KEY (id),
  KEY idx_slug_status_created (article_slug, status, created_at),
  KEY idx_status_created (status, created_at),
  KEY idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
