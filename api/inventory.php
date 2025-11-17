<?php
require_once '../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . BASE_URL);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
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
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetRequest() {
    global $pdo;
    
    $marketer_id = $_GET['marketer_id'] ?? null;
    $species_id = $_GET['species_id'] ?? null;
    $state = $_GET['state'] ?? null;
    $city = $_GET['city'] ?? null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    $query = "SELECT i.*, 
                     s.scientific_name, s.common_names, s.family,
                     m.business_name, m.owner_name, m.city, m.state, 
                     m.local_government, m.phone, m.email, m.verification_status
              FROM inventory i
              JOIN species s ON i.species_id = s.id
              JOIN marketers m ON i.marketer_id = m.id
              WHERE i.is_available = TRUE AND m.verification_status = 'verified' AND m.is_active = TRUE";
    
    $params = [];
    
    if ($marketer_id) {
        $query .= " AND i.marketer_id = ?";
        $params[] = $marketer_id;
    }
    
    if ($species_id) {
        $query .= " AND i.species_id = ?";
        $params[] = $species_id;
    }
    
    if ($state) {
        $query .= " AND m.state = ?";
        $params[] = $state;
    }
    
    if ($city) {
        $query .= " AND m.city LIKE ?";
        $params[] = "%$city%";
    }
    
    $query .= " ORDER BY i.updated_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process JSON fields and add image URLs
    foreach ($inventory as &$item) {
        $item['common_names'] = json_decode($item['common_names'], true);
        $item['image_url'] = $item['image_path'] ? upload('inventory/' . $item['image_path']) : asset('images/timber-placeholder.jpg');
        $item['marketer_profile_url'] = url('marketplace/profile.php?marketer_id=' . $item['marketer_id']);
        $item['inquiry_url'] = url('marketplace/inquiry.php?inventory_id=' . $item['id']);
        
        // Remove sensitive data
        unset($item['password_hash']);
    }
    
    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total 
                    FROM inventory i 
                    JOIN marketers m ON i.marketer_id = m.id 
                    WHERE i.is_available = TRUE AND m.verification_status = 'verified'";
    
    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->execute();
    $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $inventory,
        'pagination' => [
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + count($inventory)) < $total
        ]
    ]);
}

function handlePostRequest() {
    global $pdo, $auth;
    
    // Check if user is authenticated as marketer
    if (!$auth->isLoggedIn() || !$auth->isMarketer()) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        return;
    }
    
    $marketer_id = $_SESSION['marketer_id'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Validate required fields
    $required = ['species_id', 'dimensions', 'price_per_unit', 'quantity_available'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            return;
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO inventory 
            (marketer_id, species_id, dimensions, price_per_unit, quantity_available, unit_type, quality_grade, description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $marketer_id,
            $input['species_id'],
            standardizeDimensions($input['dimensions']),
            $input['price_per_unit'],
            $input['quantity_available'],
            $input['unit_type'] ?? 'length',
            $input['quality_grade'] ?? 'standard',
            sanitizeInput($input['description'] ?? '')
        ]);
        
        $inventory_id = $pdo->lastInsertId();
        
        $pdo->commit();
        
        // Return the created inventory item
        $stmt = $pdo->prepare("SELECT i.*, s.scientific_name, s.common_names 
                              FROM inventory i 
                              JOIN species s ON i.species_id = s.id 
                              WHERE i.id = ?");
        $stmt->execute([$inventory_id]);
        $new_item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $new_item['common_names'] = json_decode($new_item['common_names'], true);
        $new_item['image_url'] = $new_item['image_path'] ? upload('inventory/' . $new_item['image_path']) : asset('images/timber-placeholder.jpg');
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Inventory item created successfully',
            'data' => $new_item
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create inventory item: ' . $e->getMessage()]);
    }
}
?>