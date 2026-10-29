
CREATE DATABASE IF NOT EXISTS nexora
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE nexora;

-- 1. CUSTOMERS
CREATE TABLE customers (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	first_name VARCHAR(100) NOT NULL,
	last_name VARCHAR(100) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	password_hash VARCHAR(255) NOT NULL,
	phone VARCHAR(20) NULL
) ENGINE=InnoDB;

-- 2. ADMINS
CREATE TABLE admins (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- 3. CATEGORIES
CREATE TABLE categories (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL UNIQUE,
	image VARCHAR(255) NULL
) ENGINE=InnoDB;

-- 4. PRODUCTS
CREATE TABLE products (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	category_id INT UNSIGNED NOT NULL,
	name VARCHAR(150) NOT NULL,
	description TEXT NULL,
	price DECIMAL(10,2) NOT NULL,
	stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,

	CONSTRAINT fk_products_category
		FOREIGN KEY (category_id)
		REFERENCES categories(id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,

	CONSTRAINT chk_products_price
		CHECK (price >= 0)
) ENGINE=InnoDB;

-- 5. PRODUCT_IMAGES
CREATE TABLE product_images (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	product_id BIGINT UNSIGNED NOT NULL,
	image VARCHAR(255) NOT NULL,

	CONSTRAINT fk_product_images_product
		FOREIGN KEY (product_id)
		REFERENCES products(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. CARTS
CREATE TABLE carts (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	customer_id BIGINT UNSIGNED NULL,

	CONSTRAINT fk_carts_customer
		FOREIGN KEY (customer_id)
		REFERENCES customers(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. CART_ITEMS
CREATE TABLE cart_items (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	cart_id BIGINT UNSIGNED NOT NULL,
	product_id BIGINT UNSIGNED NOT NULL,
	quantity INT UNSIGNED NOT NULL,

	CONSTRAINT fk_cart_items_cart
		FOREIGN KEY (cart_id)
		REFERENCES carts(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

	CONSTRAINT fk_cart_items_product
		FOREIGN KEY (product_id)
		REFERENCES products(id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,

	CONSTRAINT uq_cart_product
		UNIQUE (cart_id, product_id),

	CONSTRAINT chk_cart_quantity
		CHECK (quantity > 0)
) ENGINE=InnoDB;

-- 8. ORDERS
CREATE TABLE orders (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	customer_id BIGINT UNSIGNED NOT NULL,
	order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	total_amount DECIMAL(10,2) NOT NULL,
	status ENUM(
		'pending',
		'confirmed',
		'shipped',
		'delivered',
		'cancelled'
	) NOT NULL DEFAULT 'pending',
	shipping_address VARCHAR(255) NOT NULL,

	CONSTRAINT fk_orders_customer
		FOREIGN KEY (customer_id)
		REFERENCES customers(id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,

	CONSTRAINT chk_orders_total
		CHECK (total_amount >= 0)
) ENGINE=InnoDB;

-- 9. ORDER_ITEMS
CREATE TABLE order_items (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	order_id BIGINT UNSIGNED NOT NULL,
	product_id BIGINT UNSIGNED NULL,
	quantity INT UNSIGNED NOT NULL,
	unit_price DECIMAL(10,2) NOT NULL,

	CONSTRAINT fk_order_items_order
		FOREIGN KEY (order_id)
		REFERENCES orders(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

	CONSTRAINT fk_order_items_product
		FOREIGN KEY (product_id)
		REFERENCES products(id)
		ON UPDATE CASCADE
		ON DELETE SET NULL,

	CONSTRAINT chk_order_item_quantity
		CHECK (quantity > 0),

	CONSTRAINT chk_order_item_price
		CHECK (unit_price >= 0)
) ENGINE=InnoDB;

-- 10. PAYMENTS
CREATE TABLE payments (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	order_id BIGINT UNSIGNED NOT NULL,
	amount DECIMAL(10,2) NOT NULL,
	method VARCHAR(50) NOT NULL,
	status ENUM(
		'pending',
		'successful',
		'failed'
	) NOT NULL DEFAULT 'pending',

	CONSTRAINT fk_payments_order
		FOREIGN KEY (order_id)
		REFERENCES orders(id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,

	CONSTRAINT chk_payment_amount
		CHECK (amount >= 0)
) ENGINE=InnoDB;

-- 11. WISHLISTS
CREATE TABLE wishlists (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	customer_id BIGINT UNSIGNED NOT NULL UNIQUE,

	CONSTRAINT fk_wishlists_customer
		FOREIGN KEY (customer_id)
		REFERENCES customers(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. WISHLIST_ITEMS
CREATE TABLE wishlist_items (
	wishlist_id BIGINT UNSIGNED NOT NULL,
	product_id BIGINT UNSIGNED NOT NULL,

	PRIMARY KEY (wishlist_id, product_id),



	CONSTRAINT fk_wishlist_items_wishlist
		FOREIGN KEY (wishlist_id)
		REFERENCES wishlists(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

	CONSTRAINT fk_wishlist_items_product
		FOREIGN KEY (product_id)
		REFERENCES products(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- 13. REVIEWS
CREATE TABLE reviews (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	customer_id BIGINT UNSIGNED NOT NULL,
	product_id BIGINT UNSIGNED NOT NULL,
	rating TINYINT UNSIGNED NOT NULL,
	comment TEXT NULL,

	CONSTRAINT fk_reviews_customer
		FOREIGN KEY (customer_id)
		REFERENCES customers(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

	CONSTRAINT fk_reviews_product
		FOREIGN KEY (product_id)
		REFERENCES products(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

	CONSTRAINT uq_customer_product_review
		UNIQUE (customer_id, product_id),

	CONSTRAINT chk_review_rating
		CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;


use nexora;
ALTER TABLE customers
    ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE orders
    ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE reviews
    ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE products
    ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE categories
    ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
	
