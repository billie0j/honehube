# ✅ Honehube Setup Checklist

## Pre-Installation

- [ ] XAMPP is installed on your computer
- [ ] You know where XAMPP is located (usually `C:\xampp\`)
- [ ] Project is in `C:\xampp\htdocs\honehube\`

---

## Step 1: Start Services

- [ ] Open XAMPP Control Panel
- [ ] Click "Start" next to **Apache**
- [ ] Wait for Apache to show green "Running"
- [ ] Click "Start" next to **MySQL**
- [ ] Wait for MySQL to show green "Running"

**Verify:** Both services show green "Running" status

---

## Step 2: Run Database Installation

- [ ] Open browser (Chrome, Firefox, Edge)
- [ ] Navigate to: `http://localhost/honehube/install.php`
- [ ] Page loads successfully (no errors)
- [ ] Click the "🚀 Install Database" button
- [ ] Wait for installation to complete (10-30 seconds)
- [ ] See green checkmarks for all steps:
  - [ ] ✓ MySQL Connection Successful
  - [ ] ✓ Database Created
  - [ ] ✓ Tables Created
  - [ ] ✓ Sample Data Inserted
  - [ ] ✓ Installation Complete
- [ ] Note down admin credentials:
  - Email: `admin@honehube.com`
  - Password: `Admin@123`

**Verify:** Installation shows "🎉 Installation Complete!" message

---

## Step 3: Test Login

- [ ] Navigate to: `http://localhost/honehube/login.html`
- [ ] Page loads with full-screen building image
- [ ] Login form is visible and centered
- [ ] Enter admin credentials:
  - Email: `admin@honehube.com`
  - Password: `Admin@123`
- [ ] Complete reCAPTCHA (click checkbox)
- [ ] Click "Login" button
- [ ] See "Login successful! Redirecting..." message
- [ ] Redirected to home page

**Verify:** Successfully logged in as admin

---

## Step 4: Test Registration

- [ ] Navigate to: `http://localhost/honehube/register.html`
- [ ] Registration form loads correctly
- [ ] Try registering a new user:
  - [ ] Enter your full name
  - [ ] Choose registration method (College Email, Student ID, or Gmail)
  - [ ] Enter email/student ID
  - [ ] Enter strong password (8+ chars, uppercase, lowercase, number, special char)
  - [ ] Complete reCAPTCHA
  - [ ] Click "Create Account"
- [ ] See "Account created successfully!" message
- [ ] Redirected to home page

**Verify:** New account created and logged in

---

## Step 5: Verify Database

- [ ] Navigate to: `http://localhost/phpmyadmin`
- [ ] phpMyAdmin loads successfully
- [ ] Click on "honehube" database in left sidebar
- [ ] Verify tables exist:
  - [ ] users
  - [ ] listings
  - [ ] inquiries
  - [ ] sessions
  - [ ] login_attempts
- [ ] Click on "users" table
- [ ] Click "Browse" tab
- [ ] See at least 2 users (admin + your test user)

**Verify:** Database has all tables and data

---

## Step 6: Test Hybrid System

### Test with Database (API Mode)
- [ ] XAMPP MySQL is running
- [ ] Open browser console (F12)
- [ ] Reload login page
- [ ] Check console for: "✓ Using MySQL database backend"
- [ ] Login works correctly

### Test without Database (Fallback Mode)
- [ ] Stop MySQL in XAMPP
- [ ] Reload login page
- [ ] Check console for: "⚠ API not available, using localStorage fallback"
- [ ] System still works (uses localStorage)
- [ ] Start MySQL again

**Verify:** System works in both modes

---

## Step 7: Security Cleanup

- [ ] Delete `install.php` file:
  ```powershell
  Remove-Item C:\xampp\htdocs\honehube\install.php
  ```
- [ ] Or manually delete from file explorer
- [ ] Verify file is deleted
- [ ] Try accessing `http://localhost/honehube/install.php`
- [ ] Should show 404 error (file not found)

**Verify:** install.php is deleted and inaccessible

---

## Step 8: Change Admin Password

- [ ] Login as admin
- [ ] Navigate to profile/settings page
- [ ] Change password from `Admin@123` to something secure
- [ ] Use strong password (8+ chars, mixed case, numbers, special chars)
- [ ] Save changes
- [ ] Logout
- [ ] Login with new password
- [ ] Verify new password works

**Verify:** Admin password changed successfully

---

## Step 9: Test Features

### Browse Listings
- [ ] Navigate to home page
- [ ] See 5 sample laptop listings
- [ ] Click on a listing
- [ ] Listing details page loads

### Search
- [ ] Use search bar on home page
- [ ] Search for "Dell"
- [ ] Results filter correctly

### Categories
- [ ] Click on different category tabs
- [ ] Listings filter by category

### Logout
- [ ] Click logout button
- [ ] Redirected to login page
- [ ] Session cleared

**Verify:** All basic features work

---

## Step 10: Final Verification

- [ ] XAMPP services are running
- [ ] Database exists and has data
- [ ] Can login with admin account
- [ ] Can register new users
- [ ] Can browse listings
- [ ] install.php is deleted
- [ ] Admin password is changed
- [ ] No console errors in browser
- [ ] System works smoothly

**Verify:** Everything is working perfectly! 🎉

---

## Troubleshooting

### ❌ "Cannot access localhost"
**Solution:** Start Apache in XAMPP

### ❌ "Database connection failed"
**Solution:** Start MySQL in XAMPP

### ❌ "Port 80 already in use"
**Solution:** Stop Skype, IIS, or other programs using port 80

### ❌ "Port 3306 already in use"
**Solution:** Stop other MySQL instances

### ❌ "Access denied for user 'root'"
**Solution:** Check password in `api/config.php` (default is empty)

### ❌ "CSRF token validation failed"
**Solution:** Clear browser cookies and refresh page

### ❌ "reCAPTCHA not loading"
**Solution:** Check internet connection (reCAPTCHA requires internet)

---

## Success Criteria

✅ **All checkboxes above are checked**  
✅ **No errors in browser console**  
✅ **Can login and register users**  
✅ **Database has data**  
✅ **install.php is deleted**  
✅ **Admin password is changed**  

---

## What's Next?

1. **Customize** - Add your own listings and content
2. **Style** - Customize colors and design
3. **Features** - Add more functionality
4. **Deploy** - Move to production server
5. **Backup** - Set up database backups

---

## Need Help?

📖 **Read the guides:**
- `QUICK_START.md` - Quick setup guide
- `INSTALLATION_GUIDE.md` - Detailed instructions
- `DATABASE_SETUP.md` - Database documentation
- `SECURITY_FEATURES.md` - Security information

🐛 **Check logs:**
- Apache: `C:\xampp\apache\logs\error.log`
- MySQL: `C:\xampp\mysql\data\*.err`
- Browser: Press F12 → Console tab

---

**Completion Time:** ~10 minutes  
**Difficulty:** Easy  
**Prerequisites:** XAMPP installed

🎉 **Congratulations on setting up Honehube!**
