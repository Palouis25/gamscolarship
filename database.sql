CREATE TABLE IF NOT EXISTS newsletter_subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  status ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
  interests VARCHAR(255) NOT NULL DEFAULT 'all',
  unsubscribe_token CHAR(64) NOT NULL,
  source VARCHAR(80) NOT NULL DEFAULT 'website',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  last_sent_at DATETIME NULL,
  INDEX idx_newsletter_status (status),
  INDEX idx_newsletter_last_sent (last_sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
