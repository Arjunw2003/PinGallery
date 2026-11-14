# PinPulse-PHP 📌
A modern, Pinterest-style image gallery web application built with **Core PHP**, **MySQL**, and **AJAX** featuring seamless CRUD operations, user authentication, and a responsive masonry grid layout.

![UI/UX Prototype](https://img.shields.io/badge/PHP-8.x-blue?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/Database-MySQL-orange?style=flat&logo=mysql)
![Bootstrap](https://img.shields.io/badge/UI-Bootstrap%205-purple?style=flat&logo=bootstrap)
![AJAX](https://img.shields.io/badge/Async-AJAX%2FjQuery-green?style=flat&logo=jquery)

---

## 🚀 Features

- **🔐 Secure Authentication:** User login system with password hashing (`password_hash` / `password_verify`), session management, and a secure **"Remember Me"** cookie token mechanism.
- **⚡ AJAX CRUD Operations:** Create, Update, and Delete pins dynamically without full-page reloads.
- **🎨 Pinterest-Style Masonry Grid:** Fully responsive staggered grid design tailored for visual media sharing.
- **📁 Secure File Uploads:** Validates image extensions, MIME types, and file sizes to prevent malicious uploads.
- **🛡️ SQL Injection Protection:** Built entirely using PHP MySQLi Prepared Statements.

---

## 🛠️ Tech Stack

- **Backend:** Core PHP (8.x)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript, jQuery, Bootstrap 5
- **Icons:** FontAwesome 6

---

## 📂 Project Directory Structure

```text
pinpulse-php/
│
├── ajax/
│   ├── image_create.php    # Handles AJAX image upload & insert
│   ├── image_update.php    # Handles AJAX image edit/update
│   ├── image_delete.php    # Handles AJAX image deletion
│   └── login_process.php   # Handles AJAX login authentication
│
├── config/
│   └── db.php              # Database connection script
│
├── includes/
│   ├── auth_check.php      # Session & remember token validator
│   ├── header.php          # Common HTML header & assets
│   └── footer.php          # Common HTML footer
│
├── uploads/                # Stores uploaded pin images
├── index.php               # Login page
├── dashboard.php           # Pinterest-style gallery dashboard
└── logout.php              # Destroys session & cookies
