<?php
/**
 * Honehube Purchase Requests API
 * Handles purchase requests and negotiations
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
 * Check if user is authenticated
 */
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        sendJSON(['success' => false, 'message' => 'Authentication required'], 401);
    }
}

/**
 * Check if user is admin
 */
function requireAdmin() {
    requireAuth();
    global $db;
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user || $user['role'] !== 'admin') {
        sendJSON(['success' => false, 'message' => 'Admin access required'], 403);
    }
}

/**
 * Get all purchase requests (Admin sees all, users see their own)
 */
function getRequests($db) {
    requireAuth();
    
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $isAdmin = $user['role'] === 'admin';
    
    $status = $_GET['status'] ?? 'all';
    
    $sql = "SELECT pr.*, 
            l.title as listing_title, l.image as listing_image, l.category as listing_category,
            buyer.name as buyer_name, buyer.email as buyer_email, buyer.student_id as buyer_student_id,
            seller.name as seller_name
            FROM purchase_requests pr
            LEFT JOIN listings l ON pr.listing_id = l.id
            LEFT JOIN users buyer ON pr.buyer_id = buyer.id
            LEFT JOIN users seller ON l.user_id = seller.id
            WHERE 1=1";
    $params = [];
    
    if (!$isAdmin) {
        $sql .= " AND pr.buyer_id = ?";
        $params[] = $_SESSION['user_id'];
    }
    
    if ($status !== 'all') {
        $sql .= " AND pr.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY pr.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $requests = $stmt->fetchAll();
    
    sendJSON(['success' => true, 'requests' => $requests]);
}

/**
 * Get single purchase request with negotiation history
 */
function getRequest($db) {
    requireAuth();
    
    $id = intval($_GET['id'] ?? 0);
    
    // Get request details
    $stmt = $db->prepare("
        SELECT pr.*, 
        l.title as listing_title, l.description as listing_description, 
        l.image as listing_image, l.category as listing_category,
        buyer.name as buyer_name, buyer.email as buyer_email, buyer.student_id as buyer_student_id,
        seller.name as seller_name
        FROM purchase_requests pr
        LEFT JOIN listings l ON pr.listing_id = l.id
        LEFT JOIN users buyer ON pr.buyer_id = buyer.id
        LEFT JOIN users seller ON l.user_id = seller.id
        WHERE pr.id = ?
    ");
    $stmt->execute([$id]);
    $request = $stmt->fetch();
    
    if (!$request) {
        sendJSON(['success' => false, 'message' => 'Request not found'], 404);
    }
    
    // Check authorization
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $isAdmin = $user['role'] === 'admin';
    
    if (!$isAdmin && $request['buyer_id'] != $_SESSION['user_id']) {
        sendJSON(['success' => false, 'message' => 'Access denied'], 403);
    }
    
    // Get negotiation history
    $stmt = $db->prepare("
        SELECT n.*, u.name as user_name 
        FROM negotiations n
        LEFT JOIN users u ON n.user_id = u.id
        WHERE n.request_id = ?
        ORDER BY n.created_at ASC
    ");
    $stmt->execute([$id]);
    $negotiations = $stmt->fetchAll();
    
    $request['negotiations'] = $negotiations;
    
    sendJSON(['success' => true, 'request' => $request]);
}

/**
 * Create purchase request (Student)
 */
function createRequest($db) {
    requireAuth();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $listing_id = intval($data['listing_id'] ?? 0);
    $offered_price = isset($data['offered_price']) ? floatval($data['offered_price']) : null;
    $message = sanitizeInput($data['message'] ?? '');
    
    // Validation
    if ($listing_id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid listing ID'], 400);
    }
    
    // Get listing details
    $stmt = $db->prepare("SELECT * FROM listings WHERE id = ? AND status = 'active'");
    $stmt->execute([$listing_id]);
    $listing = $stmt->fetch();
    
    if (!$listing) {
        sendJSON(['success' => false, 'message' => 'Listing not found or not available'], 404);
    }
    
    // Check if user already has a pending request for this listing
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM purchase_requests 
        WHERE listing_id = ? AND buyer_id = ? AND status IN ('pending', 'negotiating', 'accepted')
    ");
    $stmt->execute([$listing_id, $_SESSION['user_id']]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        sendJSON(['success' => false, 'message' => 'You already have an active request for this item'], 400);
    }
    
    // Validate offered price
    if ($offered_price !== null) {
        if ($offered_price <= 0) {
            sendJSON(['success' => false, 'message' => 'Offered price must be greater than 0'], 400);
        }
        if ($offered_price >= $listing['price']) {
            sendJSON(['success' => false, 'message' => 'Offered price must be less than original price'], 400);
        }
    }
    
    try {
        // Create purchase request
        $stmt = $db->prepare("
            INSERT INTO purchase_requests (listing_id, buyer_id, original_price, offered_price, message, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $status = $offered_price !== null ? 'negotiating' : 'pending';
        $stmt->execute([$listing_id, $_SESSION['user_id'], $listing['price'], $offered_price, $message, $status]);
        
        $requestId = $db->lastInsertId();
        
        // If offered price provided, create negotiation entry
        if ($offered_price !== null) {
            $stmt = $db->prepare("
                INSERT INTO negotiations (request_id, user_id, user_type, offered_price, message) 
                VALUES (?, ?, 'buyer', ?, ?)
            ");
            $stmt->execute([$requestId, $_SESSION['user_id'], $offered_price, $message]);
        }
        
        // Get created request
        $stmt = $db->prepare("SELECT * FROM purchase_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();
        
        sendJSON([
            'success' => true,
            'message' => 'Purchase request sent successfully',
            'request' => $request
        ]);
        
    } catch(PDOException $e) {
        error_log("Create Request Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to create request'], 500);
    }
}

/**
 * Accept purchase request (Admin)
 */
function acceptRequest($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid request ID'], 400);
    }
    
    try {
        // Get request details
        $stmt = $db->prepare("SELECT * FROM purchase_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch();
        
        if (!$request) {
            sendJSON(['success' => false, 'message' => 'Request not found'], 404);
        }
        
        if (!in_array($request['status'], ['pending', 'negotiating'])) {
            sendJSON(['success' => false, 'message' => 'Request cannot be accepted'], 400);
        }
        
        // Update request status
        $stmt = $db->prepare("UPDATE purchase_requests SET status = 'accepted', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        
        sendJSON([
            'success' => true,
            'message' => 'Purchase request accepted successfully'
        ]);
        
    } catch(PDOException $e) {
        error_log("Accept Request Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to accept request'], 500);
    }
}

/**
 * Deny purchase request (Admin)
 */
function denyRequest($db) {
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
        sendJSON(['success' => false, 'message' => 'Invalid request ID'], 400);
    }
    
    try {
        // Update request status
        $stmt = $db->prepare("
            UPDATE purchase_requests 
            SET status = 'denied', denial_reason = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$reason, $id]);
        
        sendJSON([
            'success' => true,
            'message' => 'Purchase request denied'
        ]);
        
    } catch(PDOException $e) {
        error_log("Deny Request Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to deny request'], 500);
    }
}

/**
 * Make counter-offer (Admin)
 */
function counterOffer($db) {
    requireAdmin();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    $offered_price = floatval($data['offered_price'] ?? 0);
    $message = sanitizeInput($data['message'] ?? '');
    
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid request ID'], 400);
    }
    
    if ($offered_price <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid offered price'], 400);
    }
    
    try {
        // Get request details
        $stmt = $db->prepare("SELECT * FROM purchase_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch();
        
        if (!$request) {
            sendJSON(['success' => false, 'message' => 'Request not found'], 404);
        }
        
        // Validate counter-offer price
        if ($offered_price <= ($request['offered_price'] ?? 0)) {
            sendJSON(['success' => false, 'message' => 'Counter-offer must be higher than student offer'], 400);
        }
        
        if ($offered_price >= $request['original_price']) {
            sendJSON(['success' => false, 'message' => 'Counter-offer must be less than original price'], 400);
        }
        
        // Update request status
        $stmt = $db->prepare("
            UPDATE purchase_requests 
            SET status = 'negotiating', updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        
        // Add negotiation entry
        $stmt = $db->prepare("
            INSERT INTO negotiations (request_id, user_id, user_type, offered_price, message) 
            VALUES (?, ?, 'seller', ?, ?)
        ");
        $stmt->execute([$id, $_SESSION['user_id'], $offered_price, $message]);
        
        sendJSON([
            'success' => true,
            'message' => 'Counter-offer sent successfully'
        ]);
        
    } catch(PDOException $e) {
        error_log("Counter Offer Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to send counter-offer'], 500);
    }
}

/**
 * Cancel purchase request (Student)
 */
function cancelRequest($db) {
    requireAuth();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $id = intval($data['id'] ?? 0);
    
    if ($id <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid request ID'], 400);
    }
    
    try {
        // Get request details
        $stmt = $db->prepare("SELECT * FROM purchase_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch();
        
        if (!$request) {
            sendJSON(['success' => false, 'message' => 'Request not found'], 404);
        }
        
        // Check authorization
        if ($request['buyer_id'] != $_SESSION['user_id']) {
            sendJSON(['success' => false, 'message' => 'Access denied'], 403);
        }
        
        // Can only cancel pending or negotiating requests
        if (!in_array($request['status'], ['pending', 'negotiating'])) {
            sendJSON(['success' => false, 'message' => 'Request cannot be cancelled'], 400);
        }
        
        // Update request status
        $stmt = $db->prepare("UPDATE purchase_requests SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        
        sendJSON([
            'success' => true,
            'message' => 'Purchase request cancelled'
        ]);
        
    } catch(PDOException $e) {
        error_log("Cancel Request Error: " . $e->getMessage());
        sendJSON(['success' => false, 'message' => 'Failed to cancel request'], 500);
    }
}

// Route requests
if ($method === 'GET') {
    switch ($action) {
        case 'list':
            getRequests($db);
            break;
        case 'get':
            getRequest($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} elseif ($method === 'POST') {
    switch ($action) {
        case 'create':
            createRequest($db);
            break;
        case 'accept':
            acceptRequest($db);
            break;
        case 'deny':
            denyRequest($db);
            break;
        case 'counter':
            counterOffer($db);
            break;
        case 'cancel':
            cancelRequest($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} else {
    sendJSON(['success' => false, 'message' => 'Method not allowed'], 405);
}
?>
