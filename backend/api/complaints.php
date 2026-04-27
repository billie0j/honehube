<?php
/**
 * Honehube Complaints API
 * Handles item malfunction complaints and issues reporting
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
 * Require authentication
 */
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        sendJSON(['success' => false, 'message' => 'Authentication required'], 401);
    }
}

/**
 * Require admin role
 */
function requireAdmin($db) {
    requireAuth();
    $stmt = $db->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user || $user['role'] !== 'admin') {
        sendJSON(['success' => false, 'message' => 'Admin access required'], 403);
    }
}

/**
 * Submit a complaint
 */
function submitComplaint($db) {
    requireAuth();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    // Validate input
    $itemId = intval($data['item_id'] ?? 0);
    $purchaseRequestId = isset($data['purchase_request_id']) ? intval($data['purchase_request_id']) : null;
    $complaintType = sanitizeInput($data['complaint_type'] ?? 'malfunction');
    $subject = sanitizeInput($data['subject'] ?? '');
    $description = sanitizeInput($data['description'] ?? '');
    $userId = $_SESSION['user_id'];
    
    // Validation
    if ($itemId <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid item ID'], 400);
    }
    
    if (empty($subject) || strlen($subject) < 5) {
        sendJSON(['success' => false, 'message' => 'Subject must be at least 5 characters'], 400);
    }
    
    if (empty($description) || strlen($description) < 10) {
        sendJSON(['success' => false, 'message' => 'Description must be at least 10 characters'], 400);
    }
    
    // Verify item exists
    $stmt = $db->prepare("SELECT item_id, item_name FROM accessories WHERE item_id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();
    
    if (!$item) {
        sendJSON(['success' => false, 'message' => 'Item not found'], 404);
    }
    
    try {
        // Insert complaint
        $stmt = $db->prepare("
            INSERT INTO complaints (item_id, user_id, purchase_request_id, complaint_type, subject, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$itemId, $userId, $purchaseRequestId, $complaintType, $subject, $description]);
        
        $complaintId = $db->lastInsertId();
        
        // Get complaint details
        $stmt = $db->prepare("
            SELECT c.*, a.item_name, u.full_name as user_name, u.email as user_email
            FROM complaints c
            JOIN accessories a ON c.item_id = a.item_id
            JOIN users u ON c.user_id = u.user_id
            WHERE c.complaint_id = ?
        ");
        $stmt->execute([$complaintId]);
        $complaint = $stmt->fetch();
        
        // Log audit trail
        logAudit('complaint_submitted', "Complaint submitted for item: {$item['item_name']} by user ID: {$userId}", 'complaints', $complaintId);
        
        sendJSON([
            'success' => true,
            'message' => 'Complaint submitted successfully. Admin will review it shortly.',
            'complaint' => $complaint
        ]);
        
    } catch(PDOException $e) {
        sendJSON(handleDatabaseError($e, 'complaint submission'), 500);
    }
}

/**
 * Get all complaints (Admin only)
 */
function getAllComplaints($db) {
    requireAdmin($db);
    
    try {
        $status = $_GET['status'] ?? '';
        
        $sql = "
            SELECT c.*, 
                   a.item_name, a.image as item_image, a.original_price,
                   u.full_name as user_name, u.email as user_email
            FROM complaints c
            JOIN accessories a ON c.item_id = a.item_id
            JOIN users u ON c.user_id = u.user_id
        ";
        
        if ($status) {
            $sql .= " WHERE c.status = ?";
            $stmt = $db->prepare($sql . " ORDER BY c.created_at DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $db->prepare($sql . " ORDER BY c.created_at DESC");
            $stmt->execute();
        }
        
        $complaints = $stmt->fetchAll();
        
        sendJSON([
            'success' => true,
            'complaints' => $complaints,
            'count' => count($complaints)
        ]);
        
    } catch(PDOException $e) {
        sendJSON(handleDatabaseError($e, 'get complaints'), 500);
    }
}

/**
 * Get user's complaints
 */
function getUserComplaints($db) {
    requireAuth();
    
    try {
        $userId = $_SESSION['user_id'];
        
        $stmt = $db->prepare("
            SELECT c.*, 
                   a.item_name, a.image as item_image, a.original_price
            FROM complaints c
            JOIN accessories a ON c.item_id = a.item_id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$userId]);
        
        $complaints = $stmt->fetchAll();
        
        sendJSON([
            'success' => true,
            'complaints' => $complaints,
            'count' => count($complaints)
        ]);
        
    } catch(PDOException $e) {
        sendJSON(handleDatabaseError($e, 'get user complaints'), 500);
    }
}

/**
 * Get single complaint
 */
function getComplaint($db) {
    requireAuth();
    
    $complaintId = intval($_GET['id'] ?? 0);
    
    if ($complaintId <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid complaint ID'], 400);
    }
    
    try {
        $stmt = $db->prepare("
            SELECT c.*, 
                   a.item_name, a.image as item_image, a.original_price, a.description as item_description,
                   u.full_name as user_name, u.email as user_email
            FROM complaints c
            JOIN accessories a ON c.item_id = a.item_id
            JOIN users u ON c.user_id = u.user_id
            WHERE c.complaint_id = ?
        ");
        $stmt->execute([$complaintId]);
        
        $complaint = $stmt->fetch();
        
        if (!$complaint) {
            sendJSON(['success' => false, 'message' => 'Complaint not found'], 404);
        }
        
        // Check if user owns this complaint or is admin
        $stmt = $db->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($complaint['user_id'] != $_SESSION['user_id'] && $user['role'] !== 'admin') {
            sendJSON(['success' => false, 'message' => 'Access denied'], 403);
        }
        
        sendJSON([
            'success' => true,
            'complaint' => $complaint
        ]);
        
    } catch(PDOException $e) {
        sendJSON(handleDatabaseError($e, 'get complaint'), 500);
    }
}

/**
 * Update complaint status (Admin only)
 */
function updateComplaintStatus($db) {
    requireAdmin($db);
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $complaintId = intval($data['complaint_id'] ?? 0);
    $status = sanitizeInput($data['status'] ?? '');
    $adminResponse = sanitizeInput($data['admin_response'] ?? '');
    
    if ($complaintId <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid complaint ID'], 400);
    }
    
    $validStatuses = ['pending', 'investigating', 'resolved', 'rejected'];
    if (!in_array($status, $validStatuses)) {
        sendJSON(['success' => false, 'message' => 'Invalid status'], 400);
    }
    
    try {
        // Update complaint
        $resolvedAt = ($status === 'resolved' || $status === 'rejected') ? date('Y-m-d H:i:s') : null;
        
        $stmt = $db->prepare("
            UPDATE complaints 
            SET status = ?, admin_response = ?, resolved_at = ?, updated_at = NOW()
            WHERE complaint_id = ?
        ");
        $stmt->execute([$status, $adminResponse, $resolvedAt, $complaintId]);
        
        // Get updated complaint
        $stmt = $db->prepare("
            SELECT c.*, 
                   a.item_name,
                   u.full_name as user_name, u.email as user_email
            FROM complaints c
            JOIN accessories a ON c.item_id = a.item_id
            JOIN users u ON c.user_id = u.user_id
            WHERE c.complaint_id = ?
        ");
        $stmt->execute([$complaintId]);
        $complaint = $stmt->fetch();
        
        // Log audit trail
        logAudit('complaint_updated', "Complaint #{$complaintId} status changed to: {$status}", 'complaints', $complaintId);
        
        sendJSON([
            'success' => true,
            'message' => 'Complaint updated successfully',
            'complaint' => $complaint
        ]);
        
    } catch(PDOException $e) {
        sendJSON(handleDatabaseError($e, 'update complaint'), 500);
    }
}

/**
 * Delete complaint (Admin only)
 */
function deleteComplaint($db) {
    requireAdmin($db);
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate CSRF token
    if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
        sendJSON(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $complaintId = intval($data['complaint_id'] ?? 0);
    
    if ($complaintId <= 0) {
        sendJSON(['success' => false, 'message' => 'Invalid complaint ID'], 400);
    }
    
    try {
        $stmt = $db->prepare("DELETE FROM complaints WHERE complaint_id = ?");
        $stmt->execute([$complaintId]);
        
        // Log audit trail
        logAudit('complaint_deleted', "Complaint #{$complaintId} deleted", 'complaints', $complaintId);
        
        sendJSON([
            'success' => true,
            'message' => 'Complaint deleted successfully'
        ]);
        
    } catch(PDOException $e) {
        sendJSON(handleDatabaseError($e, 'delete complaint'), 500);
    }
}

// Route requests
if ($method === 'POST') {
    switch ($action) {
        case 'submit':
            submitComplaint($db);
            break;
        case 'update_status':
            updateComplaintStatus($db);
            break;
        case 'delete':
            deleteComplaint($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} elseif ($method === 'GET') {
    switch ($action) {
        case 'list':
            getAllComplaints($db);
            break;
        case 'my_complaints':
            getUserComplaints($db);
            break;
        case 'get':
            getComplaint($db);
            break;
        default:
            sendJSON(['success' => false, 'message' => 'Invalid action'], 400);
    }
} else {
    sendJSON(['success' => false, 'message' => 'Method not allowed'], 405);
}
?>
