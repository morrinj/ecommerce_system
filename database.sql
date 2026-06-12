-- SmartShopping E-Commerce Database Schema
-- Engine: InnoDB | Charset: utf8mb4

CREATE DATABASE IF NOT EXISTS smartshopping
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE smartshopping;

-- -----------------------------------------------------------
-- Users Table
-- -----------------------------------------------------------
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  address_line1 VARCHAR(255) DEFAULT NULL,
  address_line2 VARCHAR(255) DEFAULT NULL,
  city VARCHAR(100) DEFAULT NULL,
  county VARCHAR(100) DEFAULT NULL,
  zip_code VARCHAR(20) DEFAULT NULL,
  country VARCHAR(100) DEFAULT 'KE',
  email_verified_at DATETIME DEFAULT NULL,
  remember_token VARCHAR(100) DEFAULT NULL,
  reset_token VARCHAR(100) DEFAULT NULL,
  reset_token_expires DATETIME DEFAULT NULL,
  status ENUM('active','inactive','banned') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_email (email),
  INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Admins Table
-- -----------------------------------------------------------
CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  role ENUM('superadmin','admin','manager') DEFAULT 'admin',
  avatar VARCHAR(255) DEFAULT NULL,
  last_login DATETIME DEFAULT NULL,
  failed_attempts INT DEFAULT 0,
  locked_until DATETIME DEFAULT NULL,
  password_changed_at DATETIME DEFAULT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_admins_email (email),
  INDEX idx_admins_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Categories Table
-- -----------------------------------------------------------
CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description TEXT DEFAULT NULL,
  parent_id INT UNSIGNED DEFAULT NULL,
  image VARCHAR(255) DEFAULT NULL,
  icon VARCHAR(50) DEFAULT NULL,
  sort_order INT DEFAULT 0,
  is_featured TINYINT(1) DEFAULT 0,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_categories_slug (slug),
  INDEX idx_categories_parent (parent_id),
  INDEX idx_categories_status (status),
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Products Table
-- -----------------------------------------------------------
CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED DEFAULT NULL,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  short_description VARCHAR(500) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  compare_price DECIMAL(10,2) DEFAULT NULL,
  cost_price DECIMAL(10,2) DEFAULT NULL,
  sku VARCHAR(50) DEFAULT NULL UNIQUE,
  barcode VARCHAR(100) DEFAULT NULL,
  stock_quantity INT DEFAULT 0,
  stock_alert_threshold INT DEFAULT 5,
  is_track_stock TINYINT(1) DEFAULT 1,
  weight DECIMAL(8,2) DEFAULT NULL,
  dimensions VARCHAR(100) DEFAULT NULL,
  image_primary VARCHAR(255) DEFAULT NULL,
  images JSON DEFAULT NULL,
  is_featured TINYINT(1) DEFAULT 0,
  is_new TINYINT(1) DEFAULT 0,
  is_on_sale TINYINT(1) DEFAULT 0,
  tags JSON DEFAULT NULL,
  meta_title VARCHAR(200) DEFAULT NULL,
  meta_description VARCHAR(500) DEFAULT NULL,
  average_rating DECIMAL(3,2) DEFAULT 0.00,
  review_count INT UNSIGNED DEFAULT 0,
  status ENUM('active','inactive','draft') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_products_slug (slug),
  INDEX idx_products_category (category_id),
  INDEX idx_products_status (status),
  INDEX idx_products_price (price),
  INDEX idx_products_featured (is_featured),
  INDEX idx_products_rating (average_rating),
  FULLTEXT idx_products_search (name, short_description, description),
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Product Images (Gallery)
-- -----------------------------------------------------------
CREATE TABLE product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(200) DEFAULT NULL,
  sort_order INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pi_product (product_id),
  CONSTRAINT fk_pi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Cart Table
-- -----------------------------------------------------------
CREATE TABLE cart (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  session_id VARCHAR(100) DEFAULT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_cart_user (user_id),
  INDEX idx_cart_session (session_id),
  INDEX idx_cart_product (product_id),
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Coupons Table
-- -----------------------------------------------------------
CREATE TABLE coupons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  description TEXT DEFAULT NULL,
  type ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
  value DECIMAL(10,2) NOT NULL,
  min_order_amount DECIMAL(10,2) DEFAULT 0.00,
  max_uses INT UNSIGNED DEFAULT NULL,
  uses_count INT UNSIGNED DEFAULT 0,
  max_uses_per_user INT UNSIGNED DEFAULT 1,
  is_active TINYINT(1) DEFAULT 1,
  starts_at DATETIME DEFAULT NULL,
  expires_at DATETIME DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_coupons_code (code),
  INDEX idx_coupons_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Orders Table
-- -----------------------------------------------------------
CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(20) NOT NULL UNIQUE,
  user_id INT UNSIGNED DEFAULT NULL,
  guest_email VARCHAR(100) DEFAULT NULL,
  status ENUM('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  payment_status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
  payment_method VARCHAR(50) DEFAULT NULL,
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  discount_amount DECIMAL(10,2) DEFAULT 0.00,
  coupon_id INT UNSIGNED DEFAULT NULL,
  tax_amount DECIMAL(10,2) DEFAULT 0.00,
  shipping_amount DECIMAL(10,2) DEFAULT 0.00,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  currency VARCHAR(3) DEFAULT 'KES',
  shipping_first_name VARCHAR(50) DEFAULT NULL,
  shipping_last_name VARCHAR(50) DEFAULT NULL,
  shipping_address1 VARCHAR(255) DEFAULT NULL,
  shipping_address2 VARCHAR(255) DEFAULT NULL,
  shipping_city VARCHAR(100) DEFAULT NULL,
  shipping_county VARCHAR(100) DEFAULT NULL,
  shipping_zip VARCHAR(20) DEFAULT NULL,
  shipping_country VARCHAR(100) DEFAULT 'KE',

  billing_country VARCHAR(100) DEFAULT 'KE',
  notes TEXT DEFAULT NULL,
  tracking_number VARCHAR(100) DEFAULT NULL,
  shipped_at DATETIME DEFAULT NULL,
  delivered_at DATETIME DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_orders_user (user_id),
  INDEX idx_orders_status (status),
  INDEX idx_orders_number (order_number),
  INDEX idx_orders_payment (payment_status),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Coupon Usage Table
-- -----------------------------------------------------------
CREATE TABLE coupon_usage (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  coupon_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  order_id INT UNSIGNED DEFAULT NULL,
  used_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_cu_coupon (coupon_id),
  INDEX idx_cu_user (user_id),
  CONSTRAINT fk_cu_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cu_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cu_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Order Items Table
-- -----------------------------------------------------------
CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED DEFAULT NULL,
  product_name VARCHAR(200) NOT NULL,
  product_sku VARCHAR(50) DEFAULT NULL,
  product_image VARCHAR(255) DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  subtotal DECIMAL(10,2) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_oi_order (order_id),
  INDEX idx_oi_product (product_id),
  CONSTRAINT fk_oi_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Payments Table
-- -----------------------------------------------------------
CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED DEFAULT NULL,
  transaction_id VARCHAR(100) DEFAULT NULL,
  payment_method VARCHAR(50) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(3) DEFAULT 'KES',
  status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
  response_data JSON DEFAULT NULL,
  paid_at DATETIME DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_pay_order (order_id),
  INDEX idx_pay_transaction (transaction_id),
  CONSTRAINT fk_pay_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pay_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Reviews Table
-- -----------------------------------------------------------
CREATE TABLE reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED DEFAULT NULL,
  order_id INT UNSIGNED DEFAULT NULL,
  rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
  title VARCHAR(200) DEFAULT NULL,
  comment TEXT DEFAULT NULL,
  is_approved TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_rev_product (product_id),
  INDEX idx_rev_user (user_id),
  INDEX idx_rev_approved (is_approved),
  CONSTRAINT fk_rev_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_rev_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_rev_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Wishlist Table
-- -----------------------------------------------------------
CREATE TABLE wishlist (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_wishlist_user_product (user_id, product_id),
  INDEX idx_wish_user (user_id),
  INDEX idx_wish_product (product_id),
  CONSTRAINT fk_wish_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_wish_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Shipping Details Table
-- -----------------------------------------------------------
CREATE TABLE shipping_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  county VARCHAR(100) NOT NULL,
  city VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  apartment VARCHAR(100) DEFAULT NULL,
  postal_code VARCHAR(20) DEFAULT NULL,
  delivery_option VARCHAR(50) DEFAULT 'standard',
  order_notes TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sd_user (user_id),
  CONSTRAINT fk_sd_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- User Activity Log (for AI recommendations)
-- -----------------------------------------------------------
CREATE TABLE user_activities (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  session_id VARCHAR(100) DEFAULT NULL,
  activity_type ENUM('view','search','add_to_cart','remove_from_cart','purchase','wishlist','review') NOT NULL,
  product_id INT UNSIGNED DEFAULT NULL,
  category_id INT UNSIGNED DEFAULT NULL,
  search_query VARCHAR(255) DEFAULT NULL,
  metadata JSON DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ua_user (user_id),
  INDEX idx_ua_type (activity_type),
  INDEX idx_ua_product (product_id),
  INDEX idx_ua_created (created_at),
  CONSTRAINT fk_ua_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_ua_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- AI Chatbot Conversations
-- -----------------------------------------------------------
CREATE TABLE chatbot_conversations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  session_id VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  response TEXT DEFAULT NULL,
  intent VARCHAR(100) DEFAULT NULL,
  is_resolved TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_cc_session (session_id),
  INDEX idx_cc_user (user_id),
  CONSTRAINT fk_cc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- AI Recommendations Cache
-- -----------------------------------------------------------
CREATE TABLE ai_recommendations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  session_id VARCHAR(100) DEFAULT NULL,
  product_id INT UNSIGNED NOT NULL,
  score DECIMAL(5,4) DEFAULT 0.0000,
  reason VARCHAR(255) DEFAULT NULL,
  type ENUM('personalized','trending','related','upsell','cross_sell') DEFAULT 'personalized',
  expires_at DATETIME DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ar_user (user_id),
  INDEX idx_ar_product (product_id),
  INDEX idx_ar_type (type),
  CONSTRAINT fk_ar_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_ar_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Insert Default Admin
-- -----------------------------------------------------------
INSERT INTO admins (username, email, password, full_name, role)
VALUES ('admin', 'admin@smartshop.com', '$2y$10$jw/YNKpzeWh1DbezdMol5eCY2oAmFuuDk1jGJua49fxQLWUuzKzI6', 'Administrator', 'superadmin');
-- Default password: "admin123" (generated with password_hash())

-- -----------------------------------------------------------
-- Insert Sample Categories
-- -----------------------------------------------------------
INSERT INTO categories (name, slug, description, is_featured, sort_order) VALUES
('Coats & Jackets', 'coats-jackets', 'Outerwear including trench coats, biker jackets, puffers, and blazers', 1, 1),
('Dresses', 'dresses', 'Mini, midi, maxi, shirt, and occasion dresses', 1, 2),
('Tops', 'tops', 'T-shirts, crop tops, camis, bodysuits, and vests', 1, 3),
('Shirts & Blouses', 'shirts-blouses', 'Button-down shirts, blouses, and formal tops', 1, 4),
('Jumpers & Cardigans', 'jumpers-cardigans', 'Knitwear, jumpers, cardigans, and sweaters', 1, 5),
('Trousers & Leggings', 'trousers-leggings', 'Trousers, leggings, joggers, and tailored pants', 1, 6),
('Jeans', 'jeans', 'Denim jeans in various fits and washes', 0, 7),
('Shorts', 'shorts', 'Casual and tailored shorts for all seasons', 0, 8),
('Skirts', 'skirts', 'Mini, midi, maxi, and denim skirts', 0, 9),
('Suits & Blazers', 'suits-blazers', 'Tailored suits, blazers, and formal separates', 0, 10),
('Jumpsuits & Playsuits', 'jumpsuits-playsuits', 'One-piece outfits and rompers', 0, 11),
('Lingerie & Nightwear', 'lingerie-nightwear', 'Bras, underwear, lingerie, pyjamas, and loungewear', 0, 12),
('Swimwear', 'swimwear', 'Bikinis, swimsuits, and beachwear', 0, 13),
('Sportswear', 'sportswear', 'Activewear, gym wear, and sports apparel', 0, 14),
('Accessories', 'accessories', 'Bags, jewellery, hats, socks, and belts', 0, 15),
('Maternity', 'maternity', 'Maternity wear for all stages of pregnancy', 0, 16);

-- -----------------------------------------------------------
-- Insert Sample Products
-- -----------------------------------------------------------
INSERT INTO products (category_id, name, slug, short_description, description, price, compare_price, sku, stock_quantity, is_featured, is_new, is_on_sale, status) VALUES
(1, 'Classic Trench Coat in Camel', 'classic-trench-coat-camel', 'Timeless trench coat with notch collar and tie waist', 'A wardrobe essential. This classic trench coat features a notch collar, button placket, tie waist, and side pockets. Regular fit.', 49.99, 79.99, 'CT-JK-001', 50, 1, 1, 1, 'active'),
(1, 'Faux Leather Biker Jacket in Black', 'faux-leather-biker-jacket-black', 'Edgy faux leather biker jacket with asymmetric zip', 'Make a statement with this faux leather biker jacket. Features an asymmetric zip fastening, notch collar, and functional pockets.', 59.99, 89.99, 'CT-JK-002', 35, 1, 0, 0, 'active'),
(1, 'Oversized Trench Coat in Stone', 'oversized-trench-coat-stone', 'Relaxed fit trench coat with belted waist', 'Low-key layering at its finest. This oversized trench coat features a notch collar, button placket, belted waist, and side pockets.', 45.00, 65.00, 'CT-JK-003', 40, 0, 0, 1, 'active'),
(2, 'Floral Wrap Midi Dress', 'floral-wrap-midi-dress', 'Beautiful floral print midi dress with wrap front', 'Love at first scroll. This floral midi dress features a wrap front, tie waist, and bell sleeves. Regular fit.', 39.99, 59.99, 'DR-001', 45, 1, 1, 1, 'active'),
(2, 'Little Black Mini Dress', 'little-black-mini-dress', 'Versatile black mini dress for any occasion', 'The perfect LBD. Features a scoop neck, short sleeves, and a flattering regular fit silhouette.', 29.99, 44.99, 'DR-002', 60, 1, 0, 0, 'active'),
(3, 'Ribbed Crop Top in White', 'ribbed-crop-top-white', 'Essential ribbed crop top in classic white', 'Cos your jeans deserve a nice top. This ribbed crop top features a scoop neck and slim fit.', 14.99, 22.99, 'TP-001', 80, 1, 0, 1, 'active'),
(3, 'Oversized Graphic T-Shirt', 'oversized-graphic-t-shirt', 'Relaxed graphic t-shirt with logo print', 'Act casual. This oversized t-shirt features a crew neck, short sleeves, and a graphic logo print to chest.', 24.99, 34.99, 'TP-002', 65, 0, 1, 0, 'active'),
(5, 'Ribbed Knit Jumper in Beige', 'ribbed-knit-jumper-beige', 'Cosy ribbed knit jumper in neutral beige', 'Cosy never looked so good. This jumper features a crew neck, drop shoulders, and an oversized fit.', 34.99, 49.99, 'KN-001', 40, 1, 1, 0, 'active'),
(14, 'Nike Running Hooded Jacket in Pink', 'nike-running-hooded-jacket-pink', 'Lightweight running jacket with reflective details', 'Hit that new PB. Features a toggle hood, zip fastening, side pockets, and reflective details for visibility.', 84.95, 109.99, 'SP-001', 25, 0, 0, 1, 'active'),
(15, 'Gold Chain Necklace', 'gold-chain-necklace', '14k gold plated chain necklace with lobster clasp', 'The finishing touch. Features a fine chain, adjustable length, and lobster clasp fastening.', 19.99, 29.99, 'AC-001', 100, 1, 1, 0, 'active');

-- -----------------------------------------------------------
-- Insert Sample Reviews
-- -----------------------------------------------------------
INSERT INTO reviews (product_id, user_id, rating, title, comment, is_approved) VALUES
(1, NULL, 5, 'Perfect trench coat!', 'Classic style that goes with everything. The fit is spot on.', 1),
(1, NULL, 4, 'Great value coat', 'Love the colour and material. True to size.', 1),
(2, NULL, 5, 'Edgy and stylish', 'The faux leather looks amazing. Gets compliments everywhere.', 1),
(4, NULL, 4, 'Beautiful dress', 'The floral print is gorgeous and the wrap style is flattering.', 1),
(9, NULL, 5, 'Great running jacket', 'Lightweight, breathable, and the reflective details are a nice touch.', 1);

-- -----------------------------------------------------------
-- Settings Table
-- -----------------------------------------------------------
CREATE TABLE settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT DEFAULT NULL,
  group_name VARCHAR(50) DEFAULT 'general',
  type ENUM('text','textarea','email','image','select','number') DEFAULT 'text',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_settings_key (setting_key),
  INDEX idx_settings_group (group_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (setting_key, setting_value, group_name, type) VALUES
('store_name', 'SmartShop', 'general', 'text'),
('store_email', 'admin@smartshop.com', 'general', 'email'),
('store_phone', '+254 712 345 678', 'general', 'text'),
('store_address', 'Kaunda Street, Nairobi, Kenya', 'general', 'textarea'),
('store_currency', 'KES', 'general', 'text'),
('store_tax_rate', '16', 'general', 'number'),
('store_shipping_threshold', '5000', 'general', 'number'),
('store_shipping_rate', '350', 'general', 'number'),
('facebook_url', '', 'social', 'text'),
('twitter_url', '', 'social', 'text'),
('instagram_url', '', 'social', 'text'),
('email_smtp_host', '', 'email', 'text'),
('email_smtp_port', '587', 'email', 'number'),
('email_smtp_user', '', 'email', 'text'),
('email_smtp_pass', '', 'email', 'text');

-- -----------------------------------------------------------
-- Migration: Add password security columns to admins table
-- Run this if upgrading from an older version
-- -----------------------------------------------------------
-- ALTER TABLE admins
--   ADD COLUMN IF NOT EXISTS failed_attempts INT DEFAULT 0 AFTER last_login,
--   ADD COLUMN IF NOT EXISTS locked_until DATETIME DEFAULT NULL AFTER failed_attempts,
--   ADD COLUMN IF NOT EXISTS password_changed_at DATETIME DEFAULT NULL AFTER locked_until;

-- -----------------------------------------------------------
-- ASOS Product Import from products_asos.csv
-- -----------------------------------------------------------
-- The file assets/images/products_asos.csv contains 30,845 ASOS fashion products.
--
-- Option A — Quick CSV import (basic fields, no descriptions/images):
--   LOAD DATA LOCAL INFILE './assets/images/products_asos.csv'
--   INTO TABLE products_temp
--   FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n'
--   IGNORE 1 LINES (url, name, size, category, price, color, sku, description, images);
--
-- Option B — Full import with descriptions, images, categories, and currency conversion:
--   Run: php migrate_asos.php
--   (This parses the Python-dict format descriptions, extracts image URLs,
--    maps product names to categories, converts GBP to KES, and inserts 15,000 products.)
--
-- The migrate_asos.php script handles all the complex transformations
-- that cannot be done in pure SQL.
