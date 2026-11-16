CREATE DATABASE IF NOT EXISTS pinterest_gallery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pinterest_gallery;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    token_expiry DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_images_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Password for 'admin' is: arjun123
INSERT INTO users (username, password) VALUES
('admin', '$2y$12$9O682n.z4ThNw.bWNjQ2d.PoI4wnf.p3c/zeZYOWm0zE7M3t35DBC');