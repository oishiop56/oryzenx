-- Domain Marketplace Platform - Complete Database Schema
-- Core PHP + MySQL Compatible

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  `password` VARCHAR(255),
  `google_id` VARCHAR(255) UNIQUE,
  `profile_image` VARCHAR(255),
  `is_admin` TINYINT(1) DEFAULT 0,
  `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `email` (`email`),
  INDEX `google_id` (`google_id`),
  INDEX `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `domains` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `domain_name` VARCHAR(255) UNIQUE NOT NULL,
  `description` TEXT,
  `price` DECIMAL(12, 2) NOT NULL,
  `domain_type` ENUM('normal', 'premium') DEFAULT 'normal',
  `status` ENUM('available', 'sold', 'pending') DEFAULT 'available',
  `seller_id` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `domain_type` (`domain_type`),
  INDEX `status` (`status`),
  INDEX `price` (`price`),
  INDEX `created_at` (`created_at`),
  FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `offers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `domain_id` INT NOT NULL,
  `offer_price` DECIMAL(12, 2) NOT NULL,
  `status` ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
  `admin_notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `user_id` (`user_id`),
  INDEX `domain_id` (`domain_id`),
  INDEX `status` (`status`),
  INDEX `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`domain_id`) REFERENCES `domains`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `domain_id` INT NOT NULL,
  `offer_id` INT,
  `amount` DECIMAL(12, 2) NOT NULL,
  `currency` ENUM('BTC', 'USDT') DEFAULT 'USDT',
  `status` ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `user_id` (`user_id`),
  INDEX `domain_id` (`domain_id`),
  INDEX `status` (`status`),
  INDEX `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`domain_id`) REFERENCES `domains`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`offer_id`) REFERENCES `offers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `amount` DECIMAL(12, 2) NOT NULL,
  `currency` ENUM('BTC', 'USDT') DEFAULT 'USDT',
  `transaction_id` VARCHAR(255),
  `screenshot_path` VARCHAR(255),
  `payment_method` VARCHAR(100),
  `wallet_address` VARCHAR(255),
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `admin_notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `order_id` (`order_id`),
  INDEX `user_id` (`user_id`),
  INDEX `status` (`status`),
  INDEX `created_at` (`created_at`),
  UNIQUE KEY `transaction_id` (`transaction_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new', 'read', 'replied') DEFAULT 'new',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `email` (`email`),
  INDEX `status` (`status`),
  INDEX `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(255) UNIQUE NOT NULL,
  `setting_value` LONGTEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('btc_wallet', 'TLKZgeHU45vMuZcHeEHQ95GZQ2UhB3cfxV'),
('usdt_wallet', '0x79395cbf73a98c48bfa53480d16cd5b428b5aff9'),
('site_name', 'OryZenX Domain Marketplace'),
('site_email', 'support@oryzenx.com'),
('google_client_id', 'YOUR_GOOGLE_CLIENT_ID'),
('google_client_secret', 'YOUR_GOOGLE_CLIENT_SECRET');
