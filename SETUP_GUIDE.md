# 🚀 HoneHube Setup Guide

Complete step-by-step guide to set up the HoneHube marketplace system.

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation Steps](#installation-steps)
3. [Configuration](#configuration)
4. [Testing](#testing)
5. [Production Deployment](#production-deployment)
6. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Required Software
- **XAMPP** (Apache + MySQL + PHP 7.4+)
  - Download: https://www.apachefriends.org/
  - Install to: `C:\xampp\`

### System Requirements
- Windows 10/11 or Linux/Mac
- 2GB RAM minimum
- 500MB free disk space
- Modern web browser (Chrome, Firefox, Safari, Edge)
- Internet connection (for reCAPTCHA)

---

## Installation Steps

### Step 1: Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/
2. Run the installer
3. Install to `C:\xampp\` (default location)
4. Select components:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
5. Complete installation

### Step 2: Get Project Files

**Option A: Clone from Git**
```bash
cd C:\xampp\htdocs\
git clone <repository-url> honehube
```

**Option B: Download ZIP**
1. Download project ZIP file
2. Extract to `C:\xampp\htdocs\honehube\`

### Step 3: Start Services

**Method 1: XAMPP Control Panel**
1. Open XAMPP Control Panel
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for green "Running" status

**Method 2: Quick Start Script**
```bash
cd C:\xampp\htdocs\honehube\
scripts\start-xampp.bat
```

**Method 3: PowerShell Script**
```powershell
cd C:\xampp\htdocs\honehube\
.\scripts\Start-Honehube.ps1
```

### Step 4: Run Installation Wizard

1. Open browser
2. Navigate to: `http://localhost/honehube/backend/scripts/install.php`
3. You should see the installation page
4. Click "Install Database" button
5. Wait for success message
6. Database is now ready!

### Step 5: Verify Installation

1. Go to: `http://localhost/honehube/`
2. You should see the landing page
3. Click "Browse Items"
4. You should see sample items

---

## Configuration

### Database Configuration

**File:** `backend/config/config.php`

```php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'honehube');
define('DB_USER', 'root');      // Change for production
define('DB_PASS', '');          // Change for production
define('DB_CHARSET', 'utf8mb4');
```

### Security Configuration

**For Production:**

1. **Create Limited Database User**
```sql
CREATE USER 'honehube_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT SELECT, INSERT, UPDATE, DELETE ON honehube.* TO 'honehube_user'@'localhost';
FLUSH PRIVILEGES;
```

2. **Update config.php**
```php
define('DB_USER', 'honehube_user');
define('DB_PASS', 'strong_password_here');
error_reporting(0);
ini_set('display_errors', 0);
```

3. **Enable HTTPS**
```php
ini_set('session.cookie_secure', 1);
```

### reCAPTCHA Configuration

**Get Keys:**
1. Go to: https://www.google.com/recaptcha/admin
2. Register your site
3. Choose reCAPTCHA v2 (Checkbox)
4. Get Site Key and Secret Key

**Update Frontend:**

File: `frontend/pages/login.html` (line ~75)
```html
<div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY_HERE"></div>
```

File: `frontend/pages/register.html` (line ~95)
```html
<div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY_HERE"></div>
```

**Update Backend:**

File: `backend/api/auth.php` (add verification function)
```php
function verifyRecaptcha($response) {
    $secret = 'YOUR_SECRET_KEY_HERE';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
    $captcha_success = json_decode($verify);
    return $captcha_success->success;
}
```

---

## Testing

### Test Admin Features

1. **Login as Admin**
   - URL: `http://localhost/honehube/frontend/pages/login.html`
   - Email: `admin@honehube.com`
   - Password: `Admin@123`

2. **Create Listing**
   - Go to Admin Dashboard
   - Click "➕ Create Listing"
   - Fill in details:
     - Title: "Test Laptop"
     - Category: "Laptops"
     - Price: 500
     - Description: "Test description"
   - Click "Create Listing"
   - Verify item appears in listings

3. **View Purchase Requests**
   - Go to "Purchase Requests" section
   - Should see any student requests

4. **Manage Users**
   - Go to "Recent Users" section
   - Click "View All Users"
   - Verify user list appears

### Test Student Features

1. **Register New Account**
   - URL: `http://localhost/honehube/frontend/pages/register.html`
   - Fill in details
   - Complete reCAPTCHA
   - Click "Create Account"

2. **Browse Items**
   - Go to: `http://localhost/honehube/frontend/pages/index.html`
   - Verify items are displayed
   - Test search functionality
   - Test category filters

3. **Send Purchase Request**
   - Click on any item
   - Fill in purchase request form
   - Enter offer price (lower than original)
   - Add message
   - Click "Send Purchase Request"
   - Verify success message

4. **View Dashboard**
   - Go to: `http://localhost/honehube/frontend/pages/dashboard.html`
   - Verify purchase requests are displayed
   - Check request status

---

## Production Deployment

### Pre-Deployment Checklist

- [ ] Change default admin password
- [ ] Create limited database user
- [ ] Update database credentials in config.php
- [ ] Disable error display
- [ ] Enable HTTPS
- [ ] Update reCAPTCHA keys (production keys)
- [ ] Set up automated backups
- [ ] Test all features
- [ ] Review security settings
- [ ] Update CORS origins

### Deployment Steps

1. **Upload Files**
   - Upload entire project to web server
   - Maintain directory structure

2. **Configure Database**
   ```bash
   mysql -u root -p < backend/database/schema.sql
   ```

3. **Update Configuration**
   - Edit `backend/config/config.php`
   - Update database credentials
   - Update CORS origins
   - Disable error display

4. **Set File Permissions**
   ```bash
   chmod 755 backend/scripts/
   chmod 644 backend/config/config.php
   chmod 755 frontend/
   ```

5. **Configure SSL Certificate**
   - Install SSL certificate
   - Update .htaccess to force HTTPS
   - Update session.cookie_secure to 1

6. **Set Up Automated Backups**
   
   **Linux (Cron Job):**
   ```bash
   # Edit crontab
   crontab -e
   
   # Add daily backup at 2 AM
   0 2 * * * php /path/to/honehube/backend/scripts/backup_database.php
   ```
   
   **Windows (Task Scheduler):**
   1. Open Task Scheduler
   2. Create Basic Task
   3. Name: "HoneHube Database Backup"
   4. Trigger: Daily at 2:00 AM
   5. Action: Start a program
   6. Program: `C:\xampp\htdocs\honehube\scripts\backup-database.bat`

7. **Test Production Site**
   - Test all features
   - Verify HTTPS is working
   - Check database connection
   - Test backups

---

## Troubleshooting

### Issue: "API not available" message

**Cause:** MySQL is not running

**Solution:**
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Refresh the page

### Issue: "404 Not Found" errors

**Cause:** Project not in correct location

**Solution:**
1. Verify project is in `C:\xampp\htdocs\honehube\`
2. Check .htaccess file exists
3. Verify Apache mod_rewrite is enabled

### Issue: "Access denied for user" error

**Cause:** Incorrect database credentials

**Solution:**
1. Open `backend/config/config.php`
2. Verify DB_USER and DB_PASS are correct
3. Test MySQL connection:
   ```bash
   mysql -u root -p
   ```

### Issue: "Can't create listing"

**Cause:** Not logged in as admin

**Solution:**
1. Logout
2. Login with admin credentials:
   - Email: `admin@honehube.com`
   - Password: `Admin@123`

### Issue: Items not loading

**Cause:** Database connection issue or empty database

**Solution:**
1. Run installation wizard again
2. Check MySQL is running
3. Verify database exists:
   ```sql
   SHOW DATABASES;
   USE honehube;
   SHOW TABLES;
   ```

### Issue: reCAPTCHA not working

**Cause:** Using test keys or incorrect keys

**Solution:**
1. Get production keys from Google reCAPTCHA
2. Update keys in login.html and register.html
3. Update secret key in auth.php

### Issue: Session timeout too fast

**Cause:** Default session lifetime is 1 hour

**Solution:**
1. Open `backend/config/config.php`
2. Update SESSION_LIFETIME:
   ```php
   define('SESSION_LIFETIME', 7200); // 2 hours
   ```

### Issue: Backup script not working

**Cause:** mysqldump not in PATH

**Solution:**
1. Add MySQL bin to PATH:
   ```
   C:\xampp\mysql\bin\
   ```
2. Or update backup script with full path:
   ```bash
   C:\xampp\mysql\bin\mysqldump.exe
   ```

---

## Additional Resources

### Documentation
- [README.md](README.md) - Project overview
- [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) - Directory structure
- [docs/ADMIN_GUIDE.md](docs/ADMIN_GUIDE.md) - Admin user guide
- [docs/STUDENT_GUIDE.md](docs/STUDENT_GUIDE.md) - Student user guide
- [docs/FINAL_SECURITY_GUIDE.md](docs/FINAL_SECURITY_GUIDE.md) - Security guide

### Quick Links
- Landing Page: `http://localhost/honehube/`
- Browse Items: `http://localhost/honehube/frontend/pages/index.html`
- Login: `http://localhost/honehube/frontend/pages/login.html`
- Register: `http://localhost/honehube/frontend/pages/register.html`
- Admin Dashboard: `http://localhost/honehube/frontend/pages/admin-dashboard.html`
- Installation: `http://localhost/honehube/backend/scripts/install.php`

### Support
For issues and questions:
1. Check this setup guide
2. Review [docs/TROUBLESHOOTING_INSTALL.md](docs/TROUBLESHOOTING_INSTALL.md)
3. Check browser console for errors
4. Verify XAMPP services are running
5. Test database connection

---

## Success Checklist

After setup, verify:

- [ ] XAMPP Apache is running
- [ ] XAMPP MySQL is running
- [ ] Database is created and populated
- [ ] Landing page loads correctly
- [ ] Can browse items
- [ ] Can register new account
- [ ] Can login as student
- [ ] Can login as admin
- [ ] Admin can create listings
- [ ] Student can send purchase requests
- [ ] Dashboard displays correctly
- [ ] Search and filters work
- [ ] reCAPTCHA is functional
- [ ] No console errors

---

**Setup Complete! 🎉**

Your HoneHube marketplace is now ready to use!

**Next Steps:**
1. Change default admin password
2. Add real items for sale
3. Invite students to register
4. Monitor system performance
5. Set up automated backups

---

**Version:** 1.0  
**Last Updated:** April 26, 2026  
**Status:** Production Ready ✅
