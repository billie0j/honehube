<?php
/**
 * Honehube Users Management API
 * Handles user CRUD operations (Admin only)
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
 * Check if user is admin
 */
function requireAdmin() {
    if (!isset($_SESSION['user_id'])) {
        sendJSON(['success' => false, 'message' => 'Authentication required'], 401);
    }
    
    global $db;
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user || $user['role'] !== 'admin') {
        sendJSON(['success' => false, 'message' => 'Admin access required'], 403);
    }
}

/**
 * Get all users
 */
function getUsers($db) {
    requireAdmin();
    
    $role = $_GET['role'] ?? 'all';
    $status = $_GET['status'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    $sql = "SELECT id, name, email, student_id, role, created_at, last_login, is_active 
            FROM users 
            WHERE 1=1";
    $params = [];
    
    if ($role !== 'all') {
        $sql .= " AND role = ?";
        $params[] = $role;
    }
    
    if ($status === 'active') {
        $sql .= " AND is_active = 1";
    } elseif ($status === 'inactive') {
        $sql .= " AND is_active = 0";
    }
    
    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR email LIKE ? OR student_id LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    
    sendJSON(['success' => true, 'users' => $users]);
}

/**
 * Get single user
 */
function getUser($db) {
    requireAdmin();
    
    $id = intval($_GET['id'] ?? 0);
    
    $stmt = $db->prepare("
        SELECT id, name, email, student_id, role, created_at, updated_at, last_login, is_active 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Get user statistics
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM purchase_requests WHERE buyer_id = ?");
        $stmt->execute([$id]);
        $stats = $stmt->fetch();
        $user['total_requests'] = $stats['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM purchase_requests WHERE buyer_id = ? AND status = 'accepted'");
        $stmt->execute([$id]);
        $stats = $stmt->fetch();
        $user['accepted_requests'] = $stats['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM purchase_requests WHERE buyer_id = ? AND status = 'denied'");
        $stmt->execute([$id]);
        $stats = $stmt->fetch();
        $user['denied_requests'] = $stats['count'];
        
        sendJSON(['success' => true, 'user' => $user]);
    } else {
        sendJSON(['success' => false, 'message' => 'User not found'], 404);
    }
}

/**
 * Update user
 */
function updateUser($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    $name = sanitizeInput($data['name'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $student_id = isset($data['student_id']) ? sanitizeInput($data['student_id']) : null;
    $role = sanitizeInput($data['role'] ?? 'user');
    $is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
    
    // Validation
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid user ID'], 400);
    }
    
    if (empty($name) || strlen($name) < 2) {
        sendJSON(['success' => false, 'message' => 'Name must be at least 2 characters'], 400);
    }
    
    if (!validateEmail($email)) {
        sendJSON(['success' => false, 'message' => 'Invalid email format'], 400);
    }
    
    if (!in_array($role, ['user', 'admin'])) {
        sendJSON(['success' => false, 'message' => 'Invalid role'], 400);
    }
    
    // Check if email is already taken by another user
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        sendJSON(['success' => false, 'message' => 'Email already in use'], 409);
    }
    
    try {
        // Update user
        $stmt = $db->prepare("
            UPDATE users 
            SET name = ?, email = ?, student_id = ?, role = ?, is_active = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$name, $email, $student_id, $role, $is_active, $id]);
        
        // Get updated user
        $stmt = $db->prepare("
            SELECT id, name, email, student_id, role, created_at, updated_at, last_login, is_active 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        sendJSON([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
        
    } catch(PDOException $e) {
        error_log("Update User Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to update user'], 500);
    }
}

/**
 * Deactivate user
 */
function deactivateUser($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    $reason = sanitizeInput($data['reason'] ?? '');
    
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid user ID'], 400);
    }
    
    // Cannot deactivate self
    if ($id == $_SESSION['user_id']) {
        sendJSON(['success' => false, 'message' => 'Cannot deactivate your own account'], 400);
    }
    
    try {
        // Deactivate user
        $stmt = $db->prepare("UPDATE users SET is_active = 0, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        
        // Cancel all pending purchase requests
        $stmt = $db->prepare("
            UPDATE purchase_requests 
            SET status = 'cancelled' 
            WHERE buyer_id = ? AND status IN ('pending', 'negotiating')
        ");
        $stmt->execute([$id]);
        
        sendJSON([
            'success' => true,
            'message' => 'User deactivated successfully'
        ]);
        
    } catch(PDOException $e) {
        error_log("Deactivate User Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to deactivate user'], 500);
    }
}

/**
 * Activate user
 */
function activateUser($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid user ID'], 400);
    }
    
    try {
        // Activate user
        $stmt = $db->prepare("UPDATE users SET is_active = 1, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        
        sendJSON([
            'success' => true,
            'message' => 'User activated successfully'
        ]);
        
    } catch(PDOException $e) {
        error_log("Activate User Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to activate user'], 500);
    }
}

/**
 * Delete user
 */
function deleteUser($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid user ID'], 400);
    }
    
    // Cannot delete self
    if ($id == $_SESSION['user_id']) {
        sendJSON(['success' => false, 'message' => 'Cannot delete your own account'], 400);
    }
    
    // Check if user is admin
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if ($user && $user['role'] === 'admin') {
        sendJSON(['success' => false, 'message' => 'Cannot delete admin accounts'], 400);
    }
    
    try {
        // Delete user (cascade will handle related records)
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        sendJSON([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
        
    } catch(PDOException $e) {
        error_log("Delete User Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to delete user'], 500);
    }
}

// Route requests
if ($method === 'GET') {
    switch ($action) {
        case 'list':
            getUsers($db);
            break;
        case 'get':
            getUser($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} elseif ($method === 'POST') {
    switch ($action) {
        case 'update':
            updateUser($db);
            break;
        case 'deactivate':
            deactivateUser($db);
            break;
        case 'activate':
            activateUser($db);
            break;
        case 'delete':
            deleteUser($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} else {
    sendJSON(['success' => false, 'message' => 'Method not allowed'], 405);
}
?>
