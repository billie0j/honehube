# Dashboard Guide

## Overview
Honehube now includes two comprehensive dashboards for different user roles.

---

## 🎯 User Dashboard (`dashboard.html`)

### Access
- **URL:** `http://localhost/honehube/dashboard.html`
- **Who:** Regular users (buyers/sellers)
- **Login Required:** Yes

### Features

#### 📊 Statistics Overview
- **My Listings** - Total number of your listings
- **Active** - Currently active listings
- **Sold** - Successfully sold items
- **Inquiries** - Messages from buyers

#### 📦 My Listings Management
- **View All Listings** - See all your posted items
- **Filter by Status:**
  - All listings
  - Active listings
  - Sold items
  - Inactive listings
- **Actions:**
  - View listing details
  - Edit listing
  - Delete listing

#### 👤 Profile Section
- **View Profile Information:**
  - Full name
  - Email address
  - Student ID
  - Account role
  - Member since date
  - Last login time
- **Profile Actions:**
  - Edit profile
  - Change password

#### ➕ Quick Actions
- Browse marketplace
- Create new listing
- View all listings by status

---

## 👨‍💼 Admin Dashboard (`admin-dashboard.html`)

### Access
- **URL:** `http://localhost/honehube/admin-dashboard.html`
- **Who:** Admin users only
- **Login Required:** Yes (admin role)
- **Default Admin:**
  - Email: `admin@honehube.com`
  - Password: `Admin@123`

### Features

#### 📊 System Statistics
- **Total Users** - All registered users
- **Active Listings** - Currently active products
- **Total Value** - Sum of all listing prices
- **Inquiries** - Total buyer inquiries

#### 👥 User Management
- **View All Users:**
  - User ID
  - Full name
  - Email address
  - Student ID
  - Role (admin/user)
  - Join date
  - Account status
- **Actions:**
  - View user details
  - Edit user information
  - Delete user (except admins)
- **Export:** Download user data

#### 📦 Listings Management
- **View All Listings:**
  - Listing ID
  - Title
  - Category
  - Price
  - Condition (new/used)
  - Seller name
  - Status (active/sold/inactive)
- **Actions:**
  - View listing details
  - Edit listing
  - Delete listing
- **Export:** Download listings data

#### 📧 Inquiries Management
- **View All Inquiries:**
  - Inquiry ID
  - Listing title
  - Buyer name
  - Seller name
  - Message content
  - Status (pending/replied/closed)
  - Date sent
- **Actions:**
  - View inquiry details
  - Mark as replied/closed

#### ⚡ Quick Actions
- Manage users
- Manage listings
- View inquiries
- Generate reports

---

## 🎨 Dashboard Features

### Common Features (Both Dashboards)

#### Navigation
- **Home** - Return to marketplace
- **Listings** - Browse all products
- **Dashboard** - Current page
- **Logout** - Sign out

#### Responsive Design
- Mobile-friendly layout
- Adaptive grid system
- Touch-friendly buttons

#### Real-time Updates
- Statistics update automatically
- Listings refresh on actions
- Instant feedback on operations

---

## 🚀 How to Use

### For Regular Users

1. **Login to Your Account**
   ```
   http://localhost/honehube/login.html
   ```

2. **Access Dashboard**
   - Click "📊 My Dashboard" in navigation
   - Or visit: `http://localhost/honehube/dashboard.html`

3. **View Your Statistics**
   - See total listings count
   - Check active/sold items
   - Monitor inquiries

4. **Manage Listings**
   - Click on tabs to filter (All/Active/Sold/Inactive)
   - Use action buttons to View/Edit/Delete
   - Create new listings with "➕ New Listing"

5. **Update Profile**
   - Scroll to Profile section
   - Click "✏️ Edit Profile"
   - Change password with "🔒 Change Password"

### For Admins

1. **Login as Admin**
   ```
   Email: admin@honehube.com
   Password: Admin@123
   ```

2. **Access Admin Dashboard**
   - Click "📊 Admin Dashboard" in navigation
   - Or visit: `http://localhost/honehube/admin-dashboard.html`

3. **Monitor System**
   - View total users, listings, and revenue
   - Check system health statistics
   - Track growth metrics

4. **Manage Users**
   - Scroll to "👥 Recent Users" section
   - View user details
   - Edit or delete users as needed
   - Export user data for reports

5. **Manage Listings**
   - Scroll to "📦 Recent Listings" section
   - Monitor all marketplace listings
   - Edit or remove inappropriate content
   - Export listings data

6. **Handle Inquiries**
   - Scroll to "📧 Recent Inquiries" section
   - View buyer-seller communications
   - Monitor inquiry status

---

## 🔒 Security & Access Control

### User Dashboard
- ✅ Requires login
- ✅ Users can only see their own listings
- ✅ Users can only edit/delete their own items
- ✅ Profile data is private

### Admin Dashboard
- ✅ Requires admin role
- ✅ Non-admin users are redirected
- ✅ Full access to all data
- ✅ Can manage all users and listings
- ✅ Cannot delete other admin accounts

---

## 📱 Responsive Design

### Desktop (1200px+)
- Full grid layout
- Multiple columns
- All features visible

### Tablet (768px - 1199px)
- Adaptive grid
- 2-column layout
- Optimized spacing

### Mobile (< 768px)
- Single column
- Stacked cards
- Touch-friendly buttons
- Simplified navigation

---

## 🎯 Dashboard Statistics

### User Dashboard Metrics
- **My Listings** - Count of all your listings
- **Active** - Listings currently available
- **Sold** - Successfully sold items
- **Inquiries** - Messages received

### Admin Dashboard Metrics
- **Total Users** - All registered accounts
- **Active Listings** - Currently available products
- **Total Value** - Sum of all listing prices (in Kwacha)
- **Inquiries** - Total buyer inquiries

---

## 🔧 Customization

### Changing Dashboard Colors
Edit the inline styles in the dashboard HTML files:

```css
/* Primary gradient */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Change to your colors */
background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
```

### Adding New Statistics
1. Add new stat card in HTML
2. Update JavaScript to calculate metric
3. Display value in the card

### Customizing Tables
- Modify column headers in `<thead>`
- Update data display in JavaScript
- Add/remove action buttons

---

## 🐛 Troubleshooting

### "Access Denied" Error
**Problem:** Non-admin trying to access admin dashboard  
**Solution:** Login with admin credentials or use user dashboard

### Dashboard Not Loading
**Problem:** JavaScript errors  
**Solution:** Check browser console (F12) for errors

### Statistics Showing Zero
**Problem:** No data in localStorage/database  
**Solution:** 
- Create some listings
- Register more users
- Ensure database is populated

### Listings Not Appearing
**Problem:** User ID mismatch  
**Solution:** 
- Check if listings have correct user_id
- Verify current user is logged in
- Clear browser cache

---

## 📊 Future Enhancements

### Planned Features
- [ ] Charts and graphs for statistics
- [ ] Export data to CSV/Excel
- [ ] Advanced filtering and search
- [ ] Bulk actions (delete multiple items)
- [ ] Email notifications
- [ ] Activity logs
- [ ] User analytics
- [ ] Revenue reports
- [ ] Messaging system
- [ ] Image upload for listings

---

## 🎓 Best Practices

### For Users
1. Keep your profile updated
2. Respond to inquiries promptly
3. Mark items as sold when completed
4. Use clear titles and descriptions
5. Set competitive prices

### For Admins
1. Monitor user activity regularly
2. Remove inappropriate content quickly
3. Respond to user reports
4. Keep statistics updated
5. Export data for backups
6. Review inquiries for issues

---

## 📞 Support

### Need Help?
- Check browser console for errors
- Review `SECURITY_FEATURES.md` for security info
- See `DATABASE_SETUP.md` for database issues
- Read `INSTALLATION_GUIDE.md` for setup help

### Common Issues
- **Can't login:** Check credentials, clear cookies
- **Dashboard blank:** Check if logged in
- **Actions not working:** Refresh page, check console
- **Data not saving:** Check database connection

---

## 🎉 Quick Start

### User Dashboard
1. Login to your account
2. Click "📊 My Dashboard"
3. View your statistics
4. Manage your listings
5. Update your profile

### Admin Dashboard
1. Login as admin
2. Click "📊 Admin Dashboard"
3. Monitor system statistics
4. Manage users and listings
5. Review inquiries

---

**Dashboard Version:** 1.0  
**Last Updated:** 2026-04-25  
**Compatible With:** Honehube v1.0+
