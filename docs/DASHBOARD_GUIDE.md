# Dashboard Guide - Admin vs Student 📊

**Quick Reference:** Understanding the Two Separate Dashboards

---

## 🎯 Quick Answer

**YES, the dashboards are already separate!**

- **Admin Dashboard:** `admin-dashboard.html` (Management interface)
- **Student Dashboard:** `dashboard.html` (Buyer interface)

---

## 🔐 How Access Works

### When You Login:

**As Admin:**
```
Login → System checks role → role = 'admin' 
→ Navbar shows "Admin Dashboard" 
→ Click → Opens admin-dashboard.html
→ Full management access ✅
```

**As Student:**
```
Login → System checks role → role = 'student'
→ Navbar shows "My Dashboard"
→ Click → Opens dashboard.html
→ Personal dashboard access ✅
```

**If Student Tries Admin Dashboard:**
```
Student → Tries to open admin-dashboard.html
→ System checks role → role ≠ 'admin'
→ "Access denied" alert
→ Redirect to home page ❌
```

---

## 📊 Admin Dashboard

**File:** `frontend/pages/admin-dashboard.html`

### What Admins See:

```
┌─────────────────────────────────────────┐
│  🔧 ADMIN DASHBOARD                     │
│  Welcome, Admin User                    │
├─────────────────────────────────────────┤
│  📊 STATISTICS                          │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐  │
│  │Users │ │Items │ │Value │ │Reqs  │  │
│  │  25  │ │  24  │ │ K500 │ │  12  │  │
│  └──────┘ └──────┘ └──────┘ └──────┘  │
├─────────────────────────────────────────┤
│  📑 TABS                                │
│  [Users] [Listings] [Requests] [Complaints]
├─────────────────────────────────────────┤
│  👥 USER MANAGEMENT                     │
│  ┌─────────────────────────────────┐   │
│  │ Name    Email    Role   Actions │   │
│  │ John    john@    Student [Edit] │   │
│  │ Mary    mary@    Student [Edit] │   │
│  └─────────────────────────────────┘   │
├─────────────────────────────────────────┤
│  📦 LISTING MANAGEMENT                  │
│  [+ Add New Listing]                    │
│  ┌─────────────────────────────────┐   │
│  │ Item         Price    Actions   │   │
│  │ iPhone X     K5,800   [Edit]    │   │
│  │              [Delete] [Mark Sold]│   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
```

### Admin Features:
- ✅ View all statistics
- ✅ Manage users (view, edit, delete)
- ✅ Post new listings
- ✅ Edit/delete listings
- ✅ Mark items as sold/available
- ✅ View all purchase requests
- ✅ Accept/deny requests
- ✅ View all complaints
- ✅ Respond to complaints
- ✅ Update complaint status

---

## 🛍️ Student Dashboard (Buyer Dashboard)

**File:** `frontend/pages/dashboard.html`

### What Students See:

```
┌─────────────────────────────────────────┐
│  🛍️ MY DASHBOARD                        │
│  Welcome back, John!                    │
├─────────────────────────────────────────┤
│  📑 TABS                                │
│  [Browse] [My Requests] [Complaints] [Profile]
├─────────────────────────────────────────┤
│  🛍️ AVAILABLE LISTINGS                  │
│  ┌──────┐ ┌──────┐ ┌──────┐           │
│  │iPhone│ │Laptop│ │Mouse │           │
│  │K5,800│ │K4,500│ │K150  │           │
│  │[View]│ │[View]│ │[View]│           │
│  └──────┘ └──────┘ └──────┘           │
├─────────────────────────────────────────┤
│  📝 MY PURCHASE REQUESTS                │
│  ┌─────────────────────────────────┐   │
│  │ Item      Status    Date        │   │
│  │ iPhone X  Pending   Apr 26      │   │
│  │ Laptop    Accepted  Apr 25      │   │
│  └─────────────────────────────────┘   │
├─────────────────────────────────────────┤
│  📋 MY COMPLAINTS                       │
│  ┌─────────────────────────────────┐   │
│  │ Subject        Status           │   │
│  │ Screen issue   Investigating    │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
```

### Student Features:
- ✅ Browse available listings
- ✅ View item details
- ✅ Submit purchase requests
- ✅ View my purchase requests
- ✅ Track request status
- ✅ Submit complaints
- ✅ View my complaints
- ✅ Track complaint status
- ✅ View my profile
- ❌ Cannot manage users
- ❌ Cannot post listings
- ❌ Cannot edit/delete listings
- ❌ Cannot see other users' requests

---

## 🔄 Navigation Flow

### Navbar Changes Based on Role:

**Admin Navbar:**
```
[Home] [Browse] [📊 Admin Dashboard] [Logout]
                      ↑
                This link appears
```

**Student Navbar:**
```
[Home] [Browse] [📊 My Dashboard] [Logout]
                      ↑
                This link appears
```

---

## 🔒 Security Protection

### 1. Authentication Check
```javascript
// Both dashboards check if user is logged in
if (!user) {
  alert('Please login');
  redirect to login page
}
```

### 2. Role Verification (Admin Dashboard Only)
```javascript
// Admin dashboard checks role
if (user.role !== 'admin') {
  alert('Access denied. Admin privileges required.');
  redirect to home page
}
```

### 3. Backend API Protection
```php
// All admin APIs check role
if ($_SESSION['user_role'] !== 'admin') {
  return error('Unauthorized');
}
```

---

## 📱 Access URLs

### Admin Dashboard:
```
http://localhost/honehube/frontend/pages/admin-dashboard.html
```
- ✅ Accessible by: Admins only
- ❌ Blocked for: Students, guests

### Student Dashboard:
```
http://localhost/honehube/frontend/pages/dashboard.html
```
- ✅ Accessible by: All logged-in users
- ❌ Blocked for: Guests (not logged in)

---

## 🎨 Visual Differences

| Aspect | Admin Dashboard | Student Dashboard |
|--------|----------------|-------------------|
| **Title** | "Admin Dashboard" | "My Dashboard" |
| **Header Color** | Purple gradient | Purple gradient |
| **Stats Cards** | 4 cards (Users, Items, Revenue, Requests) | None |
| **Main Tabs** | Users, Listings, Inquiries, Complaints | Browse, My Requests, Complaints, Profile |
| **Action Buttons** | Edit, Delete, Approve, Deny, Mark Sold | View Details, Submit Request |
| **User List** | ✅ Shows all users | ❌ Not visible |
| **Listing Management** | ✅ Add/Edit/Delete | ❌ View only |
| **All Requests** | ✅ See everyone's | ❌ See only mine |
| **All Complaints** | ✅ See everyone's | ❌ See only mine |

---

## 🧪 Testing the Separation

### Test 1: Login as Student
```bash
1. Go to login page
2. Login with student account
3. Check navbar → Should see "My Dashboard"
4. Click "My Dashboard" → Opens dashboard.html
5. Try to access admin-dashboard.html directly
   → Should show "Access denied" and redirect
```

### Test 2: Login as Admin
```bash
1. Go to login page
2. Login with admin@honehube.com / Admin@123
3. Check navbar → Should see "Admin Dashboard"
4. Click "Admin Dashboard" → Opens admin-dashboard.html
5. Should see all management features
```

---

## 📋 Feature Checklist

### Admin Dashboard Features:
- [x] Statistics overview
- [x] User management
- [x] Listing management (CRUD)
- [x] Mark as sold/available
- [x] Purchase request management
- [x] Complaint management
- [x] Role-based access control
- [x] Security protection

### Student Dashboard Features:
- [x] Browse listings
- [x] Submit purchase requests
- [x] View my requests
- [x] Track request status
- [x] Submit complaints
- [x] View my complaints
- [x] Profile information
- [x] Authentication required

---

## 🚀 Quick Start

### For Admins:
1. Login with admin credentials
2. Click "📊 Admin Dashboard" in navbar
3. Manage users, listings, requests, complaints

### For Students:
1. Register or login with student account
2. Click "📊 My Dashboard" in navbar
3. Browse items, submit requests, track status

---

## ✅ Verification Checklist

- [x] Two separate dashboard files exist
- [x] Admin dashboard has role check
- [x] Student dashboard has auth check
- [x] Navbar shows correct link based on role
- [x] Direct URL access is protected
- [x] Different features for each role
- [x] Backend APIs verify permissions
- [x] Security alerts for unauthorized access

---

## 📞 Common Questions

**Q: Can students access the admin dashboard?**
A: No. If they try, they get "Access denied" and are redirected.

**Q: Can admins access the student dashboard?**
A: Yes, admins can view it, but admin features are only in admin dashboard.

**Q: How does the system know which dashboard to show?**
A: The navbar checks the user's role and displays the appropriate link.

**Q: What if someone tries to access a dashboard directly via URL?**
A: The dashboard checks authentication and role, then redirects if unauthorized.

**Q: Are the dashboards really separate?**
A: Yes! Two different HTML files with different features and access controls.

---

## 🎯 Summary

### ✅ Dashboards ARE Separate

**Admin Dashboard:**
- File: `admin-dashboard.html`
- Access: Admins only
- Features: Full management

**Student Dashboard:**
- File: `dashboard.html`
- Access: All logged-in users
- Features: Personal dashboard

**Security:** ✅ Active
**Separation:** ✅ Complete
**Working:** ✅ Perfectly

---

**Last Updated:** April 26, 2026  
**Status:** ✅ Verified and Working  
**Security:** 🔒 Protected

---

🎉 **Two Dashboards, Perfectly Separated!** 🎉
