-- Honehube Database Schema
-- MySQL Database for Evlyne Hone College E-commerce Platform

-- Create database
CREATE DATABASE IF NOT EXISTS honehube CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE honehube;

-- Users table (stores admin and student accounts)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Accessories table (stores items posted by admin)
CREATE TABLE IF NOT EXISTS accessories (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(200) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    original_price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(500) NULL,
    status ENUM('available', 'sold') DEFAULT 'available',
    posted_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_posted_by (posted_by),
    INDEX idx_category (category),
    INDEX idx_status (status),
    CONSTRAINT chk_price CHECK (original_price > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchase requests table (stores student buying and negotiation requests)
CREATE TABLE IF NOT EXISTS purchase_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    student_id INT NOT NULL,
    original_price DECIMAL(10, 2) NOT NULL,
    offered_price DECIMAL(10, 2) NULL,
    message TEXT NULL,
    status ENUM('pending', 'accepted', 'denied', 'negotiating', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES accessories(item_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_item_id (item_id),
    INDEX idx_student_id (student_id),
    INDEX idx_status (status),
    CONSTRAINT chk_offered_price CHECK (offered_price IS NULL OR offered_price > 0),
    CONSTRAINT chk_original_price CHECK (original_price > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table (for CSRF tokens and session management)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NULL,
    csrf_token VARCHAR(64) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login attempts table (for security - rate limiting)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    success BOOLEAN DEFAULT FALSE,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_ip_address (ip_address),
    INDEX idx_attempted_at (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Account lockouts table (tracks locked accounts)
CREATE TABLE IF NOT EXISTS account_lockouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    locked_until TIMESTAMP NOT NULL,
    lock_reason VARCHAR(255) DEFAULT 'Too many failed login attempts',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_locked_until (locked_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit logs table (tracks all important actions)
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action_type VARCHAR(50) NOT NULL,
    action_description TEXT NOT NULL,
    table_name VARCHAR(50) NULL,
    record_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created_at (created_at),
    INDEX idx_table_name (table_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
-- Password: Admin@123 (hashed with bcrypt)
INSERT INTO users (full_name, email, password, role) VALUES 
('Admin User', 'admin@honehube.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Insert sample accessories
INSERT INTO accessories (item_name, category, description, original_price, image, status, posted_by) VALUES
('Dell Latitude E7450', 'Laptops', 'Intel Core i5, 8GB RAM, 256GB SSD, 14" display', 4500.00, NULL, 'available', 1),
('Kingston 16GB DDR4 RAM', 'RAM', 'Brand new 16GB DDR4 2666MHz memory module', 750.00, NULL, 'available', 1),
('Samsung 500GB SSD', 'Storage', 'High-speed SATA SSD, barely used', 600.00, NULL, 'available', 1),
('HP Laptop Charger 65W', 'Chargers', 'Compatible with HP laptops, original charger', 250.00, NULL, 'available', 1),
('Lenovo ThinkPad T480', 'Laptops', 'Intel Core i7, 16GB RAM, 512GB SSD, excellent condition', 6500.00, NULL, 'available', 1),
('iPhone 15', 'Phones', 'Brand new Apple iPhone 15, 128GB, Blue color, A16 Bionic chip, 6.1" Super Retina XDR display, Dual camera system', 12500.00, '../assets/images/iphone 15.webp', 'available', 1),
('Samsung Galaxy A07', 'Phones', 'Samsung Galaxy A07, 64GB storage, 4GB RAM, excellent condition', 1800.00, '../assets/images/samsung A07.jpg', 'available', 1),
('Phone Stand', 'Accessories', 'Adjustable aluminum phone stand, compatible with all smartphones, sturdy and portable design, perfect for desk or bedside use', 150.00, '../assets/images/stand.png', 'available', 1)
ON DUPLICATE KEY UPDATE item_id=item_id;

-- Insert default admin user
-- Password: Admin@123 (hashed with bcrypt)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@honehube.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Purchase Requests table
CREATE TABLE IF NOT EXISTS purchase_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    buyer_id INT NOT NULL,
    original_price DECIMAL(10, 2) NOT NULL,
    offered_price DECIMAL(10, 2) NULL,
    message TEXT NULL,
    status ENUM('pending', 'negotiating', 'accepted', 'denied', 'cancelled', 'completed') DEFAULT 'pending',
    denial_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_listing_id (listing_id),
    INDEX idx_buyer_id (buyer_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Negotiations table (tracks negotiation history)
CREATE TABLE IF NOT EXISTS negotiations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('buyer', 'seller') NOT NULL,
    offered_price DECIMAL(10, 2) NOT NULL,
    message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES purchase_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_request_id (request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications table (for future use)
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(500) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample listings
INSERT INTO listings (user_id, title, description, category, price, image, condition_type, status) VALUES
(1, 'Dell Latitude E7450', 'Intel Core i5, 8GB RAM, 256GB SSD, 14" display', 'Laptops', 4500.00, NULL, 'used', 'active'),
(1, 'Kingston 16GB DDR4 RAM', 'Brand new 16GB DDR4 2666MHz memory module', 'RAM', 750.00, NULL, 'new', 'active'),
(1, 'Samsung 500GB SSD', 'High-speed SATA SSD, barely used', 'Storage', 600.00, NULL, 'used', 'active'),
(1, 'HP Laptop Charger 65W', 'Compatible with HP laptops, original charger', 'Chargers', 250.00, NULL, 'new', 'active'),
(1, 'Lenovo ThinkPad T480', 'Intel Core i7, 16GB RAM, 512GB SSD, excellent condition', 'Laptops', 6500.00, NULL, 'used', 'active'),
(1, 'iPhone 15', 'Brand new Apple iPhone 15, 128GB, Blue color, A16 Bionic chip, 6.1" Super Retina XDR display, Dual camera system', 'Phones', 12500.00, '../assets/images/iphone 15.webp', 'new', 'active'),
(1, 'Samsung Galaxy A07', 'Samsung Galaxy A07, 64GB storage, 4GB RAM, excellent condition', 'Phones', 1800.00, '../assets/images/samsung A07.jpg', 'used', 'active'),
(1, 'Phone Stand', 'Adjustable aluminum phone stand, compatible with all smartphones, sturdy and portable design, perfect for desk or bedside use', 'Accessories', 150.00, '../assets/images/stand.png', 'new', 'active')
ON DUPLICATE KEY UPDATE id=id;
