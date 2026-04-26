# HoneHube Project Structure

## 📁 Directory Organization

```
honehube/
├── frontend/                    # Frontend application
│   ├── pages/                   # HTML pages
│   │   ├── index.html          # Browse items page
│   │   ├── login.html          # Login page
│   │   ├── register.html       # Registration page
│   │   ├── dashboard.html      # Student dashboard
│   │   ├── admin-dashboard.html # Admin dashboard
│   │   ├── listing.html        # Item detail page
│   │   └── home.html           # Landing page
│   └── assets/                 # Static assets
│       ├── css/                # Stylesheets
│       │   └── style.css       # Main stylesheet
│       ├── js/                 # JavaScript files
│       │   ├── api.js          # API client & hybrid store
│       │   ├── store.js        # LocalStorage fallback
│       │   └── navbar.js       # Navigation component
│       └── images/             # Image assets
│           ├── building.png    # College building
│           ├── market.jpg      # Campus market
│           ├── iphone 15.webp  # Sample product
│           └── samsung A07.jpg # Sample product
│
├── backend/                    # Backend application
│   ├── api/                    # API endpoints
│   │   ├── auth.php           # Authentication API
│   │   ├── listings.php       # Listings management API
│   │   ├── accessories.php    # Accessories API (new schema)
│   │   ├── requests.php       # Purchase requests API
│   │   └── users.php          # User management API
│   ├── config/                # Configuration files
│   │   └── config.php         # Database & security config
│   ├── database/              # Database files
│   │   └── schema.sql         # Database schema
│   └── scripts/               # Backend scripts
│       ├── install.php        # Installation wizard
│       └── backup_database.php # Database backup script
│
├── scripts/                    # Utility scripts
│   ├── backup-database.bat    # Windows backup script
│   ├── start-xampp.bat        # Start XAMPP services
│   └── Start-Honehube.ps1     # PowerShell startup script
│
├── docs/                       # Documentation
│   ├── README.md              # Project overview
│   ├── ADMIN_GUIDE.md         # Admin user guide
│   ├── STUDENT_GUIDE.md       # Student user guide
│   ├── INSTALLATION_GUIDE.md  # Installation instructions
│   ├── DATABASE_SETUP.md      # Database setup guide
│   ├── SECURITY_FEATURES.md   # Security documentation
│   ├── FINAL_SECURITY_GUIDE.md # Complete security guide
│   ├── ENHANCED_SECURITY.md   # Enhanced security features
│   ├── DASHBOARD_GUIDE.md     # Dashboard usage guide
│   ├── IMPLEMENTATION_SUMMARY.md # Implementation details
│   ├── COMPLETE_SYSTEM_SUMMARY.md # Full system overview
│   ├── TROUBLESHOOTING_INSTALL.md # Troubleshooting guide
│   ├── QUICK_START.md         # Quick start guide
│   ├── SETUP_CHECKLIST.md     # Setup checklist
│   ├── RECAPTCHA_SETUP.md     # reCAPTCHA configuration
│   ├── DATABASE_MIGRATION.md  # Migration guide
│   ├── SCHEMA_UPDATE_STATUS.md # Schema update status
│   └── GITHUB_PAGES_TROUBLESHOOTING.md # GitHub Pages guide
│
├── index.html                  # Main landing page
└── .git/                       # Git repository

```

---

## 🎯 File Purposes

### Frontend Files

#### Pages (`frontend/pages/`)
- **index.html** - Main marketplace page with item browsing, search, and filters
- **login.html** - User authentication with slideshow background
- **register.html** - New user registration with multiple email options
- **dashboard.html** - Student dashboard showing purchase requests and statistics
- **admin-dashboard.html** - Admin dashboard for managing items, requests, and users
- **listing.html** - Detailed item view with purchase request form
- **home.html** - Landing page with category navigation

#### Assets (`frontend/assets/`)
- **css/style.css** - Complete styling for all pages (responsive design)
- **js/api.js** - API client with hybrid mode (MySQL + localStorage fallback)
- **js/store.js** - LocalStorage data management
- **js/navbar.js** - Dynamic navigation bar component
- **images/** - Product images and background photos

### Backend Files

#### API (`backend/api/`)
- **auth.php** - User authentication (login, register, logout)
- **listings.php** - Item management (CRUD operations)
- **accessories.php** - Accessories API (simplified schema)
- **requests.php** - Purchase request handling and negotiation
- **users.php** - User account management (admin only)

#### Configuration (`backend/config/`)
- **config.php** - Database connection, security functions, audit logging

#### Database (`backend/database/`)
- **schema.sql** - Complete database schema with 8 tables

#### Scripts (`backend/scripts/`)
- **install.php** - One-click database installation wizard
- **backup_database.php** - Automated database backup with compression

### Utility Scripts (`scripts/`)
- **backup-database.bat** - Windows batch file for database backups
- **start-xampp.bat** - Quick XAMPP startup script
- **Start-Honehube.ps1** - PowerShell script to start all services

### Documentation (`docs/`)
- Complete guides for users, admins, developers, and system administrators

---

## 🔗 Path References

### Frontend to Backend API
```javascript
// In frontend/assets/js/api.js
baseURL: '/honehube/backend/api'  // Local development
baseURL: '/backend/api'            // Production
```

### Frontend Asset Paths
```html
<!-- In frontend/pages/*.html -->
<link rel="stylesheet" href="../assets/css/style.css" />
<script src="../assets/js/api.js"></script>
<script src="../assets/js/store.js"></script>
<script src="../assets/js/navbar.js"></script>
<img src="../assets/images/building.png" />
```

### Backend Configuration Paths
```php
// In backend/api/*.php
require_once '../config/config.php';
```

---

## 🚀 Access URLs

### Development (XAMPP)
- **Landing Page:** `http://localhost/honehube/`
- **Browse Items:** `http://localhost/honehube/frontend/pages/index.html`
- **Login:** `http://localhost/honehube/frontend/pages/login.html`
- **Register:** `http://localhost/honehube/frontend/pages/register.html`
- **Admin Dashboard:** `http://localhost/honehube/frontend/pages/admin-dashboard.html`
- **Student Dashboard:** `http://localhost/honehube/frontend/pages/dashboard.html`
- **Installation:** `http://localhost/honehube/backend/scripts/install.php`

### API Endpoints
- **Auth API:** `http://localhost/honehube/backend/api/auth.php`
- **Listings API:** `http://localhost/honehube/backend/api/listings.php`
- **Requests API:** `http://localhost/honehube/backend/api/requests.php`
- **Users API:** `http://localhost/honehube/backend/api/users.php`

---

## 📊 Database Tables

1. **users** - User accounts (admin and students)
2. **accessories** - Items for sale
3. **purchase_requests** - Student purchase requests
4. **sessions** - Session management
5. **login_attempts** - Rate limiting
6. **account_lockouts** - Account security
7. **audit_logs** - Audit trail
8. **negotiations** - Negotiation history (optional)

---

## 🔒 Security Features

### Implemented in Backend
- Password hashing (bcrypt)
- CSRF token protection
- SQL injection prevention (prepared statements)
- XSS prevention (input sanitization)
- Rate limiting (5 attempts/15 min)
- Account lockout (5 attempts/30 min)
- Session security (ID regeneration)
- Audit logging
- Database error hiding
- Foreign key constraints
- Data encryption (AES-256-CBC)

### Implemented in Frontend
- Client-side validation
- reCAPTCHA integration
- Secure password requirements
- CSRF token management
- Generic error messages

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

## 📝 Key Features

### Admin Features (10/10)
1. ✅ Login securely
2. ✅ Add accessories/items for sale
3. ✅ Set original price
4. ✅ Upload item image
5. ✅ Edit or delete items
6. ✅ View student purchase requests
7. ✅ View negotiated prices
8. ✅ Accept or deny student offers
9. ✅ Mark item as sold
10. ✅ Manage student accounts

### Student Features (10/10)
1. ✅ Register and login securely
2. ✅ View available accessories
3. ✅ Search/filter items
4. ✅ View item details
5. ✅ Request to buy an item
6. ✅ Suggest a lower price
7. ✅ Wait for admin approval
8. ✅ View request status
9. ✅ View negotiation history
10. ✅ Cancel requests

---

## 🔄 Hybrid Mode

The system operates in **hybrid mode**:
- **Primary:** MySQL database via PHP API
- **Fallback:** LocalStorage (if database unavailable)
- **Automatic detection:** System checks API availability on load
- **Seamless switching:** No user intervention required

---

## 📦 Deployment

### Development Setup
1. Install XAMPP
2. Clone repository to `C:\xampp\htdocs\honehube\`
3. Start Apache and MySQL
4. Run `http://localhost/honehube/backend/scripts/install.php`
5. Access `http://localhost/honehube/`

### Production Setup
1. Upload files to web server
2. Update `backend/config/config.php` with production credentials
3. Run installation script
4. Configure SSL certificate
5. Update reCAPTCHA keys
6. Set up automated backups

---

## 🎓 Default Credentials

**Admin Account:**
- Email: `admin@honehube.com`
- Password: `Admin@123`

**Change immediately after installation!**

---

## 📞 Support

For issues and questions:
1. Check relevant documentation in `docs/`
2. Review `TROUBLESHOOTING_INSTALL.md`
3. Check browser console for errors
4. Verify XAMPP services are running
5. Test database connection

---

**Version:** 1.0  
**Last Updated:** April 26, 2026  
**Status:** Production Ready ✅
