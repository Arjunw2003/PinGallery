# Gallery-PHP 📌
A modern, Pinterest-style image gallery web application built with **Core PHP**, **MySQL**, and **AJAX** featuring seamless CRUD operations, secure user authentication, and a responsive masonry grid layout.

![PHP](https://img.shields.io/badge/PHP-8.x-blue?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/Database-MySQL-orange?style=flat&logo=mysql)
![Bootstrap](https://img.shields.io/badge/UI-Bootstrap%205.3-purple?style=flat&logo=bootstrap)
![AJAX](https://img.shields.io/badge/Async-AJAX%2FjQuery-green?style=flat&logo=jquery)

---

## 🚀 Features

- **🔐 Secure Authentication:** Login system with pre-hashed bcrypt credentials (`arjun123`), PHP sessions, and a SHA-256 hashed **"Remember Me"** cookie token mechanism.
- **⚡ Real-Time AJAX CRUD:** Create, Edit, and Delete pins dynamically without full-page reloads.
- **🎨 Pinterest-Style Masonry Layout:** Responsive CSS multi-column masonry grid that adapts smoothly across desktop, tablet, and mobile screens.
- **📁 Safe File Uploads:** Validates image extensions (`JPG`, `JPEG`, `PNG`, `GIF`, `WEBP`), image dimensions (`getimagesize`), and file size limits (max 5 MB).
- **🛡️ SQL Injection & XSS Protection:** Built using PHP MySQLi Prepared Statements and `htmlspecialchars` output escaping.

---

## 🛠️ Tech Stack

- **Backend:** Core PHP (8.x)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3 (Masonry Layout), JavaScript, jQuery 3.7, Bootstrap 5.3

---

## 📂 Project Directory Structure

```text
PinPulse-PHP/
│
├── ajax/
│   ├── imageCrud.php    
│   └── login.php   # Handles AJAX login verification
│
├── config/
│   └── db.php              # MySQLi database connection configuration
│
├── includes/
│   ├── auth_check.php      # Protects dashboard & validates remember_token cookies
│   ├── header.php          # Bootstrap CSS & Masonry grid styles
│   └── footer.php          # jQuery & Bootstrap JS scripts
│
├── uploads/                # Directory where uploaded images are stored
├── database.sql            # MySQL database schema & pre-seeded admin user
├── index.php               # Login page
├── dashboard.php           # Main Pinterest-style gallery dashboard
└── logout.php
