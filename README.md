# 🎓 HoneHube - Evelyn Hone College Marketplace

A secure web-based marketplace system where admins post accessories (laptops, chargers, phones, bags, earphones, books, etc.) and students can browse, request to buy, and negotiate prices.

![Status](https://img.shields.io/badge/status-production%20ready-brightgreen)
![Version](https://img.shields.io/badge/version-1.0-blue)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1)

---

## ✨ Features

### 👨‍💼 Admin Features
- ✅ Secure login with enterprise-level security
- ✅ Add, edit, and delete items for sale
- ✅ Upload item images and set prices
- ✅ View and manage student purchase requests
- ✅ Accept, deny, or counter-offer negotiations
- ✅ Mark items as sold
- ✅ Manage student accounts
- ✅ View complete negotiation history
- ✅ Dashboard with statistics

### 👨‍🎓 Student Features
- ✅ Secure registration and login
- ✅ Browse available accessories
- ✅ Search and filter items by category/condition
- ✅ View detailed item information
- ✅ Request to buy items
- ✅ Negotiate prices with sellers
- ✅ Track request status (pending, accepted, denied)
- ✅ View negotiation timeline
- ✅ Cancel requests
- ✅ Personal dashboard

### 🔒 Security Features
- ✅ Password hashing (bcrypt)
- ✅ CSRF token protection
- ✅ SQL injection prevention
- ✅ XSS prevention
- ✅ Rate limiting (5 attempts/15 min)
- ✅ Account lockout (5 attempts/30 min)
- ✅ Session security with ID regeneration
- ✅ Audit logging
- ✅ Database error hiding
- ✅ Foreign key constraints
- ✅ Data encryption (AES-256-CBC)
- ✅ reCAPTCHA integration

---

## 📁 Project Structure

```
honehube/
├── frontend/              # Frontend application
│   ├── pages/            # HTML pages
│   └── assets/           # CSS, JS, images
├── backend/              # Backend application
│   ├── api/             # API endpoints
│   ├── config/          # Configuration
│   ├── database/        # Database schema
│   └── scripts/         # Utility scripts
├── scripts/             # Startup & backup scripts
├── docs/                # Documentation
└── index.html           # Landing page
```

See [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) for detailed structure.

---

## 🚀 Quick Start

### Prerequisites
- XAMPP (Apache + MySQL + PHP 7.4+)
- Modern web browser
- Internet connection (for reCAPTCHA)

### Installation

1. **Install XAMPP**
   - Download from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install to `C:\xampp\`

2. **Clone/Download Project**
   ```bash
   cd C:\xampp\htdocs\
   git clone <repository-url> honehube
   ```

3. **Start Services**
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL
   
   Or use the quick start script:
   ```bash
   scripts\start-xampp.bat
   ```

4. **Run Installation Wizard**
   - Open browser: `http://localhost:8080/honehube/backend/scripts/install.php`
   - Click "Install Database"
   - Wait for success message

5. **Access Application**
   - Landing page: `http://localhost:8080/honehube/`
   - Browse items: `http://localhost:8080/honehube/frontend/pages/index.html`
   - Login: `http://localhost:8080/honehube/frontend/pages/login.html`

### Default Admin Credentials
```
Email: admin@honehube.com
Password: Admin@123
```
**⚠️ Change immediately after first login!**

---

## 📖 Documentation

Comprehensive guides available in the `docs/` directory:

### User Guides
- [Admin Guide](docs/ADMIN_GUIDE.md) - Complete admin user guide
- [Student Guide](docs/STUDENT_GUIDE.md) - Complete student user guide
- [Dashboard Guide](docs/DASHBOARD_GUIDE.md) - Dashboard usage

### Setup & Installation
- [Installation Guide](docs/INSTALLATION_GUIDE.md) - Detailed installation
- [Database Setup](docs/DATABASE_SETUP.md) - Database configuration
- [Quick Start](docs/QUICK_START.md) - Get started quickly
- [Setup Checklist](docs/SETUP_CHECKLIST.md) - Pre-launch checklist
- [Troubleshooting](docs/TROUBLESHOOTING_INSTALL.md) - Common issues

### Security
- [Security Features](docs/SECURITY_FEATURES.md) - Security overview
- [Final Security Guide](docs/FINAL_SECURITY_GUIDE.md) - Complete security guide
- [Enhanced Security](docs/ENHANCED_SECURITY.md) - Advanced security
- [reCAPTCHA Setup](docs/RECAPTCHA_SETUP.md) - reCAPTCHA configuration

### Development
- [Implementation Summary](docs/IMPLEMENTATION_SUMMARY.md) - Implementation details
- [Complete System Summary](docs/COMPLETE_SYSTEM_SUMMARY.md) - Full system overview
- [Database Migration](docs/DATABASE_MIGRATION.md) - Schema migration guide

---

## 🛠️ Technology Stack

### Frontend
- HTML5
- CSS3 (Responsive Design)
- Vanilla JavaScript (ES6+)
- Google reCAPTCHA v2

### Backend
- PHP 7.4+
- MySQL 5.7+
- PDO (PHP Data Objects)

### Server
- Apache 2.4+
- XAMPP (Development)

---

## 🔄 Hybrid Mode

The system operates in **hybrid mode** for maximum reliability:

- **Primary Mode:** MySQL database via PHP API
- **Fallback Mode:** LocalStorage (if database unavailable)
- **Automatic Detection:** System checks API availability on load
- **Seamless Switching:** No user intervention required

This ensures the system works even if:
- MySQL is not running
- Database connection fails
- API is temporarily unavailable

---

## 📊 Database Schema

### Tables (8)
1. **users** - User accounts (admin and students)
2. **accessories** - Items for sale
3. **purchase_requests** - Student purchase requests
4. **sessions** - Session management
5. **login_attempts** - Rate limiting
6. **account_lockouts** - Account security
7. **audit_logs** - Audit trail
8. **negotiations** - Negotiation history

See [backend/database/schema.sql](backend/database/schema.sql) for complete schema.

---

## 🔌 API Endpoints

### Authentication (`/backend/api/auth.php`)
- `POST ?action=register` - Register new user
- `POST ?action=login` - Login user
- `POST ?action=logout` - Logout user
- `GET ?action=user` - Get current user
- `GET ?action=csrf` - Get CSRF token

### Listings (`/backend/api/listings.php`)
- `GET ?action=list` - Get all listings
- `GET ?action=get&id={id}` - Get single listing
- `POST ?action=create` - Create listing (Admin)
- `POST ?action=update` - Update listing (Admin)
- `POST ?action=delete` - Delete listing (Admin)

### Purchase Requests (`/backend/api/requests.php`)
- `GET ?action=list` - Get all requests
- `GET ?action=get&id={id}` - Get single request
- `POST ?action=create` - Create request
- `POST ?action=accept` - Accept request (Admin)
- `POST ?action=deny` - Deny request (Admin)
- `POST ?action=counter` - Counter-offer (Admin)
- `POST ?action=cancel` - Cancel request

### Users (`/backend/api/users.php`)
- `GET ?action=list` - Get all users (Admin)
- `GET ?action=get&id={id}` - Get user details (Admin)
- `POST ?action=update` - Update user (Admin)
- `POST ?action=deactivate` - Deactivate user (Admin)
- `POST ?action=activate` - Activate user (Admin)
- `POST ?action=delete` - Delete user (Admin)

---

## 🔐 Security Best Practices

### For Production Deployment

1. **Database Security**
   ```sql
   CREATE USER 'honehube_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON honehube.* TO 'honehube_user'@'localhost';
   ```

2. **Update Configuration**
   ```php
   // backend/config/config.php
   define('DB_USER', 'honehube_user');
   define('DB_PASS', 'strong_password');
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

3. **Enable HTTPS**
   ```php
   ini_set('session.cookie_secure', 1);
   ```

4. **Update reCAPTCHA Keys**
   - Get keys from [Google reCAPTCHA](https://www.google.com/recaptcha/admin)
   - Update in `frontend/pages/login.html` and `register.html`

5. **Set Up Automated Backups**
   ```bash
   # Windows Task Scheduler
   scripts\backup-database.bat
   ```

---

## 🧪 Testing

### Admin Testing
1. Login as admin: `admin@honehube.com` / `Admin@123`
2. Create a new listing
3. View purchase requests
4. Accept/deny/counter-offer
5. Mark item as sold
6. Manage users

### Student Testing
1. Register new account
2. Browse items
3. Search and filter
4. Send purchase request with offer
5. View request status
6. Check negotiation history

---

## 📈 Statistics

- **20 Features** implemented (10 admin + 10 student)
- **12 Security Features** implemented
- **7 API Files** created
- **8 Database Tables** with foreign keys
- **7 Main Pages** with responsive design
- **20+ API Endpoints** secured
- **1000+ Lines** of documentation

---

## 🐛 Troubleshooting

### Common Issues

**"API not available" message**
- Solution: Start MySQL in XAMPP Control Panel

**"404 Not Found" errors**
- Solution: Verify project is in `C:\xampp\htdocs\honehube\`

**"Can't create listing"**
- Solution: Verify you're logged in as admin

**Items not loading**
- Solution: Refresh page, check database connection

See [docs/TROUBLESHOOTING_INSTALL.md](docs/TROUBLESHOOTING_INSTALL.md) for more solutions.

---

## 🔄 Backup & Restore

### Backup Database
```bash
# Manual backup
scripts\backup-database.bat

# Or use PHP script
php backend\scripts\backup_database.php
```

### Restore Database
```bash
# Extract backup
gunzip backups\honehube_backup_2026-04-26_14-30-00.sql.gz

# Restore
mysql -u root -p honehube < backups\honehube_backup_2026-04-26_14-30-00.sql
```

---

## 🤝 Contributing

This is a college project. For improvements:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

---

## 📝 License

This project is created for Evelyn Hone College. All rights reserved.

---

## 👥 Credits

**Developed for:** Evelyn Hone College  
**Version:** 1.0  
**Release Date:** April 26, 2026  
**Status:** Production Ready ✅

---

## 📞 Support

For issues and questions:
1. Check documentation in `docs/` directory
2. Review troubleshooting guide
3. Check browser console for errors
4. Verify XAMPP services are running
5. Test database connection

---

## 🎯 Future Enhancements

Potential features for version 2.0:
- [ ] File upload for images
- [ ] Email notifications
- [ ] Real-time chat system
- [ ] Payment integration
- [ ] Review and rating system
- [ ] Wishlist functionality
- [ ] Mobile app (iOS/Android)
- [ ] Advanced analytics
- [ ] Bulk operations
- [ ] Image gallery (multiple images per item)

---

**Made with ❤️ for Evelyn Hone College Students**
