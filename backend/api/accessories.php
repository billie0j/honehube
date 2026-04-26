<?php
/**
 * Honehube Accessories API
 * Handles CRUD operations for accessories
 */

require_once '../config/config.php';

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
    $stmt = $db->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user || $user['role'] !== 'admin') {
        sendJSON(['success' => false, 'message' => 'Admin access required'], 403);
    }
}

/**
 * Get all accessories
 */
function getAccessories($db) {
    $status = $_GET['status'] ?? 'all';
    $category = $_GET['category'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    $sql = "SELECT a.*, u.full_name as seller_name, u.email as seller_email 
            FROM accessories a 
            LEFT JOIN users u ON a.posted_by = u.user_id 
            WHERE 1=1";
    $params = [];
    
    if ($status !== 'all') {
        $sql .= " AND a.status = ?";
        $params[] = $status;
    }
    
    if ($category !== 'all') {
        $sql .= " AND a.category = ?";
        $params[] = $category;
    }
    
    if (!empty($search)) {
        $sql .= " AND (a.item_name LIKE ? OR a.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY a.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $accessories = $stmt->fetchAll();
    
    sendJSON(['success' => true, 'accessories' => $accessories]);
}

/**
 * Get single accessory
 */
function getAccessory($db) {
    $id = $_GET['id'] ?? 0;
    
    $stmt = $db->prepare("
        SELECT a.*, u.full_name as seller_name, u.email as seller_email 
        FROM accessories a 
        LEFT JOIN users u ON a.posted_by = u.user_id 
        WHERE a.item_id = ?
    ");
    $stmt->execute([$id]);
    $accessory = $stmt->fetch();
    
    if ($accessory) {
        sendJSON(['success' => true, 'accessory' => $accessory]);
    } else {
        sendJSON(['success' => false, 'message' => 'Item not found'], 404);
    }
}

/**
 * Create new accessory (Admin only)
 */
function createAccessory($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    // Sanitize and validate input
    $item_name = sanitizeInput($data['item_name'] ?? '');
    $description = sanitizeInput($data['description'] ?? '');
    $category = sanitizeInput($data['category'] ?? '');
    $original_price = floatval($data['original_price'] ?? 0);
    $image = sanitizeInput($data['image'] ?? '');
    
    // Validation
    if (empty($item_name) || strlen($item_name) < 3) {
        sendJSON(['success' => false, 'message' => 'Item name must be at least 3 characters'], 400);
    }
    
    if ($original_price <= 0) {
        sendJSON(['success' => false, 'message' => 'Price must be greater than 0'], 400);
    }
    
    if (!in_array($category, ['Laptops', 'Chargers', 'Phones', 'Bags', 'Earphones', 'Books', 'RAM', 'Storage', 'Other'])) {
        sendJSON(['success' => false, 'message' => 'Invalid category'], 400);
    }
    
    // Insert accessory
    try {
        $stmt = $db->prepare("
            INSERT INTO accessories (posted_by, item_name, description, category, original_price, image, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'available')
        ");
        $stmt->execute([$_SESSION['user_id'], $item_name, $description, $category, $original_price, $image]);
        
        $itemId = $db->lastInsertId();
        
        // Get created accessory
        $stmt = $db->prepare("SELECT * FROM accessories WHERE item_id = ?");
        $stmt->execute([$itemId]);
        $accessory = $stmt->fetch();
        
        // Log audit trail
        logAudit('item_added', "Admin added item: {$item_name} (K{$original_price})", 'accessories', $itemId);
        
        sendJSON([
            'success' => true,
            'message' => 'Item created successfully',
            'accessory' => $accessory
        ]);
        
    } catch(PDOException $e) {
        error_log("Create Accessory Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to create item'], 500);
    }
}

/**
 * Update accessory (Admin only)
 */
function updateAccessory($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    $item_name = sanitizeInput($data['item_name'] ?? '');
    $description = sanitizeInput($data['description'] ?? '');
    $category = sanitizeInput($data['category'] ?? '');
    $original_price = floatval($data['original_price'] ?? 0);
    $status = sanitizeInput($data['status'] ?? 'available');
    $image = sanitizeInput($data['image'] ?? '');
    
    // Validation
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid item ID'], 400);
    }
    
    if (empty($item_name) || strlen($item_name) < 3) {
        sendJSON(['success' => false, 'message' => 'Item name must be at least 3 characters'], 400);
    }
    
    if ($original_price <= 0) {
        sendJSON(['success' => false, 'message' => 'Price must be greater than 0'], 400);
    }
    
    if (!in_array($status, ['available', 'sold'])) {
        sendJSON(['success' => false, 'message' => 'Invalid status'], 400);
    }
    
    // Update accessory
    try {
        $stmt = $db->prepare("
            UPDATE accessories 
            SET item_name = ?, description = ?, category = ?, original_price = ?, 
                status = ?, image = ?
            WHERE item_id = ?
        ");
        $stmt->execute([$item_name, $description, $category, $original_price, $status, $image, $id]);
        
        // If marked as sold, update related purchase requests
        if ($status === 'sold') {
            // Mark accepted request as completed
            $db->prepare("
                UPDATE purchase_requests 
                SET status = 'completed' 
                WHERE item_id = ? AND status = 'accepted'
            ")->execute([$id]);
            
            // Cancel other pending requests
            $db->prepare("
                UPDATE purchase_requests 
                SET status = 'cancelled' 
                WHERE item_id = ? AND status IN ('pending', 'negotiating')
            ")->execute([$id]);
        }
        
        // Get updated accessory
        $stmt = $db->prepare("SELECT * FROM accessories WHERE item_id = ?");
        $stmt->execute([$id]);
        $accessory = $stmt->fetch();
        
        // Log audit trail
        logAudit('item_updated', "Admin updated item: {$item_name} (Status: {$status})", 'accessories', $id);
        
        sendJSON([
            'success' => true,
            'message' => 'Item updated successfully',
            'accessory' => $accessory
        ]);
        
    } catch(PDOException $e) {
        error_log("Update Accessory Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to update item'], 500);
    }
}

/**
 * Delete accessory (Admin only)
 */
function deleteAccessory($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid item ID'], 400);
    }
    
    try {
        // Check if item has active purchase requests
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM purchase_requests 
            WHERE item_id = ? AND status IN ('pending', 'negotiating', 'accepted')
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            // Cancel all related purchase requests
            $db->prepare("
                UPDATE purchase_requests 
                SET status = 'cancelled' 
                WHERE item_id = ?
            ")->execute([$id]);
        }
        
        // Delete accessory
        $stmt = $db->prepare("DELETE FROM accessories WHERE item_id = ?");
        $stmt->execute([$id]);
        
        // Log audit trail
        logAudit('item_deleted', "Admin deleted item ID: {$id}", 'accessories', $id);
        
        sendJSON([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
        
    } catch(PDOException $e) {
        error_log("Delete Accessory Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to delete item'], 500);
    }
}

// Route requests
if ($method === 'GET') {
    switch ($action) {
        case 'list':
            getAccessories($db);
            break;
        case 'get':
            getAccessory($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} elseif ($method === 'POST') {
    switch ($action) {
        case 'create':
            createAccessory($db);
            break;
        case 'update':
            updateAccessory($db);
            break;
        case 'delete':
            deleteAccessory($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} else {
    sendJSON(['success' => false, 'message' => 'Method not allowed'], 405);
}
?>
