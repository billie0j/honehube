# Honehube Installation Guide

## Step-by-Step Installation

### Step 1: Start XAMPP Services ⚠️ REQUIRED

1. **Open XAMPP Control Panel**
   - Location: `C:\xampp\xampp-control.exe`
   - Or search "XAMPP" in Windows Start menu

2. **Start Apache**
   - Click "Start" button next to Apache
   - Wait until it shows "Running" in green

3. **Start MySQL**
   - Click "Start" button next to MySQL
   - Wait until it shows "Running" in green

**Screenshot of what you should see:**
```
Apache  [Running] [Port: 80, 443]
MySQL   [Running] [Port: 3306]
```

### Step 2: Run Database Installation

1. **Open your browser**

2. **Navigate to:**
   ```
   http://localhost/honehube/install.php
   ```

3. **Click the "🚀 Install Database" button**

4. **Wait for success message**
   - You should see green checkmarks for each step
   - Default admin credentials will be displayed

5. **Save the admin credentials:**
   - Email: `admin@honehube.com`
   - Password: `Admin@123`

### Step 3: Test the System

1. **Visit the login page:**
   ```
   http://localhost/honehube/login.html
   ```

2. **Try logging in with admin credentials:**
   - Email: `admin@honehube.com`
   - Password: `Admin@123`

3. **Test registration:**
   - Click "Register" link
   - Create a new account with your college email

### Step 4: Security Cleanup

After successful installation:

1. **Delete install.php:**
   ```powershell
   Remove-Item install.php
   ```
   Or manually delete the file from your project folder

2. **Change admin password:**
   - Login as admin
   - Go to profile settings
   - Change password to something secure

## Troubleshooting

### Issue: "Cannot access http://localhost/honehube/"

**Solution:**
1. Check if Apache is running in XAMPP
2. Verify your project is in `C:\xampp\htdocs\honehube\`
3. Try: `http://127.0.0.1/honehube/`

### Issue: "Database connection failed"

**Solution:**
1. Check if MySQL is running in XAMPP (green "Running" status)
2. Click "Admin" next to MySQL to open phpMyAdmin
3. If phpMyAdmin opens, MySQL is working
4. Check `api/config.php` for correct credentials

### Issue: "Access denied for user 'root'"

**Solution:**
1. Open `api/config.php`
2. Check the password:
   ```php
   define('DB_PASS', '');  // Empty for default XAMPP
   ```
3. If you set a MySQL password, add it here

### Issue: "Port 80 already in use"

**Solution:**
1. Another program is using port 80 (Skype, IIS, etc.)
2. Stop the conflicting program
3. Or change Apache port in XAMPP config

### Issue: "Port 3306 already in use"

**Solution:**
1. Another MySQL instance is running
2. Stop other MySQL services
3. Restart XAMPP MySQL

## Verification Checklist

After installation, verify:

- [ ] XAMPP Apache is running
- [ ] XAMPP MySQL is running
- [ ] Database "honehube" exists in phpMyAdmin
- [ ] Can access http://localhost/honehube/
- [ ] Can see login page
- [ ] Can login with admin credentials
- [ ] Can register new user
- [ ] install.php has been deleted

## Quick Commands

### Check if services are running:
```powershell
# Check Apache (port 80)
Test-NetConnection -ComputerName localhost -Port 80

# Check MySQL (port 3306)
Test-NetConnection -ComputerName localhost -Port 3306
```

### Access phpMyAdmin:
```
http://localhost/phpmyadmin
```

### Access your site:
```
http://localhost/honehube/
```

## What Happens During Installation

1. **Database Creation**
   - Creates database named "honehube"
   - Sets UTF-8 character encoding

2. **Table Creation**
   - Creates 5 tables (users, listings, inquiries, sessions, login_attempts)
   - Sets up foreign keys and indexes

3. **Sample Data**
   - Inserts admin user (password hashed with bcrypt)
   - Inserts 5 sample laptop listings

4. **Security Setup**
   - Configures session security
   - Sets up CSRF token system
   - Enables rate limiting

## Next Steps After Installation

1. **Update Frontend** (Already done in next step)
   - JavaScript will use API instead of localStorage
   - AJAX calls to PHP backend

2. **Test All Features**
   - Registration
   - Login/Logout
   - Browse listings
   - Create listings
   - Send inquiries

3. **Customize**
   - Add your own listings
   - Update admin profile
   - Customize styling

4. **Deploy** (Optional)
   - Move to production server
   - Update database credentials
   - Enable HTTPS
   - Update CORS settings

## Support

If you encounter issues:

1. Check XAMPP error logs:
   - Apache: `C:\xampp\apache\logs\error.log`
   - MySQL: `C:\xampp\mysql\data\*.err`

2. Check PHP errors in browser console

3. Review `DATABASE_SETUP.md` for detailed information

---

**Ready to start?** Make sure XAMPP is running, then visit:
```
http://localhost/honehube/install.php
```
