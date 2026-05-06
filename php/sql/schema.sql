-- schema.sql — PanelaFinca · Base de datos completa
-- Ejecutar una vez en XAMPP: mysql -u root < schema.sql

CREATE DATABASE IF NOT EXISTS ventas_panela
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ventas_panela;

-- ── Usuarios ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id         INT          NOT NULL AUTO_INCREMENT,
  name       VARCHAR(120) NOT NULL,
  email      VARCHAR(150) NOT NULL,
  password   VARCHAR(255) NOT NULL,  -- bcrypt hash
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

-- ── Productos ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
  id                INT            NOT NULL AUTO_INCREMENT,
  name              VARCHAR(150)   NOT NULL,
  short_description VARCHAR(255)   DEFAULT NULL,
  description       TEXT           DEFAULT NULL,
  price             DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  stock             INT            NOT NULL DEFAULT 0,
  image             VARCHAR(255)   DEFAULT NULL,
  created_at        TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FULLTEXT KEY ft_products_search (name, short_description, description)
) ENGINE=InnoDB;

-- ── Órdenes ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
  id         INT           NOT NULL AUTO_INCREMENT,
  user_id    INT           DEFAULT NULL,       -- NULL = invitado
  total      DECIMAL(10,2) NOT NULL,
  status     ENUM('pending','paid','shipped','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_orders_user (user_id),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Ítems de orden ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
  id         INT           NOT NULL AUTO_INCREMENT,
  order_id   INT           NOT NULL,
  product_id INT           NOT NULL,
  name       VARCHAR(150)  NOT NULL,  -- snapshot del nombre al momento de compra
  price      DECIMAL(10,2) NOT NULL,  -- snapshot del precio
  qty        INT           NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_order_items_order (order_id),
  CONSTRAINT fk_oi_order   FOREIGN KEY (order_id)   REFERENCES orders   (id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ── Datos de prueba ───────────────────────────────────────────
INSERT INTO products (name, short_description, description, price, stock, image) VALUES
  ('Panela en bloque 1 kg',
   'Panela artesanal tradicional de 1 kg',
   'Elaborada en fogón de leña con caña cultivada sin agroquímicos. Sabor intenso y color dorado natural. Ideal para preparar agua de panela, coladas y postres.',
   12000, 50, 'panela_bloque_1kg.jpg'),
  ('Panela granulada 500 g',
   'Presentación granulada, fácil de medir',
   'Panela pulverizada artesanalmente, perfecta para endulzar bebidas, repostería y marinadas. Sin conservantes ni colorantes artificiales.',
   7000, 80, 'panela_granulada_500g.jpg'),
  ('Miel de caña 250 ml',
   'Jarabe natural de primera extracción',
   'Primera extracción del proceso de producción de panela. Sabor concentrado y textura suave. Úsala como endulzante o jarabe para cócteles.',
   9500, 30, 'miel_cana_250ml.jpg');
