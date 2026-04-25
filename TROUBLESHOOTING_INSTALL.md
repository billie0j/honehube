# Troubleshooting Installation Issues

## Problem: Can't Run install.php

### Quick Diagnosis

Run these commands to check your system:

```powershell
# Check if Apache is running
Test-NetConnection localhost -Port 80

# Check if MySQL is running
Test-NetConnection localhost -Port 3306

# Check if XAMPP is installed
Test-Path "C:\xampp\xampp-control.exe"
```

---

## Solution 1: Start XAMPP Services

### Step 1: Open XAMPP Control Panel

**Method A: Using File Explorer**
1. Navigate to: `C:\xampp\`
2. Double-click: `xampp-control.exe`

**Method B: Using PowerShell**
```powershell
Start-Process "C:\xampp\xampp-control.exe"
```

**Method C: Using Windows Search**
1. Press `Windows Key`
2. Type: "XAMPP"
3. Click "XAMPP Control Panel"

### Step 2: Start Services

1. In XAMPP Control Panel, click **"Start"** next to **Apache**
2. Wait until it shows green "Running"
3. Click **"Start"** next to **MySQL**
4. Wait until it shows green "Running"

**Expected Result:**
```
Apache  [Running] [Port: 80, 443]
MySQL   [Running] [Port: 3306]
```

### Step 3: Test Apache

Open browser and visit:
```
http://localhost
```

You should see the XAMPP welcome page.

### Step 4: Run install.php

Visit:
```
http://localhost/honehube/install.php
```

---

## Solution 2: Manual Database Setup

If install.php still doesn't work, set up the database manually:

### Step 1: Open phpMyAdmin

Visit:
```
http://localhost/phpmyadmin
```

### Step 2: Create Database

1. Click **"SQL"** tab at the top
2. Copy and paste this SQL:

```sql
CREATE DATABASE IF NOT EXISTS honehube CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE honehube;
```

3. Click **"Go"**

### Step 3: Create Tables

Copy and paste the contents of `database/schema.sql` into the SQL tab and click "Go".

Or run this complete script:

```sql
-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    student_id VARCHAR(20) NULL UNIQUE,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_student_id (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listings table
CREATE TABLE IF NOT EXISTS listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    condition_type ENUM('new', 'used') NOT NULL,
    image VARCHAR(500) NULL,
    status ENUM('active', 'sold', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_category (category),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inquiries table
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'replied', 'closed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_listing_id (listing_id),
    INDEX idx_buyer_id (buyer_id),
    INDEX idx_seller_id (seller_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NULL,
    csrf_token VARCHAR(64) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login attempts table
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

-- Insert default admin user
-- Password: Admin@123 (hashed with bcrypt)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@honehube.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Insert sample listings
INSERT INTO listings (user_id, title, description, category, price, condition_type, status) VALUES
(1, 'Dell Latitude E7450', 'Intel Core i5, 8GB RAM, 256GB SSD, 14" display', 'Laptops', 450.00, 'used', 'active'),
(1, 'Kingston 16GB DDR4 RAM', 'Brand new 16GB DDR4 2666MHz memory module', 'RAM', 75.00, 'new', 'active'),
(1, 'Samsung 500GB SSD', 'High-speed SATA SSD, barely used', 'Storage', 60.00, 'used', 'active'),
(1, 'HP Laptop Charger 65W', 'Compatible with HP laptops, original charger', 'Chargers', 25.00, 'new', 'active'),
(1, 'Lenovo ThinkPad T480', 'Intel Core i7, 16GB RAM, 512GB SSD, excellent condition', 'Laptops', 650.00, 'used', 'active')
ON DUPLICATE KEY UPDATE id=id;
```

4. Click **"Go"**

### Step 4: Verify Installation

1. Click on **"honehube"** database in left sidebar
2. You should see 5 tables:
   - users
   - listings
   - inquiries
   - sessions
   - login_attempts

---

## Solution 3: Check Common Issues

### Issue 1: Port 80 Already in Use

**Symptoms:**
- Apache won't start
- Error: "Port 80 in use by another application"

**Solutions:**

**A. Stop Conflicting Programs:**
```powershell
# Stop IIS (if installed)
Stop-Service -Name W3SVC -Force

# Check what's using port 80
netstat -ano | findstr :80
```

**B. Change Apache Port:**
1. In XAMPP Control Panel, click **"Config"** next to Apache
2. Select **"httpd.conf"**
3. Find: `Listen 80`
4. Change to: `Listen 8080`
5. Save and restart Apache
6. Access via: `http://localhost:8080/honehube/`

### Issue 2: Port 3306 Already in Use

**Symptoms:**
- MySQL won't start
- Error: "Port 3306 in use"

**Solutions:**

**A. Stop Other MySQL Services:**
```powershell
# List MySQL services
Get-Service | Where-Object {$_.Name -like "*mysql*"}

# Stop conflicting MySQL
Stop-Service -Name "MySQL80" -Force
```

**B. Change MySQL Port:**
1. In XAMPP Control Panel, click **"Config"** next to MySQL
2. Select **"my.ini"**
3. Find: `port=3306`
4. Change to: `port=3307`
5. Update `api/config.php`:
   ```php
   define('DB_HOST', 'localhost:3307');
   ```

### Issue 3: XAMPP Not Installed

**Solution:**

Download and install XAMPP:
1. Visit: https://www.apachefriends.org/
2. Download XAMPP for Windows
3. Run installer
4. Install to: `C:\xampp\`
5. Start XAMPP Control Panel

### Issue 4: PHP Not Working

**Symptoms:**
- Browser downloads install.php instead of running it
- Blank page when accessing install.php

**Solutions:**

**A. Check PHP Module:**
1. Open XAMPP Control Panel
2. Click **"Config"** next to Apache
3. Select **"httpd.conf"**
4. Ensure these lines exist:
   ```apache
   LoadModule php_module "C:/xampp/php/php8apache2_4.dll"
   AddHandler application/x-httpd-php .php
   ```

**B. Restart Apache:**
1. Stop Apache in XAMPP
2. Wait 5 seconds
3. Start Apache again

### Issue 5: File Not Found (404)

**Symptoms:**
- 404 error when accessing install.php
- "File not found"

**Solutions:**

**A. Check File Location:**
```powershell
# Verify file exists
Test-Path "C:\xampp\htdocs\honehube\install.php"
```

**B. Check Project Location:**
Your project MUST be in:
```
C:\xampp\htdocs\honehube\
```

Not in:
```
C:\Users\YourName\Documents\honehube\  ❌
C:\xampp\honehube\  ❌
```

**C. Move Project if Needed:**
```powershell
# Copy project to correct location
Copy-Item -Path ".\*" -Destination "C:\xampp\htdocs\honehube\" -Recurse
```

---

## Solution 4: Alternative - Use Command Line

If browser access doesn't work, run installation via command line:

```powershell
# Navigate to project
cd C:\xampp\htdocs\honehube

# Run PHP directly
C:\xampp\php\php.exe install.php
```

---

## Solution 5: Skip install.php (Use System Without Database)

The system has a **hybrid mode** that works without database:

1. **Skip database installation**
2. **Use localStorage mode:**
   - Registration works
   - Login works
   - Listings work
   - All data stored in browser

3. **Access the site:**
   ```
   http://localhost/honehube/index.html
   ```

4. **Register a new account:**
   ```
   http://localhost/honehube/register.html
   ```

The system will automatically detect that the database is unavailable and use localStorage as fallback.

---

## Verification Steps

After trying solutions, verify everything works:

### 1. Check XAMPP Status
```powershell
# Apache should return True
Test-NetConnection localhost -Port 80 -InformationLevel Quiet

# MySQL should return True
Test-NetConnection localhost -Port 3306 -InformationLevel Quiet
```

### 2. Test Apache
Visit: `http://localhost`
- Should see XAMPP welcome page

### 3. Test phpMyAdmin
Visit: `http://localhost/phpmyadmin`
- Should see phpMyAdmin interface

### 4. Test Your Project
Visit: `http://localhost/honehube/`
- Should see your homepage

### 5. Test install.php
Visit: `http://localhost/honehube/install.php`
- Should see installation page

---

## Quick Fix Commands

Run these in PowerShell to fix common issues:

```powershell
# Start XAMPP Control Panel
Start-Process "C:\xampp\xampp-control.exe"

# Check if services are running
Test-NetConnection localhost -Port 80
Test-NetConnection localhost -Port 3306

# Open browser to test
Start-Process "http://localhost"
Start-Process "http://localhost/honehube/"

# Check project location
Test-Path "C:\xampp\htdocs\honehube\install.php"

# View Apache error log
Get-Content "C:\xampp\apache\logs\error.log" -Tail 20
```

---

## Still Not Working?

### Get Detailed Error Information

1. **Check Apache Error Log:**
   ```
   C:\xampp\apache\logs\error.log
   ```

2. **Check PHP Error Log:**
   ```
   C:\xampp\php\logs\php_error_log
   ```

3. **Enable Error Display:**
   - Open: `C:\xampp\php\php.ini`
   - Find: `display_errors = Off`
   - Change to: `display_errors = On`
   - Restart Apache

4. **Check Browser Console:**
   - Press F12
   - Go to Console tab
   - Look for errors

---

## Contact Information

If you're still having issues, provide:
1. XAMPP version
2. Windows version
3. Error messages from logs
4. Screenshots of XAMPP Control Panel
5. What happens when you visit `http://localhost`

---

## Summary

**Most Common Solution:**
1. Start XAMPP Control Panel
2. Start Apache
3. Start MySQL
4. Visit: `http://localhost/honehube/install.php`

**If That Doesn't Work:**
1. Use phpMyAdmin to run SQL manually
2. Or use the system without database (localStorage mode)

---

**Last Updated:** 2026-04-25
