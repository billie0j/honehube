# HoneHube - Quick Reference Guide 🚀

**Last Updated:** April 26, 2026

---

## 📦 Current Products (14 Total)

| Product | Price | Category | Image |
|---------|-------|----------|-------|
| Phone Stand | K150 | Accessories | ✅ |
| Adjustable Laptop Stand | K180 | Accessories | ✅ |
| HP Laptop Charger | K250 | Chargers | - |
| Laptop Cooling Pad | K280 | Accessories | ✅ |
| Wireless Charger | K350 | Chargers | ✅ |
| Samsung 500GB SSD | K600 | Storage | - |
| Kingston 16GB RAM | K750 | RAM | - |
| Samsung Galaxy A07 | K1,800 | Phones | ✅ |
| Dell Latitude E7450 | K4,500 | Laptops | - |
| HP EliteBook 840 G5 | K5,200 | Laptops | ✅ |
| Dell Latitude 7490 | K5,400 | Laptops | ✅ |
| Lenovo ThinkPad T480 | K6,500 | Laptops | - |
| Triple Monitor Setup | K8,500 | Monitors | ✅ |
| iPhone 15 | K12,500 | Phones | ✅ |

---

## 🗂️ Project Structure

```
honehube/
├── backend/
│   ├── api/          # 6 API files
│   ├── config/       # Configuration
│   ├── database/     # schema.sql
│   └── scripts/      # Utilities
├── frontend/
│   ├── assets/
│   │   ├── css/      # 2 CSS files
│   │   ├── js/       # 3 JS files
│   │   └── images/   # 6 product images
│   └── pages/        # 7 HTML pages
├── docs/             # 31 documentation files
└── scripts/          # Startup scripts
```

---

## 🔐 Default Credentials

**Admin:**
- Email: `admin@honehube.com`
- Password: `Admin@123`

**Student:**
- Register new account at `/frontend/pages/register.html`

---

## 🚀 Quick Start

### 1. Database Setup
```bash
mysql -u root -p
CREATE DATABASE honehube;
exit
mysql -u root -p honehube < backend/database/schema.sql
```

### 2. Configuration
Edit `backend/config/config.php`:
- Database credentials
- Session timeout (currently 10 minutes)
- HTTPS settings

### 3. Start Server
- Start XAMPP (Apache + MySQL)
- Access: `http://localhost/honehube/`

---

## 📚 Key Documentation

### Getting Started:
- `docs/QUICK_START.md` - Quick start guide
- `docs/INSTALLATION_GUIDE.md` - Full installation
- `docs/SETUP_CHECKLIST.md` - Pre-launch checklist

### User Guides:
- `docs/ADMIN_GUIDE.md` - Admin manual
- `docs/STUDENT_GUIDE.md` - Student manual
- `docs/DASHBOARD_GUIDE.md` - Dashboard features

### Complete Overview:
- `docs/MASTER_SUMMARY.md` - **START HERE** - Complete project summary
- `docs/COMPLETE_SYSTEM_SUMMARY.md` - System overview

### Recent Updates:
- `docs/TASK_9_COMPLETE.md` - Latest update (Cooling Pad + Triple Monitor)
- `docs/TASK_8_COMPLETE.md` - Complaints system
- `docs/NEW_PRODUCTS_COOLING_PAD_TRI_MONITOR.md` - New products details

### Testing:
- `docs/TESTING_GUIDE.md` - Comprehensive testing guide
- `docs/TROUBLESHOOTING_INSTALL.md` - Common issues

---

## 🎯 Features Summary

### Admin (13 features):
✅ Manage listings  
✅ Manage purchase requests  
✅ Manage users  
✅ Manage complaints  
✅ Mark items as sold/available  

### Student (13 features):
✅ Browse products  
✅ Submit purchase requests  
✅ Track requests  
✅ Submit complaints  

---

## 🔒 Security Features (12)

✅ HTTPS Support  
✅ CSRF Protection  
✅ Session Management (10 min timeout)  
✅ Account Lockout (5 attempts / 30 min)  
✅ Password Hashing (Bcrypt)  
✅ Input Sanitization  
✅ SQL Injection Prevention  
✅ Authentication  
✅ Authorization  
✅ Audit Logging  
✅ Secure Sessions  
✅ Rate Limiting  

---

## 🌐 API Endpoints (36 total)

### Auth (5):
- POST `/auth.php?action=register`
- POST `/auth.php?action=login`
- POST `/auth.php?action=logout`
- GET `/auth.php?action=user`
- GET `/auth.php?action=csrf`

### Listings (5):
- GET `/listings.php?action=list`
- GET `/listings.php?action=get&id={id}`
- POST `/listings.php?action=create`
- POST `/listings.php?action=update`
- POST `/listings.php?action=delete`

### Requests (7):
- GET `/requests.php?action=list`
- GET `/requests.php?action=get&id={id}`
- POST `/requests.php?action=create`
- POST `/requests.php?action=accept`
- POST `/requests.php?action=deny`
- POST `/requests.php?action=counter`
- POST `/requests.php?action=cancel`

### Users (6):
- GET `/users.php?action=list`
- GET `/users.php?action=get&id={id}`
- POST `/users.php?action=update`
- POST `/users.php?action=deactivate`
- POST `/users.php?action=activate`
- POST `/users.php?action=delete`

### Complaints (6):
- POST `/complaints.php?action=submit`
- GET `/complaints.php?action=list`
- GET `/complaints.php?action=my_complaints`
- GET `/complaints.php?action=get&id={id}`
- POST `/complaints.php?action=update_status`
- POST `/complaints.php?action=delete`

---

## 💾 Database Tables (10)

1. **users** - User accounts
2. **accessories** - Items posted by admin
3. **listings** - Alternative listings
4. **purchase_requests** - Student requests
5. **sessions** - Session management
6. **login_attempts** - Rate limiting
7. **account_lockouts** - Locked accounts
8. **audit_logs** - Action tracking
9. **negotiations** - Price negotiation
10. **complaints** - Item issues

---

## 🎨 CSS Files

1. `frontend/assets/css/style.css` - Global styles
2. `frontend/assets/css/listing-detail.css` - Listing page styles

---

## 📱 Pages

1. `index.html` - Landing page
2. `frontend/pages/home.html` - Browse products
3. `frontend/pages/login.html` - Login
4. `frontend/pages/register.html` - Registration
5. `frontend/pages/listing.html` - Product details
6. `frontend/pages/dashboard.html` - Student dashboard
7. `frontend/pages/admin-dashboard.html` - Admin dashboard

---

## 🖼️ Product Images

Required images in `frontend/assets/images/`:
- ✅ `building.png` - Login background
- ✅ `landing.png` - Landing page background
- ✅ `iphone 15.webp` - iPhone 15 product
- ✅ `samsung A07.jpg` - Samsung phone
- ✅ `stand.png` - Phone stand
- ✅ `wireless.png` - Wireless charger
- ✅ `coolinpad.png` - Cooling pad
- ✅ `tri monitor.png` - Triple monitor
- ✅ `lap1.png` - HP EliteBook 840 G5
- ✅ `lap2.png` - Dell Latitude 7490
- ✅ `stand1.png` - Adjustable Laptop Stand

---

## ⚡ Common Commands

### Database Backup:
```bash
mysqldump -u root -p honehube > backup.sql
```

### Database Restore:
```bash
mysql -u root -p honehube < backup.sql
```

### Import Schema:
```bash
mysql -u root -p honehube < backend/database/schema.sql
```

### Check Products:
```sql
SELECT item_name, category, original_price FROM accessories;
```

---

## 🐛 Troubleshooting

### Database Connection Error:
- Check XAMPP MySQL is running
- Verify credentials in `backend/config/config.php`

### CSRF Token Error:
- Clear browser cache
- Logout and login again

### Images Not Loading:
- Check file paths
- Verify images exist in `frontend/assets/images/`

### Session Expired:
- Normal after 10 minutes of inactivity
- Login again

---

## 📊 Statistics

- **Total Products:** 14
- **Categories:** 7
- **Features:** 27
- **Security Features:** 12
- **API Endpoints:** 36
- **Database Tables:** 10
- **Documentation Files:** 32
- **CSS Files:** 2
- **JS Files:** 3
- **HTML Pages:** 7

---

## 🔄 Recent Changes

### Task 10 (April 26, 2026):
- ✅ Added HP EliteBook 840 G5 (K5,200)
- ✅ Added Dell Latitude 7490 (K5,400)
- ✅ Added Adjustable Laptop Stand (K180)
- ✅ Implemented Mark as Sold/Available feature

### Task 9 (April 26, 2026):
- ✅ Added Laptop Cooling Pad (K280)
- ✅ Added Triple Monitor Setup (K8,500)
- ✅ Created Monitors category
- ✅ Organized docs into docs/ folder
- ✅ Extracted CSS to external files

### Task 8 (April 26, 2026):
- ✅ Added Wireless Charger (K350)
- ✅ Implemented complaints system
- ✅ Added 6 API endpoints
- ✅ Enhanced dashboards

---

## 📞 Support

### For Issues:
1. Check `docs/TROUBLESHOOTING_INSTALL.md`
2. Review `docs/TESTING_GUIDE.md`
3. Check browser console for errors
4. Review PHP error logs in XAMPP

### For Security:
1. Review `docs/FINAL_SECURITY_GUIDE.md`
2. Check `docs/SECURITY_FEATURES.md`
3. Verify HTTPS configuration

---

## ✅ Pre-Launch Checklist

- [ ] Database imported
- [ ] Config file updated
- [ ] All images present
- [ ] HTTPS configured
- [ ] Admin account works
- [ ] Student registration works
- [ ] Products display correctly
- [ ] Purchase requests work
- [ ] Complaints system works
- [ ] Security features active
- [ ] Documentation reviewed

---

**Status:** ✅ Production Ready  
**Version:** 2.0  
**Last Updated:** April 26, 2026

---

🎉 **HoneHube - Empowering Evlyne Hone College Students!** 🎉
