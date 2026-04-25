# Database Setup Guide

## Overview
This guide will help you set up the MySQL database for Honehube.

## Prerequisites
- XAMPP installed with Apache and MySQL running
- PHP 7.4 or higher
- MySQL 5.7 or higher

## Installation Steps

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### Step 2: Create Database

#### Option A: Using phpMyAdmin (Recommended for beginners)
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on "SQL" tab
3. Copy and paste the contents of `database/schema.sql`
4. Click "Go" to execute

#### Option B: Using MySQL Command Line
1. Open Command Prompt
2. Navigate to MySQL bin directory:
   ```bash
   cd C:\xampp\mysql\bin
   ```
3. Login to MySQL:
   ```bash
   mysql -u root -p
   ```
   (Press Enter when asked for password if you haven't set one)
4. Run the schema file:
   ```sql
   source C:\xampp\htdocs\honehube\database\schema.sql
   ```

### Step 3: Configure Database Connection
1. Open `api/config.php`
2. Update the database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'honehube');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Add your MySQL password if you have one
   ```

### Step 4: Test Database Connection
1. Create a test file: `test_db.php` in your project root
2. Add this code:
   ```php
   <?php
   require_once 'api/config.php';
   try {
       $db = Database::getInstance()->getConnection();
       echo "Database connection successful!";
   } catch(Exception $e) {
       echo "Connection failed: " . $e->getMessage();
   }
   ?>
   ```
3. Visit: `http://localhost/honehube/test_db.php`
4. You should see "Database connection successful!"

### Step 5: Verify Database Tables
1. Go to phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `honehube` database from the left sidebar
3. You should see these tables:
   - `users`
   - `listings`
   - `inquiries`
   - `sessions`
   - `login_attempts`

## Database Structure

### Users Table
Stores user accounts (students, admins)
- `id` - Primary key
- `name` - Full name
- `email` - Email address (unique)
- `password` - Hashed password (bcrypt)
- `student_id` - Student ID (optional, unique)
- `role` - user or admin
- `created_at` - Registration date
- `last_login` - Last login timestamp

### Listings Table
Stores product listings (laptops, parts, etc.)
- `id` - Primary key
- `user_id` - Foreign key to users
- `title` - Product title
- `description` - Product description
- `category` - Product category
- `price` - Product price
- `condition_type` - new or used
- `image` - Image URL
- `status` - active, sold, or inactive

### Inquiries Table
Stores buyer-seller messages
- `id` - Primary key
- `listing_id` - Foreign key to listings
- `buyer_id` - Foreign key to users
- `seller_id` - Foreign key to users
- `message` - Inquiry message
- `status` - pending, replied, or closed

### Sessions Table
Stores session data and CSRF tokens
- `id` - Session ID
- `user_id` - Foreign key to users
- `csrf_token` - CSRF token
- `ip_address` - User IP
- `user_agent` - Browser info
- `expires_at` - Expiration time

### Login Attempts Table
Tracks login attempts for rate limiting
- `id` - Primary key
- `email` - Email attempted
- `ip_address` - IP address
- `success` - Boolean (success/failure)
- `attempted_at` - Timestamp

## Default Admin Account

**Email:** admin@honehube.com  
**Password:** Admin@123

⚠️ **IMPORTANT:** Change this password immediately after first login!

## API Endpoints

### Authentication
- `POST /api/auth.php?action=register` - Register new user
- `POST /api/auth.php?action=login` - Login user
- `POST /api/auth.php?action=logout` - Logout user
- `GET /api/auth.php?action=user` - Get current user
- `GET /api/auth.php?action=csrf` - Get CSRF token

### Request Format (JSON)
```json
{
  "email": "student@evlynehone.ac.zw",
  "password": "Password@123",
  "csrf_token": "your_csrf_token_here"
}
```

### Response Format (JSON)
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@evlynehone.ac.zw",
    "role": "user"
  },
  "csrf_token": "new_csrf_token"
}
```

## Security Features

### Password Hashing
- Uses bcrypt algorithm
- Cost factor: 10
- Passwords are never stored in plain text

### CSRF Protection
- Every session has a unique CSRF token
- Tokens are validated on all POST requests
- Tokens are regenerated after login

### Rate Limiting
- Maximum 5 failed login attempts
- 15-minute lockout window
- Tracks by email and IP address

### SQL Injection Prevention
- All queries use prepared statements
- Input sanitization on all user data

### Session Security
- HTTP-only cookies
- Secure cookies (in production with HTTPS)
- SameSite=Strict policy

## Troubleshooting

### Error: "Database connection failed"
- Check if MySQL is running in XAMPP
- Verify database credentials in `api/config.php`
- Ensure database `honehube` exists

### Error: "Table doesn't exist"
- Run the schema.sql file again
- Check if you're using the correct database

### Error: "Access denied for user"
- Check MySQL username and password
- Default XAMPP: username=root, password=(empty)

### Error: "CSRF token validation failed"
- Clear browser cookies
- Refresh the page to get a new token

## Migration from localStorage

To migrate existing localStorage data to MySQL:

1. Export data from browser console:
   ```javascript
   console.log(localStorage.getItem('honehub_users'));
   console.log(localStorage.getItem('honehub_listings'));
   ```

2. Create a migration script or manually insert data into MySQL

3. Update frontend to use API instead of localStorage

## Next Steps

1. ✅ Database created and configured
2. ⬜ Update frontend JavaScript to use API
3. ⬜ Test registration and login
4. ⬜ Implement listings API
5. ⬜ Add image upload functionality
6. ⬜ Deploy to production

## Production Checklist

Before deploying to production:

- [ ] Change default admin password
- [ ] Update database credentials
- [ ] Enable HTTPS
- [ ] Set secure cookie flag to true
- [ ] Disable error display
- [ ] Implement server-side reCAPTCHA verification
- [ ] Set up database backups
- [ ] Configure firewall rules
- [ ] Update CORS allowed origins

---

**Last Updated:** 2026-04-25  
**Version:** 1.0
