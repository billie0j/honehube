<?php
/**
 * Honehube Listings API
 * Handles CRUD operations for listings
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
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user || $user['role'] !== 'admin') {
        sendJSON(['success' => false, 'message' => 'Admin access required'], 403);
    }
}

/**
 * Get all listings
 */
function getListings($db) {
    $status = $_GET['status'] ?? 'all';
    $category = $_GET['category'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    $sql = "SELECT l.*, u.name as seller_name, u.email as seller_email 
            FROM listings l 
            LEFT JOIN users u ON l.user_id = u.id 
            WHERE 1=1";
    $params = [];
    
    if ($status !== 'all') {
        $sql .= " AND l.status = ?";
        $params[] = $status;
    }
    
    if ($category !== 'all') {
        $sql .= " AND l.category = ?";
        $params[] = $category;
    }
    
    if (!empty($search)) {
        $sql .= " AND (l.title LIKE ? OR l.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY l.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $listings = $stmt->fetchAll();
    
    sendJSON(['success' => true, 'listings' => $listings]);
}

/**
 * Get single listing
 */
function getListing($db) {
    $id = $_GET['id'] ?? 0;
    
    $stmt = $db->prepare("
        SELECT l.*, u.name as seller_name, u.email as seller_email 
        FROM listings l 
        LEFT JOIN users u ON l.user_id = u.id 
        WHERE l.id = ?
    ");
    $stmt->execute([$id]);
    $listing = $stmt->fetch();
    
    if ($listing) {
        sendJSON(['success' => true, 'listing' => $listing]);
    } else {
        sendJSON(['success' => false, 'message' => 'Listing not found'], 404);
    }
}

/**
 * Create new listing (Admin only)
 */
function createListing($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    // Sanitize and validate input
    $title = sanitizeInput($data['title'] ?? '');
    $description = sanitizeInput($data['description'] ?? '');
    $category = sanitizeInput($data['category'] ?? '');
    $price = floatval($data['price'] ?? 0);
    $condition_type = sanitizeInput($data['condition_type'] ?? 'used');
    $image = sanitizeInput($data['image'] ?? '');
    
    // Validation
    if (empty($title) || strlen($title) < 3) {
        sendJSON(['success' => false, 'message' => 'Title must be at least 3 characters'], 400);
    }
    
    if ($price <= 0) {
        sendJSON(['success' => false, 'message' => 'Price must be greater than 0'], 400);
    }
    
    if (!in_array($category, ['Laptops', 'Chargers', 'Phones', 'Bags', 'Earphones', 'Books', 'RAM', 'Storage', 'Other'])) {
        sendJSON(['success' => false, 'message' => 'Invalid category'], 400);
    }
    
    if (!in_array($condition_type, ['new', 'used'])) {
        sendJSON(['success' => false, 'message' => 'Invalid condition type'], 400);
    }
    
    // Insert listing
    try {
        $stmt = $db->prepare("
            INSERT INTO listings (user_id, title, description, category, price, condition_type, image, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
        ");
        $stmt->execute([$_SESSION['user_id'], $title, $description, $category, $price, $condition_type, $image]);
        
        $listingId = $db->lastInsertId();
        
        // Get created listing
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = ?");
        $stmt->execute([$listingId]);
        $listing = $stmt->fetch();
        
        sendJSON([
            'success' => true,
            'message' => 'Listing created successfully',
            'listing' => $listing
        ]);
        
    } catch(PDOException $e) {
        error_log("Create Listing Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to create listing'], 500);
    }
}

/**
 * Update listing (Admin only)
 */
function updateListing($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    $title = sanitizeInput($data['title'] ?? '');
    $description = sanitizeInput($data['description'] ?? '');
    $category = sanitizeInput($data['category'] ?? '');
    $price = floatval($data['price'] ?? 0);
    $condition_type = sanitizeInput($data['condition_type'] ?? 'used');
    $status = sanitizeInput($data['status'] ?? 'active');
    $image = sanitizeInput($data['image'] ?? '');
    
    // Validation
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid listing ID'], 400);
    }
    
    if (empty($title) || strlen($title) < 3) {
        sendJSON(['success' => false, 'message' => 'Title must be at least 3 characters'], 400);
    }
    
    if ($price <= 0) {
        sendJSON(['success' => false, 'message' => 'Price must be greater than 0'], 400);
    }
    
    if (!in_array($status, ['active', 'sold', 'inactive'])) {
        sendJSON(['success' => false, 'message' => 'Invalid status'], 400);
    }
    
    // Update listing
    try {
        $stmt = $db->prepare("
            UPDATE listings 
            SET title = ?, description = ?, category = ?, price = ?, 
                condition_type = ?, status = ?, image = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$title, $description, $category, $price, $condition_type, $status, $image, $id]);
        
        // If marked as sold, update related purchase requests
        if ($status === 'sold') {
            // Mark accepted request as completed
            $db->prepare("
                UPDATE purchase_requests 
                SET status = 'completed' 
                WHERE listing_id = ? AND status = 'accepted'
            ")->execute([$id]);
            
            // Cancel other pending requests
            $db->prepare("
                UPDATE purchase_requests 
                SET status = 'cancelled' 
                WHERE listing_id = ? AND status IN ('pending', 'negotiating')
            ")->execute([$id]);
        }
        
        // Get updated listing
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = ?");
        $stmt->execute([$id]);
        $listing = $stmt->fetch();
        
        sendJSON([
            'success' => true,
            'message' => 'Listing updated successfully',
            'listing' => $listing
        ]);
        
    } catch(PDOException $e) {
        error_log("Update Listing Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to update listing'], 500);
    }
}

/**
 * Delete listing (Admin only)
 */
function deleteListing($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid listing ID'], 400);
    }
    
    try {
        // Check if listing has active purchase requests
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM purchase_requests 
            WHERE listing_id = ? AND status IN ('pending', 'negotiating', 'accepted')
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            // Cancel all related purchase requests
            $db->prepare("
                UPDATE purchase_requests 
                SET status = 'cancelled' 
                WHERE listing_id = ?
            ")->execute([$id]);
        }
        
        // Delete listing
        $stmt = $db->prepare("DELETE FROM listings WHERE id = ?");
        $stmt->execute([$id]);
        
        sendJSON([
            'success' => true,
            'message' => 'Listing deleted successfully'
        ]);
        
    } catch(PDOException $e) {
        error_log("Delete Listing Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to delete listing'], 500);
    }
}

// Route requests
if ($method === 'GET') {
    switch ($action) {
        case 'list':
            getListings($db);
            break;
        case 'get':
            getListing($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} elseif ($method === 'POST') {
    switch ($action) {
        case 'create':
            createListing($db);
            break;
        case 'update':
            updateListing($db);
            break;
        case 'delete':
            deleteListing($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} else {
    sendJSON(['success' => false, 'message' => 'Method not allowed'], 405);
}
?>
