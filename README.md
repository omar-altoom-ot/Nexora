<<<<<<< HEAD
<div align="center">

# NEXORA

### Build beyond limits.

**A PC hardware store and interactive build lab, powered by PHP, MySQL, and Three.js.**

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MariaDB](https://img.shields.io/badge/Database-MariaDB-003545?logo=mariadb&logoColor=white)](https://mariadb.org/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES_Modules-F7DF1E?logo=javascript&logoColor=111)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![3D](https://img.shields.io/badge/3D-Three.js-black?logo=threedotjs&logoColor=white)](https://threejs.org/)

</div>

NEXORA is a full-stack e-commerce project for browsing PC hardware, comparing products, and assembling a visual PC build. It includes a customer storefront and an admin area for managing the store.

## What you can do

### Shop

- Browse products by category, search, price range, and sort order.
- View product details, images, stock, and customer reviews.
- Add products to a cart, continue through checkout, and review previous orders.
- Save products to a wishlist.
- Compare two products in the same category. An optional Gemini integration can summarize the comparison using the product information available in the database.
- Use the PC Builder to select components, estimate the build total, and see them placed in an interactive 3D case preview.

### Manage the store

- Admin pages for products, categories, customers, orders, coupons, notifications, and the admin profile.
- Upload product images and manage product availability and stock.
- Moderate product reviews and manage store coupons.

> **About the PC Builder:** the 3D assembly is a visual preview. The current project does not validate hardware compatibility; technical specifications for that feature are still pending.

## Built with

| Area | Technologies |
| --- | --- |
| Server | PHP 8+, PDO |
| Database | MySQL or MariaDB |
| Front end | HTML, CSS, JavaScript ES modules |
| 3D preview | Three.js, GLTF models, OrbitControls |
| AI comparison | Google Gemini API (optional) |
| Local development | XAMPP or another Apache/PHP/MySQL stack |

## Project layout

```text
Nexora/
├── Assets/
│   ├── 3D/builder/       # GLB models used by the PC Builder
│   └── Images/           # Store and category artwork
├── Config/               # Database connection and admin guards
├── Controllers/          # Catalog, cart, account, order, and admin logic
├── Model/                # Data models, repositories, schema, and migrations
├── View/
│   ├── Includes/         # Shared page header and footer
│   ├── Layout/           # Store navigation and front controller
│   ├── Pages/            # Storefront, account, checkout, and admin pages
│   ├── css/              # Page and component styles
│   └── js/               # PC Builder scripts
├── tests/                # CLI database smoke-check scripts
├── index.php             # Local site entry point
└── README.md
```

## Run locally with XAMPP

### Requirements

- PHP 8.0 or newer with `PDO MySQL`, `fileinfo`, and `cURL` enabled.
- MySQL or MariaDB.
- Apache, if using XAMPP.
- An internet connection for the Three.js modules loaded from jsDelivr. The optional AI comparison also needs access to the Gemini API.

### 1. Put the project under XAMPP

Clone or copy the project into XAMPP's `htdocs` directory and keep the folder name `Nexora`:

```bash
cd C:\xampp\htdocs
git clone <your-repository-url> Nexora
```

The project currently uses `/Nexora` in some asset and redirect paths, so use that folder name for a local XAMPP setup.

### 2. Start Apache and MySQL

Open the XAMPP Control Panel and start **Apache** and **MySQL**. Confirm MySQL remains running before opening the site.

### 3. Create the database

In phpMyAdmin, import:

```text
Model/Database.sql
```

This script creates and selects the `nexora` database and defines the application's base tables.

Then apply the cart stock-reservation migration:

```text
Model/migrations/20261031_cart_stock_reservations.sql
```

Run the migration once, after the base schema. It adds `stock_reserved_quantity` to `cart_items`.

### 4. Check the database connection

The connection settings are currently defined in [`Config/DBconnection.php`](Config/DBconnection.php). The local defaults are:

| Setting | Default |
| --- | --- |
| Host | `localhost` |
| Database | `nexora` |
| Username | `root` |
| Password | empty |

Change these values there if your local MySQL setup uses different credentials. Do not commit real production credentials.

### 5. Open NEXORA

Visit:

```text
http://localhost/Nexora/
```

Create a customer account from the storefront. The admin sign-in page is available at:

```text
http://localhost/Nexora/View/Layout/routing.php?page=admin-login
```

### Google sign-in (optional)

Create a **Web application** OAuth client in [Google Cloud Console](https://console.cloud.google.com/), and add this authorized redirect URI:

```text
http://localhost/Nexora/google-callback.php
```

Copy `Config/google-oauth.example.php` to `Config/google-oauth.local.php` and put the client ID and client secret into the copied file. The local config is ignored by Git. The redirect URI must match the one in Google Cloud exactly. The base schema supports Google-only customers with a nullable password hash and a unique Google account ID. For an existing database, run this once if you have not already:

```sql
ALTER TABLE customers
  MODIFY password_hash VARCHAR(255) NULL,
  ADD COLUMN google_id VARCHAR(255) NULL UNIQUE;
```

## Optional Gemini comparison

Product comparison works without AI for selecting and viewing products. To enable the AI summary, provide a Gemini API key to the PHP/Apache process as `GCP_API_KEY`, then restart Apache. For XAMPP, one option is to add this to Apache's environment configuration:

```apache
SetEnv GCP_API_KEY "your-api-key"
```

Keep the real key out of Git. `.env.example` is only an example file; the application does not automatically load `.env` files.

## Database smoke checks

The scripts in `tests/` are command-line checks that connect to the configured `nexora` database. Run them from the repository root after starting MySQL and importing the schema:

```bash
php tests/check_catalog.php
php tests/check_catalog_controller.php
php tests/check_products.php
```

These are development checks, not a standalone automated test suite. Their output depends on the data in your local database.

## Security before publishing or deployment

- `Config/DBconnection.php` currently contains local development connection defaults. Use private deployment settings and never commit production credentials.
- Review `create_admin.php` before publishing or exposing the app. It currently contains a development admin seed; replace it with a secure, private provisioning process or remove it after local setup.
- Use HTTPS and secure session settings in production.
- Restrict write access to `Assets/Images/products/` to the PHP process and serve uploaded files as static images only.
- Set a private Gemini API key only in the server environment; never place it in browser JavaScript or commit it.

## Current scope

NEXORA is an active development project. Product browsing, cart and checkout flows, customer accounts, wishlist, reviews, comparison, admin screens, and the 3D builder are present. AI comparison requires a server key, and PC hardware compatibility validation is not implemented yet.
=======
<div align="center">

# NEXORA

### Build beyond limits.

**A PC hardware store and interactive build lab, powered by PHP, MySQL, and Three.js.**

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MariaDB](https://img.shields.io/badge/Database-MariaDB-003545?logo=mariadb&logoColor=white)](https://mariadb.org/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES_Modules-F7DF1E?logo=javascript&logoColor=111)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![3D](https://img.shields.io/badge/3D-Three.js-black?logo=threedotjs&logoColor=white)](https://threejs.org/)

</div>

NEXORA is a full-stack e-commerce project for browsing PC hardware, comparing products, and assembling a visual PC build. It includes a customer storefront and an admin area for managing the store.

## What you can do

### Shop

- Browse products by category, search, price range, and sort order.
- View product details, images, stock, and customer reviews.
- Add products to a cart, continue through checkout, and review previous orders.
- Save products to a wishlist.
- Compare two products in the same category. An optional Gemini integration can summarize the comparison using the product information available in the database.
- Use the PC Builder to select components, estimate the build total, and see them placed in an interactive 3D case preview.

### Manage the store

- Admin pages for products, categories, customers, orders, coupons, notifications, and the admin profile.
- Upload product images and manage product availability and stock.
- Moderate product reviews and manage store coupons.

> **About the PC Builder:** the 3D assembly is a visual preview. The current project does not validate hardware compatibility; technical specifications for that feature are still pending.

## Built with

| Area | Technologies |
| --- | --- |
| Server | PHP 8+, PDO |
| Database | MySQL or MariaDB |
| Front end | HTML, CSS, JavaScript ES modules |
| 3D preview | Three.js, GLTF models, OrbitControls |
| AI comparison | Google Gemini API (optional) |
| Local development | XAMPP or another Apache/PHP/MySQL stack |

## Project layout

```text
Nexora/
├── Assets/
│   ├── 3D/builder/       # GLB models used by the PC Builder
│   └── Images/           # Store and category artwork
├── Config/               # Database connection and admin guards
├── Controllers/          # Catalog, cart, account, order, and admin logic
├── Model/                # Data models, repositories, schema, and migrations
├── View/
│   ├── Includes/         # Shared page header and footer
│   ├── Layout/           # Store navigation and front controller
│   ├── Pages/            # Storefront, account, checkout, and admin pages
│   ├── css/              # Page and component styles
│   └── js/               # PC Builder scripts
├── tests/                # CLI database smoke-check scripts
├── index.php             # Local site entry point
└── README.md
```

## Run locally with XAMPP

### Requirements

- PHP 8.0 or newer with `PDO MySQL`, `fileinfo`, and `cURL` enabled.
- MySQL or MariaDB.
- Apache, if using XAMPP.
- An internet connection for the Three.js modules loaded from jsDelivr. The optional AI comparison also needs access to the Gemini API.

### 1. Put the project under XAMPP

Clone or copy the project into XAMPP's `htdocs` directory and keep the folder name `Nexora`:

```bash
cd C:\xampp\htdocs
git clone <your-repository-url> Nexora
```

The project currently uses `/Nexora` in some asset and redirect paths, so use that folder name for a local XAMPP setup.

### 2. Start Apache and MySQL

Open the XAMPP Control Panel and start **Apache** and **MySQL**. Confirm MySQL remains running before opening the site.

### 3. Create the database

In phpMyAdmin, import:

```text
Model/Database.sql
```

This script creates and selects the `nexora` database and defines the application's base tables.

Then apply the cart stock-reservation migration:

```text
Model/migrations/20261031_cart_stock_reservations.sql
```

Run the migration once, after the base schema. It adds `stock_reserved_quantity` to `cart_items`.

### 4. Check the database connection

The connection settings are currently defined in [`Config/DBconnection.php`](Config/DBconnection.php). The local defaults are:

| Setting | Default |
| --- | --- |
| Host | `localhost` |
| Database | `nexora` |
| Username | `root` |
| Password | empty |

Change these values there if your local MySQL setup uses different credentials. Do not commit real production credentials.

### 5. Open NEXORA

Visit:

```text
http://localhost/Nexora/
```

Create a customer account from the storefront. The admin sign-in page is available at:

```text
http://localhost/Nexora/View/Layout/routing.php?page=admin-login
```

## Optional Gemini comparison

Product comparison works without AI for selecting and viewing products. To enable the AI summary, provide a Gemini API key to the PHP/Apache process as `GCP_API_KEY`, then restart Apache. For XAMPP, one option is to add this to Apache's environment configuration:

```apache
SetEnv GCP_API_KEY "your-api-key"
```

Keep the real key out of Git. `.env.example` is only an example file; the application does not automatically load `.env` files.

## Database smoke checks

The scripts in `tests/` are command-line checks that connect to the configured `nexora` database. Run them from the repository root after starting MySQL and importing the schema:

```bash
php tests/check_catalog.php
php tests/check_catalog_controller.php
php tests/check_products.php
```

These are development checks, not a standalone automated test suite. Their output depends on the data in your local database.

## Security before publishing or deployment

- `Config/DBconnection.php` currently contains local development connection defaults. Use private deployment settings and never commit production credentials.
- Review `create_admin.php` before publishing or exposing the app. It currently contains a development admin seed; replace it with a secure, private provisioning process or remove it after local setup.
- Use HTTPS and secure session settings in production.
- Restrict write access to `Assets/Images/products/` to the PHP process and serve uploaded files as static images only.
- Set a private Gemini API key only in the server environment; never place it in browser JavaScript or commit it.

## Current scope

NEXORA is an active development project. Product browsing, cart and checkout flows, customer accounts, wishlist, reviews, comparison, admin screens, and the 3D builder are present. AI comparison requires a server key, and PC hardware compatibility validation is not implemented yet.
>>>>>>> 11233e573d38d18c262f168bcb6676d22fc64330
