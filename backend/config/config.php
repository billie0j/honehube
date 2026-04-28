<?php
/**
 * Honehube Database Configuration
 * 
 * IMPORTANT: Update these settings for your environment
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'honehube');
define('DB_USER', 'root');  // Change this for production
define('DB_PASS', '');      // Change this for production
define('DB_CHARSET', 'utf8mb4');

// Security settings
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 10);

// Session settings
define('SESSION_LIFETIME', 600); // 10 minutes (600 seconds)
define('REMEMBER_ME_LIFETIME', 2592000); // 30 days

// Rate limiting
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_WINDOW', 900); // 15 minutes

// CORS settings (for API)
define('ALLOWED_ORIGINS', ['http://localhost:8080', 'http://localhost', 'https://billie0j.github.io']);

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Timezone
date_default_timezone_set('Africa/Harare');

// Account lockout settings
define('ACCOUNT_LOCKOUT_DURATION', 1800); // 30 minutes
define('ACCOUNT_LOCKOUT_THRESHOLD', 5); // Lock after 5 failed attempts

/**
 * Database Connection Class
 */
class Database {
    private static ?Database $instance = null;
    private PDO $conn;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die(json_encode(['success' => false, 'message' => 'Database connection failed']));
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Security Helper Functions
 */

// Hash password
function hashPassword(string $password) {
    return password_hash($password, HASH_ALGO, ['cost' => HASH_COST]);
}

// Verify password
function verifyPassword(string $password, string $hash) {
    return password_verify($password, $hash);
}

// Generate CSRF token
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

// Validate CSRF token
function validateCSRFToken(string $token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitizeInput(string $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validate email
function validateEmail(string $email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Set CORS headers
function setCORSHeaders() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($origin, ALLOWED_ORIGINS)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Send JSON response
function sendJSON(mixed $data, int $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Start secure session
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1); // HTTPS enabled
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
    }
}

/**
 * Regenerate session ID for security
 * Call this after login or privilege escalation
 */
function regenerateSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Check if account is locked
 * 
 * @param string $email - Email to check
 * @return bool - True if locked, false otherwise
 */
function isAccountLocked($email) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT locked_until 
            FROM account_lockouts 
            WHERE email = ? AND locked_until > NOW()
        ");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    } catch(PDOException $e) {
        error_log("Account Lock Check Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Lock account after too many failed attempts
 * 
 * @param string $email - Email to lock
 */
function lockAccount($email) {
    try {
        $db = Database::getInstance()->getConnection();
        $lockUntil = date('Y-m-d H:i:s', time() + ACCOUNT_LOCKOUT_DURATION);
        
        $stmt = $db->prepare("
            INSERT INTO account_lockouts (email, locked_until) 
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE locked_until = ?, created_at = NOW()
        ");
        $stmt->execute([$email, $lockUntil, $lockUntil]);
        
        // Log audit trail
        logAudit('account_locked', "Account locked due to too many failed login attempts: {$email}", 'account_lockouts', null);
    } catch(PDOException $e) {
        error_log("Account Lock Error: " . $e->getMessage());
    }
}

/**
 * Get remaining lockout time in minutes
 * 
 * @param string $email - Email to check
 * @return int - Minutes remaining, 0 if not locked
 */
function getLockoutTimeRemaining($email) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT TIMESTAMPDIFF(MINUTE, NOW(), locked_until) as minutes_remaining
            FROM account_lockouts 
            WHERE email = ? AND locked_until > NOW()
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ? max(0, $result['minutes_remaining']) : 0;
    } catch(PDOException $e) {
        error_log("Lockout Time Check Error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Handle database errors gracefully
 * Logs the error and returns user-friendly message
 * 
 * @param PDOException $e - The exception
 * @param string $context - Context of the error
 * @return array - Error response
 */
function handleDatabaseError($e, $context = 'operation') {
    // Log detailed error for debugging
    error_log("Database Error in {$context}: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Return generic message to user
    return [
        'success' => false,
        'message' => 'Something went wrong. Please try again later.'
    ];
}

/**
 * Log audit trail for important actions
 * 
 * @param string $actionType - Type of action (e.g., 'user_login', 'item_added', 'request_created')
 * @param string $actionDescription - Detailed description of the action
 * @param string|null $tableName - Name of the table affected (optional)
 * @param int|null $recordId - ID of the record affected (optional)
 */
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
        // Log error but don't fail the main operation
        error_log("Audit Log Error: " . $e->getMessage());
    }
}

/**
 * Validate price input
 * 
 * @param mixed $price - Price value to validate
 * @return bool - True if valid, false otherwise
 */
function validatePrice($price) {
    return is_numeric($price) && $price > 0 && $price <= 999999.99;
}

/**
 * Validate phone number (Zambian format)
 * 
 * @param string $phone - Phone number to validate
 * @return bool - True if valid, false otherwise
 */
function validatePhone($phone) {
    // Zambian phone format: +260 or 0 followed by 9 digits
    return preg_match('/^(\+260|0)[0-9]{9}$/', $phone);
}

/**
 * Encrypt sensitive data
 * 
 * @param string $data - Data to encrypt
 * @return string - Encrypted data
 */
function encryptData($data) {
    $key = hash('sha256', 'honehube_encryption_key_2026'); // Use environment variable in production
    $iv = substr(hash('sha256', 'honehube_iv_2026'), 0, 16);
    return base64_encode(openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv));
}

/**
 * Decrypt sensitive data
 * 
 * @param string $data - Encrypted data
 * @return string - Decrypted data
 */
function decryptData($data) {
    $key = hash('sha256', 'honehube_encryption_key_2026'); // Use environment variable in production
    $iv = substr(hash('sha256', 'honehube_iv_2026'), 0, 16);
    return openssl_decrypt(base64_decode($data), 'AES-256-CBC', $key, 0, $iv);
}
?>
