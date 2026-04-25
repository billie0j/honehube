<?php
/**
 * Honehube Authentication API
 * Handles login, register, and logout
 */

require_once 'config.php';

// Start session and set CORS headers
startSecureSession();
setCORSHeaders();

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Get database connection
$db = Database::getInstance()->getConnection();

/**
 * Register new user
 */
function registerUser($db) {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    // Validate reCAPTCHA (if provided)
    if (isset($data['recaptcha_response'])) {
        // TODO: Implement server-side reCAPTCHA verification
        // For now, we'll skip this in development
    }
    
    // Sanitize and validate input
    $name = sanitizeInput($data['name'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $studentId = isset($data['student_id']) ? sanitizeInput($data['student_id']) : null;
    
    // Validation
    if (empty($name) || strlen($name) < 2) {
        sendJSON(['success' => false, 'message' => 'Invalid name'], 400);
    }
    
    if (!validateEmail($email)) {
        sendJSON(['success' => false, 'message' => 'Invalid email format'], 400);
    }
    
    // Password strength validation
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || 
        !preg_match('/[@#$%^&+=!]/', $password)) {
        sendJSON(['success' => false, 'message' => 'Password does not meet security requirements'], 400);
    }
    
    // Check if user already exists
    $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendJSON(['success' => false, 'message' => 'Unable to create account. Please try a different email.'], 409);
    }
    
    // Hash password
    $hashedPassword = hashPassword($password);
    
    // Insert user
    try {
        $stmt = $db->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->execute([$name, $email, $hashedPassword]);
        
        $userId = $db->lastInsertId();
        
        // Get user data
        $stmt = $db->prepare("SELECT user_id, full_name, email, role, created_at FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        // Set session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['csrf_token'] = generateCSRFToken();
        
        sendJSON([
            'success' => true,
            'message' => 'Account created successfully',
            'user' => $user,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
        
    } catch(PDOException $e) {
        error_log("Registration Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Registration failed'], 500);
    }
}

/**
 * Login user
 */
function loginUser($db) {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    // Validate reCAPTCHA (if provided)
    if (isset($data['recaptcha_response'])) {
        // TODO: Implement server-side reCAPTCHA verification
    }
    
    // Sanitize input
    $identifier = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $remember = $data['remember'] ?? false;
    
    if (empty($identifier) || empty($password)) {
        sendJSON(['success' => false, 'message' => 'Invalid email or password'], 400);
    }
    
    // Check rate limiting
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $db->prepare("
        SELECT COUNT(*) as attempts 
        FROM login_attempts 
        WHERE (email = ? OR ip_address = ?) 
        AND success = 0 
        AND attempted_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
    ");
    $stmt->execute([$identifier, $ip, LOGIN_ATTEMPT_WINDOW]);
    $result = $stmt->fetch();
    
    if ($result['attempts'] >= MAX_LOGIN_ATTEMPTS) {
        sendJSON(['success' => false, 'message' => 'Too many failed attempts. Please try again later.'], 429);
    }
    
    // Find user by email or student ID
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$identifier]);
    $user = $stmt->fetch();
    
    // Log login attempt
    $stmt = $db->prepare("INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, ?)");
    
    if ($user && verifyPassword($password, $user['password'])) {
        // Successful login
        $stmt->execute([$identifier, $ip, 1]);
        
        // Set session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['csrf_token'] = generateCSRFToken();
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + REMEMBER_ME_LIFETIME, '/', '', false, true);
        }
        
        // Remove password from response
        unset($user['password']);
        
        sendJSON([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
        
    } else {
        // Failed login
        $stmt->execute([$identifier, $ip, 0]);
        sendJSON(['success' => false, 'message' => 'Invalid email or password'], 401);
    }
}

/**
 * Logout user
 */
function logoutUser() {
    // Clear session
    $_SESSION = [];
    
    // Destroy session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    session_destroy();
    
    sendJSON(['success' => true, 'message' => 'Logged out successfully']);
}

/**
 * Get current user
 */
function getCurrentUser($db) {
    if (!isset($_SESSION['user_id'])) {
        sendJSON(['success' => false, 'message' => 'Not authenticated'], 401);
    }
    
    $stmt = $db->prepare("SELECT user_id, full_name, email, role, created_at FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        sendJSON(['success' => true, 'user' => $user]);
    } else {
        sendJSON(['success' => false, 'message' => 'User not found'], 404);
    }
}

/**
 * Generate new CSRF token
 */
function getCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateCSRFToken();
    }
    sendJSON(['success' => true, 'csrf_token' => $_SESSION['csrf_token']]);
}

// Route requests
if ($method === 'POST') {
    switch ($action) {
        case 'register':
            registerUser($db);
            break;
        case 'login':
            loginUser($db);
            break;
        case 'logout':
            logoutUser();
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} elseif ($method === 'GET') {
    switch ($action) {
        case 'user':
            getCurrentUser($db);
            break;
        case 'csrf':
            getCSRFToken();
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} else {
    sendJSON(['success' => false, 'message' => 'Method not allowed'], 405);
}
?>
