# 🔐 HoneHube Login Credentials

**Quick Reference for Testing**

---

## 👨‍💼 Admin Account (Default)

### Credentials:
- **Email:** `admin@honehube.com`
- **Password:** `Admin@123`

### Access:
- **Login URL:** http://localhost:8080/honehube/frontend/pages/login.html
- **Dashboard:** http://localhost:8080/honehube/frontend/pages/admin-dashboard.html

### Features:
- ✅ Full admin access
- ✅ Manage users
- ✅ Manage listings (24 products)
- ✅ View/manage purchase requests
- ✅ View/manage complaints
- ✅ Create new listings
- ✅ Mark items as sold
- ✅ Access reports

---

## 👨‍🎓 Student Account

### Option 1: Register New Account
1. Go to: http://localhost:8080/honehube/frontend/pages/register.html
2. Fill in the form:
   - Full Name: Your Name
   - Email: youremail@example.com
   - Password: YourPassword@123
   - Student ID: B123456 (optional)
3. Click Register
4. Login with your credentials

### Option 2: Use Test Account (if created)
- **Email:** Your registered email
- **Password:** Your registered password

### Access:
- **Login URL:** http://localhost:8080/honehube/frontend/pages/login.html
- **Dashboard:** http://localhost:8080/honehube/frontend/pages/dashboard.html

### Features:
- ✅ Browse items
- ✅ Send purchase requests
- ✅ Negotiate prices
- ✅ Submit complaints
- ✅ View request status
- ✅ Track negotiations

---

## 🎯 Quick Login Steps

### For Admin:
```
1. Go to: http://localhost:8080/honehube/frontend/pages/login.html
2. Enter: admin@honehube.com
3. Enter: Admin@123
4. Click Login
5. Redirects to: Admin Dashboard with sidebar
```

### For Student:
```
1. Register first (if new)
2. Go to: http://localhost:8080/honehube/frontend/pages/login.html
3. Enter your email
4. Enter your password
5. Click Login
6. Redirects to: Student Dashboard with sidebar
```

---

## 🔒 Password Requirements

When registering or changing passwords:
- **Minimum:** 8 characters
- **Must contain:**
  - At least 1 uppercase letter (A-Z)
  - At least 1 lowercase letter (a-z)
  - At least 1 number (0-9)
  - At least 1 special character (@#$%^&+=!)

### Valid Examples:
- `Admin@123` ✅
- `Student@2024` ✅
- `MyPass#456` ✅
- `Secure$789` ✅

### Invalid Examples:
- `password` ❌ (no uppercase, number, special char)
- `PASSWORD` ❌ (no lowercase, number, special char)
- `Pass123` ❌ (no special character)
- `Pass@` ❌ (too short, less than 8 chars)

---

## 🎨 Test the New Sidebar

### After Login as Admin:
1. **Look for purple button (☰)** in top-left
2. **Click it** → Sidebar opens
3. **See menu items:**
   - 📊 Dashboard
   - 👥 Users [count]
   - 📦 Listings [count]
   - 📧 Requests [count]
   - 📋 Complaints [count]
   - 📈 Reports
   - ➕ New Listing
   - 🏠 Browse Items
   - 🚪 Logout

### After Login as Student:
1. **Look for purple button (☰)** in top-left
2. **Click it** → Sidebar opens
3. **See menu items:**
   - 📊 Dashboard
   - 📧 My Requests [count]
   - 📋 My Complaints [count]
   - 👤 My Profile
   - 🏠 Browse Items
   - ➕ New Listing
   - 🚪 Logout

---

## ⚠️ Important Security Notes

### For Production:
1. **Change default admin password immediately!**
   - Current: `Admin@123`
   - Change to: Strong unique password

2. **Use strong passwords:**
   - Minimum 12 characters
   - Mix of upper/lower/numbers/symbols
   - Unique for each account

3. **Enable HTTPS:**
   - Get SSL certificate
   - Force HTTPS redirect
   - Update .htaccess

4. **Database Security:**
   - Change database password
   - Restrict database access
   - Use prepared statements (already done)

---

## 🧪 Testing Scenarios

### Test Admin Features:
```
1. Login as admin
2. Go to Admin Dashboard
3. Click sidebar → Users
4. View user list
5. Click sidebar → Listings
6. View all 24 products
7. Click sidebar → Requests
8. View purchase requests
9. Test accepting/denying requests
10. Test creating new listing
```

### Test Student Features:
```
1. Register new student account
2. Login with student credentials
3. Go to Student Dashboard
4. Click sidebar → My Requests
5. Browse items
6. Send purchase request
7. Check request status
8. Submit complaint (if needed)
9. View profile
```

---

## 📊 Default System Data

### Pre-loaded Products: 24
- **Laptops:** 4 items
- **Phones:** 3 items
- **RAM:** 2 items
- **Storage:** 2 items
- **Chargers:** 2 items
- **Accessories:** 9 items
- **Monitors:** 1 item
- **Adapters:** 1 item

### Pre-loaded Users: 1
- **Admin:** admin@honehube.com

### Database Tables: 10
- users
- accessories
- purchase_requests
- complaints
- sessions
- login_attempts
- account_lockouts
- audit_logs
- negotiations
- listings

---

## 🔄 Password Reset (Future Feature)

Currently not implemented. To reset password:
1. **Admin:** Manually update in database
2. **Student:** Contact admin

**SQL to reset password:**
```sql
-- Reset to Admin@123
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@honehube.com';
```

---

## 📝 Quick Reference Card

```
┌─────────────────────────────────────┐
│     HONEHUBE LOGIN CREDENTIALS      │
├─────────────────────────────────────┤
│                                     │
│ ADMIN ACCOUNT:                      │
│ Email: admin@honehube.com           │
│ Password: Admin@123                 │
│                                     │
│ LOGIN URL:                          │
│ http://localhost:8080/honehube/    │
│ frontend/pages/login.html           │
│                                     │
│ ADMIN DASHBOARD:                    │
│ http://localhost:8080/honehube/    │
│ frontend/pages/admin-dashboard.html │
│                                     │
│ FEATURES:                           │
│ ✅ Collapsing Sidebar               │
│ ✅ 24 Pre-loaded Products           │
│ ✅ User Management                  │
│ ✅ Request Management               │
│ ✅ Complaint System                 │
│                                     │
└─────────────────────────────────────┘
```

---

## 🎉 Ready to Login!

**Admin Login:**
1. Go to: http://localhost:8080/honehube/frontend/pages/login.html
2. Email: `admin@honehube.com`
3. Password: `Admin@123`
4. Click Login
5. See the new sidebar! Click the purple (☰) button

**Student Registration:**
1. Go to: http://localhost:8080/honehube/frontend/pages/register.html
2. Fill in your details
3. Create account
4. Login and test student dashboard

---

**Status:** ✅ Ready to Use  
**Port:** 8080  
**Default Admin:** admin@honehube.com / Admin@123  
**Sidebar:** ✅ Implemented  
**Products:** 24 items loaded  

