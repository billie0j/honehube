-- Honehube Database Schema
-- MySQL Database for Evelyn Hone College E-commerce Platform

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
('Admin User', 'admin@honehube.com', '$2y$10$QjY66S.XNfS.XNfS.XNfS.XNfS.XNfS.XNfS.XNfS.XNfS.', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Insert sample accessories
INSERT INTO accessories (item_name, category, description, original_price, image, status, posted_by) VALUES
('Dell Latitude E7450', 'Laptops', 'Intel Core i5, 8GB RAM, 256GB SSD, 14" display', 4500.00, '../assets/images/lap1.png', 'available', 1),
('Kingston 16GB DDR4 RAM', 'RAM', 'Brand new 16GB DDR4 2666MHz memory module', 750.00, '../assets/images/ram.png', 'available', 1),
('Samsung 500GB SSD', 'Storage', 'High-speed SATA SSD, barely used', 600.00, '../assets/images/hard.png', 'available', 1),
('HP Laptop Charger 65W', 'Chargers', 'Compatible with HP laptops, original charger', 250.00, '../assets/images/adapter.png', 'available', 1),
('Lenovo ThinkPad T480', 'Laptops', 'Intel Core i7, 16GB RAM, 512GB SSD, excellent condition', 6500.00, '../assets/images/lenove1.png', 'available', 1),
('iPhone 15', 'Phones', 'Brand new Apple iPhone 15, 128GB, Blue color, A16 Bionic chip, 6.1" Super Retina XDR display, Dual camera system', 12500.00, '../assets/images/iphone 15.webp', 'available', 1),
('iPhone X', 'Phones', 'Apple iPhone X (iPhone 10), 64GB storage, 5.8" Super Retina OLED display, A11 Bionic chip, Face ID, dual 12MP cameras, wireless charging, excellent condition, iconic design', 5800.00, '../assets/images/10.png', 'available', 1),
('Samsung Galaxy A07', 'Phones', 'Samsung Galaxy A07, 64GB storage, 4GB RAM, excellent condition', 1800.00, '../assets/images/samsung A07.jpg', 'available', 1),
('Samsung Galaxy S20', 'Phones', 'Samsung Galaxy S20, 128GB, Phantom Gray, Excellent Condition, Triple Camera System', 4500.00, '../assets/images/Galaxy20.png', 'available', 1),
('Phone Stand', 'Accessories', 'Adjustable aluminum phone stand, compatible with all smartphones, sturdy and portable design, perfect for desk or bedside use', 150.00, '../assets/images/stand.png', 'available', 1),
('Wireless Charger', 'Chargers', 'Fast wireless charging pad, Qi-certified, 15W output, compatible with iPhone, Samsung, and all Qi-enabled devices, LED indicator, non-slip surface', 350.00, '../assets/images/wireless.png', 'available', 1),
('Laptop Cooling Pad', 'Accessories', 'RGB laptop cooling pad with 6 quiet fans, adjustable height stand, dual USB ports, suitable for 12-17 inch laptops, reduces temperature by up to 20°C', 280.00, '../assets/images/coolinpad.png', 'available', 1),
('Triple Monitor Setup', 'Monitors', 'Professional triple monitor setup, 3x 24-inch Full HD displays, HDMI connectivity, adjustable stands, perfect for productivity and gaming', 8500.00, '../assets/images/tri%20monitor.png', 'available', 1),
('HP EliteBook 840 G5', 'Laptops', 'Intel Core i5 8th Generation, 8GB DDR4 RAM, 256GB SSD (Fast Storage), 14" FHD Display, Slim Smart & Lightweight Design, Battery Backup: 4+ Hours, Condition: Excellent – Business Class Machine', 5200.00, '../assets/images/lap1.png', 'available', 1),
('Dell Latitude 7490', 'Laptops', 'Intel Core i5 8th Generation, 8GB DDR4 RAM, 256GB SSD (Fast Storage), 14" FHD Display, Slim Smart & Lightweight Design, Battery Backup: 4+ Hours, Condition: Excellent – Business Class Machine', 5400.00, '../assets/images/lap2.png', 'available', 1),
('Adjustable Laptop Stand', 'Accessories', 'Ergonomic aluminum laptop stand, adjustable height and angle, compatible with all laptops 10-17 inches, improves posture and airflow, non-slip silicone pads, portable and foldable design', 180.00, '../assets/images/stand1.png', 'available', 1),
('HD Laptop Webcam', 'Accessories', 'Full HD 1080p USB webcam with built-in microphone, clip-on design for laptops and monitors, auto-focus, wide-angle lens, perfect for online classes, video calls, and streaming', 320.00, '../assets/images/came.png', 'available', 1),
('Wireless Mouse', 'Accessories', '2.4GHz wireless mouse with ergonomic design, adjustable DPI settings, silent click buttons, long battery life up to 18 months, USB nano receiver, compatible with Windows, Mac, and Linux', 150.00, '../assets/images/muse.jpg', 'available', 1),
('USB Laptop Speakers', 'Accessories', 'Portable USB-powered stereo speakers, crystal clear sound quality, compact design, volume control knob, 3.5mm audio jack, perfect for laptops, desktops, and tablets, plug-and-play', 280.00, '../assets/images/spesker.png', 'available', 1),
('Wired Earphones', 'Accessories', 'High-quality wired earphones with deep bass, noise isolation, built-in microphone for calls, tangle-free cable, 3.5mm jack, comfortable in-ear design, perfect for music, calls, and online classes', 120.00, '../assets/images/ear.png', 'available', 1),
('Cabled Earbuds', 'Accessories', 'Premium cabled earbuds with superior sound quality, ergonomic fit, noise cancellation, inline remote control, built-in microphone, durable braided cable, 3.5mm jack, perfect for music lovers and students', 180.00, '../assets/images/bug1.png', 'available', 1),
('External Hard Drive', 'Storage', 'Portable external hard drive, USB 3.0 high-speed transfer, compact and lightweight design, plug-and-play, compatible with Windows, Mac, and Linux, perfect for backup and extra storage, available in multiple capacities', 850.00, '../assets/images/hard.png', 'available', 1),
('RAM Module', 'RAM', 'High-performance RAM module for laptops and desktops, DDR4 technology, multiple speeds available (2400MHz, 2666MHz, 3200MHz), all sizes available (4GB, 8GB, 16GB, 32GB), easy installation, compatible with most systems, improves multitasking and performance', 650.00, '../assets/images/ram.png', 'available', 1),
('Power Cable', 'Cables', 'Universal power cable, 1.5 meters length, heavy duty construction, compatible with laptops, monitors, and desktop computers, 3-pin plug, durable and reliable', 80.00, '../assets/images/power.png', 'available', 1),
('Multi Adapter', 'Adapters', 'Universal multi adapter with 4 USB ports and 3 AC outlets, surge protection, compact design, perfect for charging multiple devices simultaneously, ideal for students and travelers', 320.00, '../assets/images/adapter.png', 'available', 1)
ON DUPLICATE KEY UPDATE item_id=item_id;

-- Insert default admin user
-- Password will be set by the installer or manual update
INSERT INTO users (full_name, email, password, role) VALUES 
('Admin User', 'admin@honehube.com', 'TEMP_HASH', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Purchase Requests table
-- (Already defined above, removing duplicate/conflicting version)

-- Negotiations table (tracks negotiation history)
CREATE TABLE IF NOT EXISTS negotiations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('buyer', 'seller') NOT NULL,
    offered_price DECIMAL(10, 2) NOT NULL,
    message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES purchase_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
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

-- Complaints table (for reporting malfunctioning items)
CREATE TABLE IF NOT EXISTS complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    purchase_request_id INT NULL,
    complaint_type ENUM('malfunction', 'defect', 'not_as_described', 'damaged', 'other') DEFAULT 'malfunction',
    subject VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'investigating', 'resolved', 'rejected') DEFAULT 'pending',
    admin_response TEXT NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES accessories(item_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(request_id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_item_id (item_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample listings
INSERT INTO accessories (posted_by, item_name, description, category, original_price, image, status) VALUES
(1, 'Dell Latitude E7450', 'Intel Core i5, 8GB RAM, 256GB SSD, 14" display', 'Laptops', 4500.00, '../assets/images/lap1.png', 'available'),
(1, 'Kingston 16GB DDR4 RAM', 'Brand new 16GB DDR4 2666MHz memory module', 'RAM', 750.00, '../assets/images/ram.png', 'available'),
(1, 'Samsung 500GB SSD', 'High-speed SATA SSD, barely used', 'Storage', 600.00, '../assets/images/hard.png', 'available'),
(1, 'HP Laptop Charger 65W', 'Compatible with HP laptops, original charger', 'Chargers', 250.00, '../assets/images/adapter.png', 'available'),
(1, 'Lenovo ThinkPad T480', 'Intel Core i7, 16GB RAM, 512GB SSD, excellent condition', 'Laptops', 6500.00, '../assets/images/lenove1.png', 'available'),
(1, 'iPhone 15', 'Brand new Apple iPhone 15, 128GB, Blue color, A16 Bionic chip, 6.1" Super Retina XDR display, Dual camera system', 'Phones', 12500.00, '../assets/images/iphone 15.webp', 'available'),
(1, 'iPhone X', 'Apple iPhone X (iPhone 10), 64GB storage, 5.8" Super Retina OLED display, A11 Bionic chip, Face ID, dual 12MP cameras, wireless charging, excellent condition, iconic design', 'Phones', 5800.00, '../assets/images/10.png', 'available'),
(1, 'Samsung Galaxy A07', 'Samsung Galaxy A07, 64GB storage, 4GB RAM, excellent condition', 'Phones', 1800.00, '../assets/images/samsung A07.jpg', 'available'),
(1, 'Samsung Galaxy S20', 'Samsung Galaxy S20, 128GB, Phantom Gray, Excellent Condition, Triple Camera System', 'Phones', 4500.00, '../assets/images/Galaxy20.png', 'available'),
(1, 'Phone Stand', 'Adjustable aluminum phone stand, compatible with all smartphones, sturdy and portable design, perfect for desk or bedside use', 'Accessories', 150.00, '../assets/images/stand.png', 'available'),
(1, 'Wireless Charger', 'Fast wireless charging pad, Qi-certified, 15W output, compatible with iPhone, Samsung, and all Qi-enabled devices, LED indicator, non-slip surface', 'Chargers', 350.00, '../assets/images/wireless.png', 'available'),
(1, 'Laptop Cooling Pad', 'RGB laptop cooling pad with 6 quiet fans, adjustable height stand, dual USB ports, suitable for 12-17 inch laptops, reduces temperature by up to 20°C', 'Accessories', 280.00, '../assets/images/coolinpad.png', 'available'),
(1, 'Triple Monitor Setup', 'Professional triple monitor setup, 3x 24-inch Full HD displays, HDMI connectivity, adjustable stands, perfect for productivity and gaming', 'Monitors', 8500.00, '../assets/images/tri%20monitor.png', 'available'),
(1, 'HP EliteBook 840 G5', 'Intel Core i5 8th Generation, 8GB DDR4 RAM, 256GB SSD (Fast Storage), 14" FHD Display, Slim Smart & Lightweight Design, Battery Backup: 4+ Hours, Condition: Excellent – Business Class Machine', 'Laptops', 5200.00, '../assets/images/lap1.png', 'available'),
(1, 'Dell Latitude 7490', 'Intel Core i5 8th Generation, 8GB DDR4 RAM, 256GB SSD (Fast Storage), 14" FHD Display, Slim Smart & Lightweight Design, Battery Backup: 4+ Hours, Condition: Excellent – Business Class Machine', 'Laptops', 5400.00, '../assets/images/lap2.png', 'available'),
(1, 'Adjustable Laptop Stand', 'Ergonomic aluminum laptop stand, adjustable height and angle, compatible with all laptops 10-17 inches, improves posture and airflow, non-slip silicone pads, portable and foldable design', 'Accessories', 180.00, '../assets/images/stand1.png', 'available'),
(1, 'HD Laptop Webcam', 'Full HD 1080p USB webcam with built-in microphone, clip-on design for laptops and monitors, auto-focus, wide-angle lens, perfect for online classes, video calls, and streaming', 'Accessories', 320.00, '../assets/images/came.png', 'available'),
(1, 'Wireless Mouse', '2.4GHz wireless mouse with ergonomic design, adjustable DPI settings, silent click buttons, long battery life up to 18 months, USB nano receiver, compatible with Windows, Mac, and Linux', 'Accessories', 150.00, '../assets/images/muse.jpg', 'available'),
(1, 'USB Laptop Speakers', 'Portable USB-powered stereo speakers, crystal clear sound quality, compact design, volume control knob, 3.5mm audio jack, perfect for laptops, desktops, and tablets, plug-and-play', 'Accessories', 280.00, '../assets/images/spesker.png', 'available'),
(1, 'Wired Earphones', 'High-quality wired earphones with deep bass, noise isolation, built-in microphone for calls, tangle-free cable, 3.5mm jack, comfortable in-ear design, perfect for music, calls, and online classes', 'Accessories', 120.00, '../assets/images/ear.png', 'available'),
(1, 'Cabled Earbuds', 'Premium cabled earbuds with superior sound quality, ergonomic fit, noise cancellation, inline remote control, built-in microphone, durable braided cable, 3.5mm jack, perfect for music lovers and students', 'Accessories', 180.00, '../assets/images/bug1.png', 'available'),
(1, 'External Hard Drive', 'Portable external hard drive, USB 3.0 high-speed transfer, compact and lightweight design, plug-and-play, compatible with Windows, Mac, and Linux, perfect for backup and extra storage, available in multiple capacities', 'Storage', 850.00, '../assets/images/hard.png', 'available'),
(1, 'RAM Module', 'High-performance RAM module for laptops and desktops, DDR4 technology, multiple speeds available (2400MHz, 2666MHz, 3200MHz), all sizes available (4GB, 8GB, 16GB, 32GB), easy installation, compatible with most systems, improves multitasking and performance', 'RAM', 650.00, '../assets/images/ram.png', 'available'),
(1, 'Power Cable', 'Universal power cable, 1.5 meters length, heavy duty construction, compatible with laptops, monitors, and desktop computers, 3-pin plug, durable and reliable', 'Cables', 80.00, '../assets/images/power.png', 'available'),
(1, 'Multi Adapter', 'Universal multi adapter with 4 USB ports and 3 AC outlets, surge protection, compact design, perfect for charging multiple devices simultaneously, ideal for students and travelers', 'Adapters', 320.00, '../assets/images/adapter.png', 'available')
ON DUPLICATE KEY UPDATE item_id=item_id;