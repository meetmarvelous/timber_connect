<?php
require_once '../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
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
    global $nigerian_states;
    
    $state = $_GET['state'] ?? '';
    
    if (empty($state)) {
        // Return all states if no specific state requested
        $states_list = array_keys($nigerian_states);
        echo json_encode([
            'success' => true,
            'data' => $states_list,
            'count' => count($states_list)
        ]);
        return;
    }
    
    if (!isset($nigerian_states[$state])) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'State not found',
            'available_states' => array_keys($nigerian_states)
        ]);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'state' => $state,
        'lgas' => $nigerian_states[$state],
        'count' => count($nigerian_states[$state])
    ]);
}

function handlePostRequest() {
    // For future expansion - could accept JSON payload for dynamic LGA updates
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        return;
    }
    
    // Example: Could be used to add new LGAs (admin function)
    // For now, just return the received data
    echo json_encode([
        'success' => true,
        'message' => 'POST request received',
        'received_data' => $input
    ]);
}
?>