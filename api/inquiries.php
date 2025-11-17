<?php
require_once '../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Enable CORS for API requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGetRequest();
            break;
        case 'POST':
            handlePostRequest();
            break;
        case 'PUT':
            handlePutRequest();
            break;
        case 'DELETE':
            handleDeleteRequest();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetRequest() {
    global $pdo, $auth;
    
    // Check authentication for sensitive data
    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        return;
    }
    
    $inquiry_id = $_GET['id'] ?? null;
    $marketer_id = $_GET['marketer_id'] ?? null;
    $status = $_GET['status'] ?? null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    // Validate limit and offset
    $limit = max(1, min(100, $limit)); // Limit between 1-100
    $offset = max(0, $offset);
    
    if ($inquiry_id) {
        // Get specific inquiry
        $query = "SELECT i.*, s.scientific_name, s.common_names, 
                         m.business_name as marketer_business
                  FROM inquiries i
                  JOIN species s ON i.species_id = s.id
                  JOIN marketers m ON i.marketer_id = m.id
                  WHERE i.id = ?";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$inquiry_id]);
        $inquiry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$inquiry) {
            http_response_code(404);
            echo json_encode(['error' => 'Inquiry not found']);
            return;
        }
        
        // Check permissions
        if ($auth->isMarketer() && $inquiry['marketer_id'] != $_SESSION['marketer_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        // Process JSON fields
        $inquiry['common_names'] = json_decode($inquiry['common_names'], true);
        
        echo json_encode([
            'success' => true,
            'data' => $inquiry
        ]);
        return;
    }
    
    // Build query for multiple inquiries
    $query = "SELECT i.*, s.scientific_name, s.common_names, 
                     m.business_name as marketer_business
              FROM inquiries i
              JOIN species s ON i.species_id = s.id
              JOIN marketers m ON i.marketer_id = m.id
              WHERE 1=1";
    
    $params = [];
    
    // Apply filters based on user role
    if ($auth->isMarketer()) {
        $query .= " AND i.marketer_id = ?";
        $params[] = $_SESSION['marketer_id'];
    }
    
    if ($marketer_id && $auth->isAdmin()) {
        $query .= " AND i.marketer_id = ?";
        $params[] = $marketer_id;
    }
    
    if ($status) {
        $query .= " AND i.status = ?";
        $params[] = $status;
    }
    
    $query .= " ORDER BY i.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count for pagination
    $count_query = "SELECT COUNT(*) FROM inquiries i WHERE 1=1";
    $count_params = [];
    
    if ($auth->isMarketer()) {
        $count_query .= " AND i.marketer_id = ?";
        $count_params[] = $_SESSION['marketer_id'];
    }
    
    if ($marketer_id && $auth->isAdmin()) {
        $count_query .= " AND i.marketer_id = ?";
        $count_params[] = $marketer_id;
    }
    
    if ($status) {
        $count_query .= " AND i.status = ?";
        $count_params[] = $status;
    }
    
    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->execute($count_params);
    $total_count = $count_stmt->fetchColumn();
    
    // Process JSON fields
    foreach ($inquiries as &$inquiry) {
        $inquiry['common_names'] = json_decode($inquiry['common_names'], true);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $inquiries,
        'pagination' => [
            'total' => (int)$total_count,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + count($inquiries)) < $total_count
        ]
    ]);
}

function handlePostRequest() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        return;
    }
    
    // Validate required fields
    $required_fields = ['buyer_name', 'buyer_phone', 'marketer_id', 'species_id', 'dimensions', 'quantity'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            return;
        }
    }
    
    // Validate Nigerian phone number
    if (!validateNigerianPhone($input['buyer_phone'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid Nigerian phone number format']);
        return;
    }
    
    // Validate quantity
    $quantity = (int)$input['quantity'];
    if ($quantity <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Quantity must be greater than 0']);
        return;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Check if marketer exists and is verified
        $marketer_stmt = $pdo->prepare("
            SELECT id, business_name 
            FROM marketers 
            WHERE id = ? AND verification_status = 'verified' AND is_active = TRUE
        ");
        $marketer_stmt->execute([$input['marketer_id']]);
        $marketer = $marketer_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$marketer) {
            throw new Exception('Marketer not found or not verified');
        }
        
        // Check if species exists
        $species_stmt = $pdo->prepare("SELECT id, scientific_name, common_names FROM species WHERE id = ?");
        $species_stmt->execute([$input['species_id']]);
        $species = $species_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$species) {
            throw new Exception('Timber species not found');
        }
        
        // Insert inquiry
        $stmt = $pdo->prepare("
            INSERT INTO inquiries 
            (buyer_name, buyer_phone, buyer_email, marketer_id, species_id, dimensions, quantity, message)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            sanitizeInput($input['buyer_name']),
            sanitizeInput($input['buyer_phone']),
            sanitizeInput($input['buyer_email'] ?? ''),
            $input['marketer_id'],
            $input['species_id'],
            standardizeDimensions($input['dimensions']),
            $quantity,
            sanitizeInput($input['message'] ?? '')
        ]);
        
        $inquiry_id = $pdo->lastInsertId();
        
        // Commit transaction
        $pdo->commit();
        
        // Get the created inquiry with full details
        $inquiry_stmt = $pdo->prepare("
            SELECT i.*, s.scientific_name, s.common_names, m.business_name as marketer_business
            FROM inquiries i
            JOIN species s ON i.species_id = s.id
            JOIN marketers m ON i.marketer_id = m.id
            WHERE i.id = ?
        ");
        $inquiry_stmt->execute([$inquiry_id]);
        $inquiry = $inquiry_stmt->fetch(PDO::FETCH_ASSOC);
        
        // Process JSON fields
        $inquiry['common_names'] = json_decode($inquiry['common_names'], true);
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Inquiry submitted successfully',
            'data' => $inquiry,
            'inquiry_id' => $inquiry_id
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function handlePutRequest() {
    global $pdo, $auth;
    
    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        return;
    }
    
    $inquiry_id = $input['id'] ?? null;
    $status = $input['status'] ?? null;
    $admin_notes = $input['admin_notes'] ?? null;
    
    if (!$inquiry_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Inquiry ID is required']);
        return;
    }
    
    try {
        // Check if inquiry exists and user has permission
        $check_stmt = $pdo->prepare("
            SELECT i.*, m.business_name 
            FROM inquiries i
            JOIN marketers m ON i.marketer_id = m.id
            WHERE i.id = ?
        ");
        $check_stmt->execute([$inquiry_id]);
        $inquiry = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$inquiry) {
            http_response_code(404);
            echo json_encode(['error' => 'Inquiry not found']);
            return;
        }
        
        // Check permissions
        if ($auth->isMarketer() && $inquiry['marketer_id'] != $_SESSION['marketer_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        // Build update query based on provided fields
        $update_fields = [];
        $update_params = [];
        
        if ($status && $auth->isMarketer()) {
            // Marketers can only update status
            if (in_array($status, ['pending', 'contacted', 'completed', 'cancelled'])) {
                $update_fields[] = 'status = ?';
                $update_params[] = $status;
            }
        }
        
        if ($admin_notes && $auth->isAdmin()) {
            // Only admins can add admin notes
            $update_fields[] = 'admin_notes = ?';
            $update_params[] = sanitizeInput($admin_notes);
        }
        
        if ($status && $auth->isAdmin()) {
            // Admins can update status too
            $update_fields[] = 'status = ?';
            $update_params[] = $status;
        }
        
        if (empty($update_fields)) {
            http_response_code(400);
            echo json_encode(['error' => 'No valid fields to update']);
            return;
        }
        
        $update_fields[] = 'updated_at = NOW()';
        $update_params[] = $inquiry_id;
        
        $update_query = "UPDATE inquiries SET " . implode(', ', $update_fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute($update_params);
        
        // Get updated inquiry
        $updated_stmt = $pdo->prepare("
            SELECT i.*, s.scientific_name, s.common_names, m.business_name as marketer_business
            FROM inquiries i
            JOIN species s ON i.species_id = s.id
            JOIN marketers m ON i.marketer_id = m.id
            WHERE i.id = ?
        ");
        $updated_stmt->execute([$inquiry_id]);
        $updated_inquiry = $updated_stmt->fetch(PDO::FETCH_ASSOC);
        
        // Process JSON fields
        $updated_inquiry['common_names'] = json_decode($updated_inquiry['common_names'], true);
        
        echo json_encode([
            'success' => true,
            'message' => 'Inquiry updated successfully',
            'data' => $updated_inquiry
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function handleDeleteRequest() {
    global $pdo, $auth;
    
    if (!$auth->isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $inquiry_id = $input['id'] ?? $_GET['id'] ?? null;
    
    if (!$inquiry_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Inquiry ID is required']);
        return;
    }
    
    try {
        // Check if inquiry exists
        $check_stmt = $pdo->prepare("SELECT id FROM inquiries WHERE id = ?");
        $check_stmt->execute([$inquiry_id]);
        
        if (!$check_stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Inquiry not found']);
            return;
        }
        
        // Delete inquiry
        $delete_stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
        $delete_stmt->execute([$inquiry_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Inquiry deleted successfully',
            'deleted_id' => $inquiry_id
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>