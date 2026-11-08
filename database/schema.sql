-- Schéma MySQL cible pour la prochaine étape Laravel.
CREATE TABLE users (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  role ENUM('admin','stylist','client') NOT NULL,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) UNIQUE NULL,
  phone VARCHAR(30) UNIQUE NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
CREATE TABLE stylist_profiles (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  shop_name VARCHAR(180) NOT NULL,
  city VARCHAR(100),
  bio TEXT,
  published BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_stylist_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE measurement_profiles (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  client_id BIGINT UNSIGNED NOT NULL,
  label VARCHAR(120) NOT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_measure_client FOREIGN KEY(client_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE measurements (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  profile_id BIGINT UNSIGNED NOT NULL,
  chest DECIMAL(6,2) NULL,
  waist DECIMAL(6,2) NULL,
  hips DECIMAL(6,2) NULL,
  shoulder DECIMAL(6,2) NULL,
  sleeve DECIMAL(6,2) NULL,
  length DECIMAL(6,2) NULL,
  recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_measurement_profile FOREIGN KEY(profile_id) REFERENCES measurement_profiles(id) ON DELETE CASCADE
);
CREATE TABLE products (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  stylist_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  category ENUM('sur_mesure','pret_a_porter') NOT NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  description TEXT NULL,
  published BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_product_stylist FOREIGN KEY(stylist_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE orders (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  stylist_id BIGINT UNSIGNED NOT NULL,
  client_id BIGINT UNSIGNED NOT NULL,
  measurement_profile_id BIGINT UNSIGNED NULL,
  product_id BIGINT UNSIGNED NULL,
  status ENUM('reçue','tissu_valide','coupe','couture','essayage','pret','livree','annulee') NOT NULL DEFAULT 'reçue',
  sale_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  due_date DATE NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_order_stylist FOREIGN KEY(stylist_id) REFERENCES users(id),
  CONSTRAINT fk_order_client FOREIGN KEY(client_id) REFERENCES users(id),
  CONSTRAINT fk_order_profile FOREIGN KEY(measurement_profile_id) REFERENCES measurement_profiles(id) ON DELETE SET NULL,
  CONSTRAINT fk_order_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
);
CREATE TABLE order_costs (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  materials DECIMAL(12,2) NOT NULL DEFAULT 0,
  labor DECIMAL(12,2) NOT NULL DEFAULT 0,
  fixed_costs DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_cost_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE
);
CREATE TABLE stock_items (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  stylist_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  category VARCHAR(80) NOT NULL,
  quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
  unit VARCHAR(20) NOT NULL,
  min_threshold DECIMAL(10,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_stock_stylist FOREIGN KEY(stylist_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE appointments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  stylist_id BIGINT UNSIGNED NOT NULL,
  client_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NULL,
  type VARCHAR(100) NOT NULL,
  starts_at DATETIME NOT NULL,
  ends_at DATETIME NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_appt_stylist FOREIGN KEY(stylist_id) REFERENCES users(id),
  CONSTRAINT fk_appt_client FOREIGN KEY(client_id) REFERENCES users(id),
  CONSTRAINT fk_appt_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE SET NULL
);
CREATE TABLE reviews (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL UNIQUE,
  client_id BIGINT UNSIGNED NOT NULL,
  stylist_id BIGINT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  comment TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
