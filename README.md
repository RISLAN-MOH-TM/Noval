# Novel Cosmetics — Local Setup Guide

A natural cosmetics e-commerce web application built with PHP, MySQL, and served through XAMPP. Features a customer-facing storefront and a full admin panel.

---

## Table of Contents

- [Requirements](#requirements)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [URL Routes](#url-routes)
- [Admin Panel](#admin-panel)
- [Default Credentials](#default-credentials)
- [Features](#features)
- [Troubleshooting](#troubleshooting)

---

## Requirements

| Software | Version | Download |
|----------|---------|----------|
| XAMPP    | 8.x+    | [apachefriends.org](https://www.apachefriends.org) |
| PHP      | 8.1+    | Included with XAMPP |
| MySQL    | 8.0+    | Included with XAMPP |
| Apache   | 2.4+    | Included with XAMPP |

> **Windows only setup.** This guide targets Windows with XAMPP installed at `C:\xampp`.

---

## Tech Stack

- **Backend** — PHP 8.x (PDO, sessions, OOP)
- **Database** — MySQL via PDO
- **Frontend** — HTML5, CSS3, Bootstrap 5.3, Font Awesome 6.4
- **Fonts** — Inter, New York (Google Fonts)
- **Server** — Apache with `mod_rewrite` (clean URLs)
- **Checkout** — WhatsApp API redirect

---

## Project Structure

```
novel/
├── .htaccess                  # Clean URL rewrite rules
├── index.php                  # Homepage
├── products.php               # Product listing / search / filter
├── product.php                # Single product detail
├── cart.php                   # Shopping cart
├── about.php                  # About page
├── contact.php                # Contact form
├── login.php                  # Login page
├── logout.php                 # Session destroy + redirect
├── profile.php                # User profile + order history
├── order_confirmation.php     # Order confirmation
├── order_details.php          # Order detail view
│
├── admin/
│   ├── index.php              # Admin dashboard
│   ├── products.php           # Product CRUD
│   ├── categories.php         # Category CRUD
│   ├── users.php              # User management + edit
│   ├── orders.php             # Order management
│   └── includes/
│       └── admin_layout.php   # Shared admin sidebar/topbar
│
├── includes/
│   ├── db_config.php          # Database connection (PDO)
│   ├── product_manager.php    # ProductManager + CategoryManager classes
│   ├── user_manager.php       # UserManager + SessionManager classes
│   ├── cart_manager.php       # CartManager class
│   ├── nav_helper.php         # Shared navbar + footer renderer
│   └── url_helper.php         # URL helper utilities
│
├── css/
│   ├── style.css              # Main site design system
│   └── admin.css              # Admin panel design system
│
├── js/
│   ├── main.js                # Global JS
│   └── navbar-mobile-fix.js   # Mobile nav utilities
│
└── images/
    ├── logo.png
    ├── hero-new.png
    ├── about-us.jpg
    └── products/              # Uploaded product images
```

---

## Installation

### Step 1 — Install XAMPP

Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org).
Default install path: `C:\xampp`

### Step 2 — Place the project

Copy the `novel` folder into XAMPP's web root:

```
C:\xampp\htdocs\novel\
```

The full path to `index.php` should be:

```
C:\xampp\htdocs\novel\index.php
```

### Step 3 — Enable `mod_rewrite`

The application uses clean URLs via Apache `mod_rewrite`.

1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find the line:
   ```
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Remove the `#` to uncomment it:
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Find the `<Directory "C:/xampp/htdocs">` block and change:
   ```
   AllowOverride None
   ```
   to:
   ```
   AllowOverride All
   ```
5. Save the file.

### Step 4 — Start XAMPP services

Open **XAMPP Control Panel** and start:

- ✅ **Apache**
- ✅ **MySQL**

Both status indicators should turn green.

---

## Database Setup

### Step 1 — Open phpMyAdmin

Navigate to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

### Step 2 — Create the database

Click **New** in the left panel and create a database named:

```
natural_cosmetics
```

Collation: `utf8mb4_unicode_ci`

### Step 3 — Import the schema

Run the following SQL to create all required tables. Go to the **SQL** tab in phpMyAdmin and paste:

```sql
-- Users table
CREATE TABLE `users` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `username`   VARCHAR(50)  NOT NULL UNIQUE,
  `email`      VARCHAR(100) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(50)  DEFAULT NULL,
  `last_name`  VARCHAR(50)  DEFAULT NULL,
  `phone`      VARCHAR(20)  DEFAULT NULL,
  `address`    TEXT         DEFAULT NULL,
  `city`       VARCHAR(50)  DEFAULT NULL,
  `state`      VARCHAR(50)  DEFAULT NULL,
  `zip_code`   VARCHAR(20)  DEFAULT NULL,
  `country`    VARCHAR(50)  DEFAULT NULL,
  `user_type`  ENUM('customer','admin') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE `categories` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(100) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table
CREATE TABLE `products` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `name`           VARCHAR(200) NOT NULL,
  `description`    TEXT         DEFAULT NULL,
  `price`          DECIMAL(10,2) NOT NULL,
  `category_id`    INT          DEFAULT NULL,
  `image_url`      VARCHAR(255) DEFAULT NULL,
  `stock_quantity` INT          DEFAULT 0,
  `is_active`      TINYINT(1)   DEFAULT 1,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cart table
CREATE TABLE `cart` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT          DEFAULT NULL,
  `session_id` VARCHAR(255) DEFAULT NULL,
  `product_id` INT          NOT NULL,
  `quantity`   INT          NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE `orders` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`          INT           DEFAULT NULL,
  `total_amount`     DECIMAL(10,2) NOT NULL,
  `status`           ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_status`   ENUM('pending','completed','failed') DEFAULT 'pending',
  `payment_method`   VARCHAR(50)   DEFAULT 'whatsapp',
  `shipping_address` TEXT          DEFAULT NULL,
  `billing_address`  TEXT          DEFAULT NULL,
  `order_date`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order items table
CREATE TABLE `order_items` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `order_id`   INT           NOT NULL,
  `product_id` INT           NOT NULL,
  `quantity`   INT           NOT NULL,
  `price`      DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`)   REFERENCES `orders`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 4 — Create the admin user

Insert a default admin account (password: `admin123`):

```sql
INSERT INTO `users` (`username`, `email`, `password`, `first_name`, `user_type`)
VALUES (
  'admin',
  'admin@novelcosmetics.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'Admin',
  'admin'
);
```

> The password hash above corresponds to `password` (Laravel default hash for testing).
> To generate a proper hash for your own password, run this in PHP:
> ```php
> echo password_hash('yourpassword', PASSWORD_DEFAULT);
> ```

### Step 5 — Seed sample data (optional)

```sql
-- Sample categories
INSERT INTO `categories` (`name`, `description`) VALUES
('Face Care',  'Moisturisers, serums, and cleansers for your face'),
('Body Care',  'Lotions, scrubs, and oils for your body'),
('Lip Care',   'Balms and treatments for soft lips'),
('Hair Care',  'Natural shampoos and conditioners');

-- Sample product
INSERT INTO `products` (`name`, `description`, `price`, `category_id`, `stock_quantity`) VALUES
('Rose Face Serum',  'Pure rose extract serum for glowing skin.',  1200.00, 1, 25),
('Aloe Body Lotion', '100% natural aloe vera moisturising lotion.', 850.00, 2, 40),
('Honey Lip Balm',   'Organic honey lip balm with vitamin E.',      350.00, 3, 60);
```

---

## Configuration

Open `includes/db_config.php` and confirm the settings match your XAMPP setup:

```php
define('DB_HOST', 'localhost');   // XAMPP default
define('DB_USER', 'root');        // XAMPP default
define('DB_PASS', '');            // XAMPP default (empty password)
define('DB_NAME', 'natural_cosmetics');
```

If you changed the MySQL root password in XAMPP, update `DB_PASS` accordingly.

---

## Running the Application

1. Open XAMPP Control Panel → start **Apache** and **MySQL**
2. Open your browser and visit:

```
http://localhost/novel/
```

You will be redirected to the homepage at:

```
http://localhost/novel/home
```

---

## URL Routes

All routes are handled by `.htaccess` using `mod_rewrite` — no `.php` extensions needed.

| URL | Description |
|-----|-------------|
| `http://localhost/novel/home` | Homepage |
| `http://localhost/novel/products` | All products |
| `http://localhost/novel/products/category/1` | Products by category |
| `http://localhost/novel/product/1` | Single product detail |
| `http://localhost/novel/cart` | Shopping cart |
| `http://localhost/novel/about` | About page |
| `http://localhost/novel/contact` | Contact form |
| `http://localhost/novel/login` | Login |
| `http://localhost/novel/logout` | Logout |
| `http://localhost/novel/profile` | User profile (login required) |
| `http://localhost/novel/admin/index.php` | Admin dashboard (admin only) |

---

## Admin Panel

Access the admin panel at:

```
http://localhost/novel/admin/index.php
```

You must be logged in with an account that has `user_type = 'admin'`.

### Admin Features

| Section | URL | What you can do |
|---------|-----|-----------------|
| Dashboard | `/admin/index.php` | Overview stats, stock alerts, recent products |
| Products | `/admin/products.php` | Add, edit, delete products + image upload |
| Categories | `/admin/categories.php` | Add, edit, delete categories |
| Users | `/admin/users.php` | View, edit, promote/demote users |
| Orders | `/admin/orders.php` | View orders, update order status |

---

## Default Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `password` |

> **Change this immediately** after your first login via Admin → Users → Edit, or via the Profile page.

---

## Features

### Storefront
- 🌿 Homepage with hero section, featured products, categories, brand promise
- 🛍️ Products page with category sidebar, search, grid/list toggle
- 📦 Product detail page with add-to-cart and WhatsApp checkout
- 🛒 Cart with quantity update, remove, and WhatsApp order summary
- 👤 User profile with editable info, order history, password change
- 🔒 Login with show/hide password toggle

### Admin Panel
- 📊 Dashboard with live stock alerts and product overview
- ➕ Add/edit/delete products with image upload
- 🏷️ Category management
- 👥 User management with role promotion/demotion and profile editing
- 📋 Order management with status updates

### Technical
- Clean URLs via Apache `mod_rewrite` (no `.php` extensions)
- PDO prepared statements — SQL injection protected
- Password hashing via PHP `password_hash()` / `password_verify()`
- Session-based authentication with admin role check
- Responsive design — mobile, tablet, desktop
- Apple-inspired natural colour palette (sage green, earth amber, cream)

---

## Troubleshooting

### Site shows 404 on all pages

`mod_rewrite` is not enabled. Check:
1. `httpd.conf` has `LoadModule rewrite_module` uncommented
2. `<Directory>` block has `AllowOverride All`
3. Apache was **restarted** after the config change

### "Connection failed" database error

1. Make sure MySQL is running in XAMPP Control Panel
2. Check `includes/db_config.php` — `DB_NAME` must be `natural_cosmetics`
3. The database must be created in phpMyAdmin first

### Images not loading / CSS missing

The site uses `<base href="/novel/">` — all relative asset paths resolve to `/novel/`. Ensure:
1. The project folder is named exactly `novel` inside `htdocs`
2. The full path is `C:\xampp\htdocs\novel\`

### Cannot log in to admin

Run this SQL in phpMyAdmin to verify the admin user exists:
```sql
SELECT id, username, user_type FROM users WHERE user_type = 'admin';
```
If empty, re-run the [admin user insert](#step-4--create-the-admin-user) from the setup steps.

### Product images not uploading

The upload directory must be writable:
```
C:\xampp\htdocs\novel\images\products\
```
Right-click the folder → Properties → Security → give the Apache user (or `Everyone`) write permission.

---

## WhatsApp Checkout

The checkout flow sends the cart contents via WhatsApp message. The phone number is configured in `cart.php` and `product.php`:

```php
$whatsappNumber = "94779191742"; // Sri Lanka format: 94 + number (no leading 0)
```

Change this to your own WhatsApp Business number.

---

## Folder Permissions (Windows)

| Folder | Required Permission |
|--------|-------------------|
| `images/products/` | Write (for product image uploads) |
| `includes/` | Read only |
| `admin/` | Read only (restricted by login check) |

---

*Built with ❤️ for Novel Cosmetics — 100% Natural, 0% Chemicals.*
