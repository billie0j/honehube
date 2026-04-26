# 🚀 Quick Start Guide

## Get Honehube Running in 3 Steps

### Step 1: Start XAMPP (2 minutes)

1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache**
3. Click **Start** next to **MySQL**
4. Wait for both to show green "Running" status

### Step 2: Install Database (1 minute)

1. Open your browser
2. Go to: **http://localhost/honehube/install.php**
3. Click the **"🚀 Install Database"** button
4. Wait for success message (should see green checkmarks)

### Step 3: Test the System (1 minute)

1. Go to: **http://localhost/honehube/login.html**
2. Login with:
   - **Email:** admin@honehube.com
   - **Password:** Admin@123
3. You're in! 🎉

---

## What You Get

✅ **MySQL Database** - Professional backend storage  
✅ **PHP API** - Secure authentication system  
✅ **Hybrid Mode** - Works with or without database  
✅ **Security Features** - CSRF, bcrypt, rate limiting  
✅ **Sample Data** - 5 laptop listings pre-loaded  

---

## System Features

### 🔐 Authentication
- Secure login/registration
- Password hashing (bcrypt)
- CSRF token protection
- Rate limiting (5 attempts/15 min)
- Session management

### 📦 Database Tables
- **users** - User accounts
- **listings** - Product listings
- **inquiries** - Messages
- **sessions** - Session data
- **login_attempts** - Security tracking

### 🔄 Hybrid System
The system automatically detects if the database is available:
- **With Database:** Uses MySQL + PHP API
- **Without Database:** Falls back to localStorage

---

## Quick Commands

### Check if XAMPP is running:
```powershell
# Check Apache
Test-NetConnection localhost -Port 80

# Check MySQL
Test-NetConnection localhost -Port 3306
```

### Access phpMyAdmin:
```
http://localhost/phpmyadmin
```

### Access your site:
```
http://localhost/honehube/
```

---

## Troubleshooting

### "Cannot access localhost"
→ Start Apache in XAMPP

### "Database connection failed"
→ Start MySQL in XAMPP

### "Port already in use"
→ Stop conflicting programs (Skype, IIS, etc.)

---

## After Installation

### ✅ Security Cleanup
```powershell
# Delete installation file
Remove-Item install.php
```

### ✅ Change Admin Password
1. Login as admin
2. Go to profile settings
3. Change password

### ✅ Test Features
- Register new user
- Create listing
- Browse products
- Send inquiry

---

## File Structure

```
honehube/
├── api/
│   ├── config.php      # Database config
│   └── auth.php        # Authentication API
├── database/
│   └── schema.sql      # Database structure
├── js/
│   ├── api.js          # API client (NEW)
│   ├── store.js        # localStorage fallback
│   └── navbar.js       # Navigation
├── install.php         # Installation wizard
├── login.html          # Login page
└── register.html       # Registration page
```

---

## Next Steps

1. ✅ Database installed
2. ✅ Frontend updated to use API
3. ⬜ Delete install.php
4. ⬜ Change admin password
5. ⬜ Add your own listings
6. ⬜ Customize styling
7. ⬜ Deploy to production

---

## Need Help?

📖 **Detailed Guides:**
- `INSTALLATION_GUIDE.md` - Full installation instructions
- `DATABASE_SETUP.md` - Database documentation
- `SECURITY_FEATURES.md` - Security features explained

🐛 **Troubleshooting:**
- Check XAMPP logs: `C:\xampp\apache\logs\error.log`
- Check browser console for JavaScript errors
- Verify database exists in phpMyAdmin

---

**Ready?** Start XAMPP and visit:
```
http://localhost/honehube/install.php
```

🎉 **You'll be up and running in under 5 minutes!**
