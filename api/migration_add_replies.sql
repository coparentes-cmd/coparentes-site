-- Run once in phpMyAdmin on the comments database.
-- Adds one-level staff replies support.

ALTER TABLE comments
  ADD COLUMN parent_id INT UNSIGNED NULL DEFAULT NULL AFTER id,
  ADD COLUMN is_staff TINYINT(1) NOT NULL DEFAULT 0 AFTER status,
  ADD KEY idx_parent (parent_id);
