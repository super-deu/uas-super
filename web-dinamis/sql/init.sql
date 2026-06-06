-- =====================================================================
--  Feane - Skema & Data Awal (MariaDB / MySQL)
--  Dipakai untuk:
--    1) Import manual via phpMyAdmin / mysql CLI di XAMPP
--    2) Auto-seed Docker (taruh di /docker-entrypoint-initdb.d/)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS feane
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE feane;

-- ---------- Tabel kategori ----------
CREATE TABLE IF NOT EXISTS categories (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(80)  NOT NULL,
  slug       VARCHAR(80)  NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Tabel item menu ----------
CREATE TABLE IF NOT EXISTS menu_items (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name        VARCHAR(120) NOT NULL,
  description TEXT,
  price       DECIMAL(10,2) NOT NULL DEFAULT 0,
  image       VARCHAR(255),                       -- path relatif, mis. images/f1.png
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_menu_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Tabel admin ----------
CREATE TABLE IF NOT EXISTS admins (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  DATA AWAL (seed)
-- =====================================================================

INSERT INTO categories (name, slug) VALUES
  ('Burger', 'burger'),
  ('Pizza',  'pizza'),
  ('Pasta',  'pasta'),
  ('Fries',  'fries');

INSERT INTO menu_items (category_id, name, description, price, image) VALUES
  ((SELECT id FROM categories WHERE slug='pizza'),  'Delicious Pizza', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 20.00, 'images/f1.png'),
  ((SELECT id FROM categories WHERE slug='burger'), 'Delicious Burger', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 15.00, 'images/f2.png'),
  ((SELECT id FROM categories WHERE slug='pizza'),  'Delicious Pizza', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 17.00, 'images/f3.png'),
  ((SELECT id FROM categories WHERE slug='pasta'),  'Delicious Pasta', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 18.00, 'images/f4.png'),
  ((SELECT id FROM categories WHERE slug='fries'),  'French Fries', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 10.00, 'images/f5.png'),
  ((SELECT id FROM categories WHERE slug='pizza'),  'Delicious Pizza', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 15.00, 'images/f6.png'),
  ((SELECT id FROM categories WHERE slug='burger'), 'Tasty Burger', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 12.00, 'images/f7.png'),
  ((SELECT id FROM categories WHERE slug='burger'), 'Tasty Burger', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 14.00, 'images/f8.png'),
  ((SELECT id FROM categories WHERE slug='pasta'),  'Delicious Pasta', 'Veniam debitis quaerat officiis quasi cupiditate quo, quisquam velit, magnam voluptatem repellendus sed eaque', 10.00, 'images/f9.png');

-- Akun admin default  ->  username: admin   password: admin123
INSERT INTO admins (username, password_hash) VALUES
  ('admin', '$2y$10$287zGc9fS7kTXdyENBjplOxSSB/0LLG7GgU2qsRFdYW1T4jhb3qHi');
