# HoneHube - Deployment Instructions 🚀

**Last Updated:** April 26, 2026  
**Current Version:** 2.3  
**Total Products:** 20

---

## Quick Deployment Guide

### Step 1: Import Updated Database

```bash
# Navigate to project directory
cd /path/to/honehube

# Import the updated schema (includes all 20 products)
mysql -u root -p honehube < backend/database/schema.sql
```

**What This Does:**
- Creates/updates all 10 database tables
- Adds all 20 products to both `accessories` and `listings` tables
- Sets up default admin account
- Configures all security tables
- Includes latest bug fixes (speaker image path)

---

## Step 2: Verify Image Files

All product images should be in `frontend/assets/images/`:

```bash
# Check all images exist
ls -la frontend/assets/images/
```

**Required Images (20 total):**
- ✅ adapter.png
- ✅ building.png
- ✅ came.jpg (webcam)
- ✅ coolinpad.png
- ✅ ear.png ⭐ NEW
- ✅ iphone 15.webp
- ✅ landing.png
- ✅ lap1.png
- ✅ lap2.png
- ✅ market.jpg
- ✅ muse.jpg (wireless mouse)
- ✅ muse.webp
- ✅ power.png
- ✅ samsung A07.jpg
- ✅ spesker.png (speakers)
- ✅ stand.png
- ✅ stand1.png
- ✅ tri monitor.png
- ✅ wireless.png

---

## Step 3: Start XAMPP

### Windows:
```bash
# Using batch script
scripts/start-xampp.bat

# Or using PowerShell
scripts/Start-Honehube.ps1
```

### Manual Start:
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL
4. Verify both are running (green indicators)

---

## Step 4: Access the Application

### URLs:
- **Landing Page:** `http://localhost/honehube/`
- **Home Page:** `http://localhost/honehube/frontend/pages/home.html`
- **Login:** `http://localhost/honehube/frontend/pages/login.html`
- **Register:** `http://localhost/honehube/frontend/pages/register.html`

### Default Admin Credentials:
- **Email:** admin@honehube.com
- **Password:** Admin@123

---

## Step 5: Verification Testing

### Test 1: Product Display
1. Navigate to home page
2. Verify all 20 products display
3. Check images load correctly
4. Verify prices show in Kwacha (K)

### Test 2: New Product (Wired Earphones)
1. Look for "Wired Earphones" in Accessories
2. Verify price shows K120
3. Check ear.png image displays
4. Click to view details

### Test 3: Purchase Request
1. Login as student (or register new account)
2. Browse to any product
3. Submit purchase request
4. Verify request appears in dashboard

### Test 4: Admin Functions
1. Login as admin
2. Navigate to admin dashboard
3. Verify all 20 products listed
4. Test "Mark as Sold" button
5. Test "Mark as Available" button
6. Verify statistics update

### Test 5: Complaints System
1. Login as student
2. Submit a complaint
3. Login as admin
4. View and respond to complaint
5. Update complaint status

---

## Common Issues & Solutions

### Issue 1: Database Connection Error
**Error:** "Could not connect to database"

**Solution:**
```bash
# Check MySQL is running
# Verify credentials in backend/config/config.php
# Default: username=root, password=empty, database=honehube
```

### Issue 2: Images Not Loading
**Error:** Broken image icons

**Solution:**
```bash
# Check image paths in database
# Verify images exist in frontend/assets/images/
# Check file permissions
# Clear browser cache
```

### Issue 3: Session Expired Immediately
**Error:** "Session expired" on every page

**Solution:**
```php
// Check backend/config/config.php
// Verify SESSION_LIFETIME is set to 600 (10 minutes)
// Check PHP session configuration
```

### Issue 4: HTTPS Redirect Loop
**Error:** Too many redirects

**Solution:**
```php
// In backend/config/config.php
// Set FORCE_HTTPS to false for local development
define('FORCE_HTTPS', false);
```

---

## Security Checklist

Before going live, verify:

- [ ] HTTPS enabled (set FORCE_HTTPS to true)
- [ ] Strong admin password set
- [ ] Database credentials secured
- [ ] Session timeout configured (10 minutes)
- [ ] Account lockout active (5 attempts/30 min)
- [ ] CSRF protection enabled
- [ ] Input sanitization working
- [ ] SQL injection prevention active
- [ ] Audit logging enabled
- [ ] File permissions correct
- [ ] Error reporting disabled in production
- [ ] Backup system configured

---

## Database Backup

### Create Backup:
```bash
# Using MySQL command
mysqldump -u root -p honehube > backup_$(date +%Y%m%d).sql

# Using PHP script
php backend/scripts/backup_database.php

# Using batch script (Windows)
scripts/backup-database.bat
```

### Restore Backup:
```bash
mysql -u root -p honehube < backup_20260426.sql
```

---

## Performance Optimization

### Recommended Settings:

**PHP (php.ini):**
```ini
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
```

**MySQL (my.ini):**
```ini
max_connections = 100
innodb_buffer_pool_size = 256M
query_cache_size = 64M
```

---

## Monitoring

### Check System Health:

**Database:**
```sql
-- Check table sizes
SELECT 
    table_name, 
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)"
FROM information_schema.TABLES 
WHERE table_schema = "honehube";

-- Check record counts
SELECT 'users' AS table_name, COUNT(*) AS records FROM users
UNION ALL
SELECT 'accessories', COUNT(*) FROM accessories
UNION ALL
SELECT 'listings', COUNT(*) FROM listings
UNION ALL
SELECT 'purchase_requests', COUNT(*) FROM purchase_requests
UNION ALL
SELECT 'complaints', COUNT(*) FROM complaints;
```

**Logs:**
```bash
# Check Apache error log
tail -f /xampp/apache/logs/error.log

# Check PHP error log
tail -f /xampp/php/logs/php_error_log
```

---

## Production Deployment

### Additional Steps for Production:

1. **Update Configuration:**
   ```php
   // backend/config/config.php
   define('ENVIRONMENT', 'production');
   define('FORCE_HTTPS', true);
   define('DEBUG_MODE', false);
   ```

2. **Secure Database:**
   - Change default admin password
   - Use strong database password
   - Restrict database access to localhost

3. **Enable HTTPS:**
   - Install SSL certificate
   - Configure Apache for HTTPS
   - Test redirect from HTTP to HTTPS

4. **Set File Permissions:**
   ```bash
   # Linux/Unix
   chmod 755 backend/
   chmod 644 backend/config/config.php
   chmod 755 frontend/
   ```

5. **Configure Backups:**
   - Set up automated daily backups
   - Store backups off-server
   - Test restore procedure

6. **Enable Monitoring:**
   - Set up uptime monitoring
   - Configure error notifications
   - Monitor disk space
   - Track database growth

---

## Support Resources

### Documentation:
- `README.md` - Project overview
- `docs/MASTER_SUMMARY.md` - Complete system summary
- `docs/INSTALLATION_GUIDE.md` - Detailed installation
- `docs/TESTING_GUIDE.md` - Testing procedures
- `docs/TROUBLESHOOTING_INSTALL.md` - Common issues

### User Guides:
- `docs/ADMIN_GUIDE.md` - Admin manual
- `docs/STUDENT_GUIDE.md` - Student manual
- `docs/DASHBOARD_GUIDE.md` - Dashboard features

### Technical Docs:
- `docs/COMPLETE_SYSTEM_SUMMARY.md` - Technical details
- `docs/SECURITY_FEATURES.md` - Security overview
- `docs/DATABASE_SETUP.md` - Database configuration

---

## Quick Reference

### System Statistics:
- **Products:** 20
- **Categories:** 9
- **Features:** 27 (14 admin + 13 student)
- **Security Features:** 12
- **Database Tables:** 10
- **API Endpoints:** ~36

### Latest Updates:
- **Task 14:** Wired Earphones added (K120)
- **Task 13:** USB Laptop Speakers added (K280)
- **Task 12:** Wireless Mouse added (K150)
- **Task 11:** HD Laptop Webcam added (K320)
- **Task 10:** New laptops & mark as sold feature

### Key Features:
- ✅ Product browsing & search
- ✅ Purchase requests & negotiation
- ✅ Complaints system
- ✅ Mark as sold/available
- ✅ User management
- ✅ Admin dashboard
- ✅ Student dashboard
- ✅ Complete security suite

---

## Contact & Support

For issues or questions:
1. Check documentation in `docs/` folder
2. Review troubleshooting guide
3. Check system logs
4. Verify configuration settings

---

**Deployment Version:** 2.3  
**Last Updated:** April 26, 2026  
**Status:** ✅ Ready for Deployment

---

🚀 **HoneHube - Ready to Launch!** 🚀
