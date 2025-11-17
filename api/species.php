<?php
require_once '../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

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
    
    $featured = isset($_GET['featured']) && $_GET['featured'] === 'true';
    $search = $_GET['search'] ?? '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $species_id = $_GET['id'] ?? null;
    
    // Validate limit and offset
    $limit = max(1, min(100, $limit));
    $offset = max(0, $offset);
    
    if ($species_id) {
        // Get specific species
        $query = "SELECT s.*, COUNT(i.id) as marketer_count
                  FROM species s
                  LEFT JOIN inventory i ON s.id = i.species_id AND i.is_available = TRUE
                  WHERE s.id = ?
                  GROUP BY s.id";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$species_id]);
        $species = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$species) {
            http_response_code(404);
            echo json_encode(['error' => 'Species not found']);
            return;
        }
        
        // Process JSON fields and add image URL
        $species['common_names'] = json_decode($species['common_names'], true);
        $species['common_uses'] = json_decode($species['common_uses'], true);
        $species['image_url'] = $species['image_path'] ? upload('species/' . $species['image_path']) : asset('images/species-placeholder.jpg');
        
        echo json_encode([
            'success' => true,
            'data' => $species
        ]);
        return;
    }
    
    // Build query for multiple species
    $query = "SELECT s.*, 
                     COUNT(DISTINCT i.id) as marketer_count,
                     COUNT(DISTINCT inv.id) as inquiry_count
              FROM species s
              LEFT JOIN inventory i ON s.id = i.species_id AND i.is_available = TRUE
              LEFT JOIN inquiries inv ON s.id = inv.species_id
              WHERE 1=1";
    
    $params = [];
    
    if ($featured) {
        $query .= " AND s.timber_value_rank <= 3";
    }
    
    if (!empty($search)) {
        $query .= " AND (s.scientific_name LIKE ? OR JSON_CONTAINS(s.common_names, ?))";
        $searchTerm = "%$search%";
        $searchJson = json_encode($search);
        $params[] = $searchTerm;
        $params[] = $searchJson;
    }
    
    $query .= " GROUP BY s.id 
                ORDER BY s.timber_value_rank ASC, marketer_count DESC 
                LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $species = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count for pagination
    $count_query = "SELECT COUNT(*) FROM species WHERE 1=1";
    $count_params = [];
    
    if ($featured) {
        $count_query .= " AND timber_value_rank <= 3";
    }
    
    if (!empty($search)) {
        $count_query .= " AND (scientific_name LIKE ? OR JSON_CONTAINS(common_names, ?))";
        $count_params[] = "%$search%";
        $count_params[] = json_encode($search);
    }
    
    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->execute($count_params);
    $total_count = $count_stmt->fetchColumn();
    
    // Process species data
    foreach ($species as &$spec) {
        $spec['common_names'] = json_decode($spec['common_names'], true);
        $spec['common_uses'] = json_decode($spec['common_uses'], true);
        $spec['image_url'] = $spec['image_path'] ? upload('species/' . $spec['image_path']) : asset('images/species-placeholder.jpg');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $species,
        'pagination' => [
            'total' => (int)$total_count,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + count($species)) < $total_count
        ]
    ]);
}
?>