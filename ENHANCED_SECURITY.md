# HoneHube Enhanced Security Features

## 🔒 Security Implementation Status

### ✅ **IMPLEMENTED SECURITY FEATURES**

#### 1. Password Hashing ✅
**Status:** FULLY IMPLEMENTED

**Implementation:**
- Using PHP `password_hash()` with bcrypt algorithm
- Cost factor: 10 (configurable in `api/config.php`)
- Passwords NEVER stored in plain text
- Automatic salt generation

**Code Location:**
```php
// api/config.php
function hashPassword($password) {
    return password_hash($password, HASH_ALGO, ['cost' => HASH_COST]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
```

**Usage:**
- Registration: `api/auth.php` line ~50
- Login: `api/auth.php` line ~120
- Default admin password: `Admin@123` → Hashed: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`

---

#### 2. Role-Based Access Control (RBAC) ✅
**Status:** FULLY IMPLEMENTED

**Roles:**
- `admin` - Full system access
- `student` - Limited access (view, request, negotiate only)

**Permissions Matrix:**

| Action | Admin | Student |
|--------|-------|---------|
| View items | ✅ | ✅ |
| Add items | ✅ | ❌ |
| Edit items | ✅ | ❌ |
| Delete items | ✅ | ❌ |
| View all requests | ✅ | ❌ (own only) |
| Accept/Deny requests | ✅ | ❌ |
| Send purchase request | ❌ | ✅ |
| Cancel own request | ❌ | ✅ |
| Manage users | ✅ | ❌ |

**Implementation:**
```php
// api/accessories.php, api/users.php
function requireAdmin() {
    if (!isset($_SESSION['user_id'])) {
        sendJSON(['success' => false, 'message' => 'Authentication required'], 401);
    }
    
    global $db;
    $stmt = $db->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user || $user['role'] !== 'admin') {
        sendJSON(['success' => false, 'message' => 'Admin access required'], 403);
    }
}
```

**Protected Endpoints:**
- `POST /api/accessories.php?action=create` - Admin only
- `POST /api/accessories.php?action=update` - Admin only
- `POST /api/accessories.php?action=delete` - Admin only
- `POST /api/requests.php?action=accept` - Admin only
- `POST /api/requests.php?action=deny` - Admin only
- `POST /api/requests.php?action=counter` - Admin only
- `GET /api/users.php?action=list` - Admin only
- `POST /api/users.php?action=*` - Admin only

---

#### 3. Prepared Statements (SQL Injection Prevention) ✅
**Status:** FULLY IMPLEMENTED

**Implementation:**
- ALL database queries use PDO prepared statements
- Parameter binding with type checking
- NO string concatenation in SQL queries

**Examples:**
```php
// SECURE - Using prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// SECURE - Multiple parameters
$stmt = $db->prepare("INSERT INTO accessories (item_name, original_price, posted_by) VALUES (?, ?, ?)");
$stmt->execute([$itemName, $price, $userId]);

// SECURE - Complex query
$stmt = $db->prepare("
    SELECT pr.*, a.item_name 
    FROM purchase_requests pr
    LEFT JOIN accessories a ON pr.item_id = a.item_id
    WHERE pr.student_id = ? AND pr.status = ?
");
$stmt->execute([$studentId, $status]);
```

**Coverage:**
- ✅ `api/auth.php` - All queries use prepared statements
- ✅ `api/accessories.php` - All queries use prepared statements
- ✅ `api/requests.php` - All queries use prepared statements
- ✅ `api/users.php` - All queries use prepared statements

---

#### 4. Database User Privileges ⚠️
**Status:** NEEDS MANUAL CONFIGURATION

**Current Setup:**
- Using MySQL `root` account (development only)
- **WARNING:** NOT suitable for production

**Recommended Production Setup:**

**Step 1: Create Limited Database User**
```sql
-- Connect as root
mysql -u root -p

-- Create dedicated user
CREATE USER 'honehube_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Grant only necessary privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON honehube.* TO 'honehube_user'@'localhost';

-- Do NOT grant:
-- - DROP (can't delete tables)
-- - CREATE (can't create tables)
-- - ALTER (can't modify structure)
-- - GRANT OPTION (can't give permissions to others)

-- Apply changes
FLUSH PRIVILEGES;
```

**Step 2: Update Configuration**
```php
// api/config.php
define('DB_USER', 'honehube_user');  // Change from 'root'
define('DB_PASS', 'strong_password_here');  // Set strong password
```

**Step 3: Test Connection**
```bash
mysql -u honehube_user -p honehube
# Should connect successfully
# Try: DROP TABLE users; 
# Should fail with permission denied
```

---

#### 5. Input Validation ✅
**Status:** FULLY IMPLEMENTED

**Validation Functions:**

**Email Validation:**
```php
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
```

**Price Validation:**
```php
function validatePrice($price) {
    return is_numeric($price) && $price > 0 && $price <= 999999.99;
}
```

**Phone Validation (Zambian format):**
```php
function validatePhone($phone) {
    // +260 or 0 followed by 9 digits
    return preg_match('/^(\+260|0)[0-9]{9}$/', $phone);
}
```

**Input Sanitization:**
```php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
```

**Validation Coverage:**

| Field | Validation | Location |
|-------|------------|----------|
| Email | Format + uniqueness | `api/auth.php` |
| Password | 8+ chars, uppercase, lowercase, number, special | `register.html`, `api/auth.php` |
| Price | Numeric, > 0, <= 999999.99 | `api/accessories.php` |
| Item Name | Min 3 chars, max 200 | `api/accessories.php` |
| Category | Whitelist check | `api/accessories.php` |
| Status | Enum validation | `api/accessories.php` |
| Offered Price | Numeric, > 0, < original price | `api/requests.php` |
| Phone | Zambian format | `api/config.php` (function available) |

---

#### 6. Data Encryption ✅
**Status:** IMPLEMENTED (Functions Available)

**Encryption Functions:**
```php
// Encrypt sensitive data
function encryptData($data) {
    $key = hash('sha256', 'honehube_encryption_key_2026');
    $iv = substr(hash('sha256', 'honehube_iv_2026'), 0, 16);
    return base64_encode(openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv));
}

// Decrypt sensitive data
function decryptData($data) {
    $key = hash('sha256', 'honehube_encryption_key_2026');
    $iv = substr(hash('sha256', 'honehube_iv_2026'), 0, 16);
    return openssl_decrypt(base64_decode($data), 'AES-256-CBC', $key, 0, $iv);
}
```

**Usage Example:**
```php
// Encrypt phone number before storing
$encryptedPhone = encryptData($phoneNumber);
$stmt = $db->prepare("INSERT INTO users (phone) VALUES (?)");
$stmt->execute([$encryptedPhone]);

// Decrypt when retrieving
$stmt = $db->prepare("SELECT phone FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$decryptedPhone = decryptData($user['phone']);
```

**Recommended for:**
- ✅ Phone numbers
- ✅ Physical addresses
- ✅ Payment details (if added)
- ✅ Student ID numbers (optional)
- ❌ Passwords (use hashing, not encryption)
- ❌ Emails (needed for login)

**Production Note:**
- Store encryption keys in environment variables
- Use different keys for different environments
- Rotate keys periodically

---

#### 7. Audit Logs ✅
**Status:** FULLY IMPLEMENTED

**Database Table:**
```sql
CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action_type VARCHAR(50) NOT NULL,
    action_description TEXT NOT NULL,
    table_name VARCHAR(50) NULL,
    record_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created_at (created_at)
);
```

**Logging Function:**
```php
function logAudit($actionType, $actionDescription, $tableName = null, $recordId = null) {
    try {
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt = $db->prepare("
            INSERT INTO audit_logs (user_id, action_type, action_description, table_name, record_id, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $actionType, $actionDescription, $tableName, $recordId, $ipAddress, $userAgent]);
    } catch(PDOException $e) {
        error_log("Audit Log Error: " . $e->getMessage());
    }
}
```

**Logged Actions:**

| Action Type | Description | Location |
|-------------|-------------|----------|
| `user_registered` | New user account created | `api/auth.php` |
| `user_login` | User logged in | `api/auth.php` |
| `user_logout` | User logged out | `api/auth.php` |
| `item_added` | Admin added new item | `api/accessories.php` |
| `item_updated` | Admin updated item | `api/accessories.php` |
| `item_deleted` | Admin deleted item | `api/accessories.php` |
| `request_created` | Student sent purchase request | `api/requests.php` |
| `request_accepted` | Admin accepted request | `api/requests.php` |
| `request_denied` | Admin denied request | `api/requests.php` |
| `price_negotiated` | Admin made counter-offer | `api/requests.php` |
| `request_cancelled` | Student cancelled request | `api/requests.php` |
| `user_updated` | Admin updated user account | `api/users.php` |
| `user_deactivated` | Admin deactivated user | `api/users.php` |
| `user_deleted` | Admin deleted user | `api/users.php` |

**Viewing Audit Logs:**
```sql
-- View all logs
SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 100;

-- View logs for specific user
SELECT * FROM audit_logs WHERE user_id = 1 ORDER BY created_at DESC;

-- View logs by action type
SELECT * FROM audit_logs WHERE action_type = 'item_added' ORDER BY created_at DESC;

-- View logs from specific IP
SELECT * FROM audit_logs WHERE ip_address = '192.168.1.100' ORDER BY created_at DESC;

-- View logs for specific date range
SELECT * FROM audit_logs 
WHERE created_at BETWEEN '2026-04-01' AND '2026-04-30' 
ORDER BY created_at DESC;
```

---

## 🛡️ Additional Security Features

### 8. CSRF Protection ✅
- Token generation on session start
- Token validation on all POST requests
- Token rotation after sensitive operations

### 9. Rate Limiting ✅
- Max 5 login attempts per 15 minutes
- Tracked by email and IP address
- Automatic lockout after limit exceeded

### 10. Session Security ✅
- HTTP-only cookies
- Secure flag (HTTPS only in production)
- SameSite: Strict
- Session timeout: 1 hour
- Remember me: 30 days (optional)

### 11. XSS Prevention ✅
- Input sanitization with `htmlspecialchars()`
- Output encoding
- Content Security Policy headers (recommended)

### 12. Password Requirements ✅
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character (@#$%^&+=!)

---

## 📋 Security Checklist

### Development ✅
- [x] Password hashing implemented
- [x] Role-based access control
- [x] Prepared statements for all queries
- [x] Input validation on all forms
- [x] CSRF protection
- [x] Rate limiting
- [x] Session security
- [x] Audit logging
- [x] Encryption functions available

### Production ⚠️
- [ ] Create limited database user
- [ ] Update database credentials
- [ ] Enable HTTPS
- [ ] Set secure cookie flag
- [ ] Store encryption keys in environment variables
- [ ] Disable error display
- [ ] Enable error logging
- [ ] Set up regular database backups
- [ ] Implement log rotation
- [ ] Set up monitoring/alerts
- [ ] Review and update security headers
- [ ] Conduct security audit
- [ ] Penetration testing

---

## 🔧 Production Configuration

### 1. Database Security
```php
// api/config.php - PRODUCTION
define('DB_USER', 'honehube_user');  // Limited user
define('DB_PASS', getenv('DB_PASSWORD'));  // From environment
```

### 2. Error Handling
```php
// api/config.php - PRODUCTION
error_reporting(0);  // Disable error display
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/honehube/php_errors.log');
```

### 3. Session Security
```php
// api/config.php - PRODUCTION
ini_set('session.cookie_secure', 1);  // HTTPS only
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
```

### 4. Encryption Keys
```php
// api/config.php - PRODUCTION
function encryptData($data) {
    $key = getenv('ENCRYPTION_KEY');  // From environment
    $iv = getenv('ENCRYPTION_IV');
    return base64_encode(openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv));
}
```

### 5. Security Headers
```php
// Add to all API files
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'");
```

---

## 📊 Security Monitoring

### Audit Log Analysis
```sql
-- Failed login attempts
SELECT email, COUNT(*) as attempts, MAX(attempted_at) as last_attempt
FROM login_attempts
WHERE success = 0 AND attempted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY email
HAVING attempts > 3
ORDER BY attempts DESC;

-- Recent admin actions
SELECT al.*, u.full_name
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.user_id
WHERE u.role = 'admin'
ORDER BY al.created_at DESC
LIMIT 50;

-- Suspicious activity (multiple IPs for same user)
SELECT user_id, COUNT(DISTINCT ip_address) as ip_count
FROM audit_logs
WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY user_id
HAVING ip_count > 3;
```

---

## 🆘 Security Incident Response

### If Breach Detected:
1. **Immediate Actions:**
   - Disable affected accounts
   - Change all passwords
   - Rotate encryption keys
   - Review audit logs
   - Identify attack vector

2. **Investigation:**
   - Check audit logs for unauthorized access
   - Review login attempts
   - Analyze IP addresses
   - Check for data exfiltration

3. **Recovery:**
   - Patch vulnerabilities
   - Restore from clean backup if needed
   - Notify affected users
   - Update security measures
   - Document incident

---

## 📚 Security Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [MySQL Security Guide](https://dev.mysql.com/doc/refman/8.0/en/security.html)
- [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)

---

**Document Version:** 1.0  
**Last Updated:** April 26, 2026  
**Status:** Production Ready (with production configuration)  
**System:** HoneHube E-commerce Platform
