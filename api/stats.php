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
    
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    handleGetRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetRequest() {
    global $pdo, $auth;
    
    $type = $_GET['type'] ?? 'overview';
    $timeframe = $_GET['timeframe'] ?? 'all'; // all, week, month, year
    
    // Basic stats are public, detailed stats require authentication
    $is_authenticated = $auth->isLoggedIn();
    
    switch ($type) {
        case 'overview':
            getOverviewStats($is_authenticated);
            break;
        case 'marketplace':
            if (!$is_authenticated) {
                http_response_code(401);
                echo json_encode(['error' => 'Authentication required for marketplace stats']);
                return;
            }
            getMarketplaceStats($timeframe);
            break;
        case 'species':
            getSpeciesStats();
            break;
        case 'marketers':
            if (!$is_authenticated) {
                http_response_code(401);
                echo json_encode(['error' => 'Authentication required for marketer stats']);
                return;
            }
            getMarketerStats();
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid stats type']);
    }
}

function getOverviewStats($is_authenticated) {
    global $pdo;
    
    $stats = [];
    
    // Public stats
    $stats['total_species'] = (int)$pdo->query("SELECT COUNT(*) FROM species")->fetchColumn();
    $stats['verified_marketers'] = (int)$pdo->query("SELECT COUNT(*) FROM marketers WHERE verification_status = 'verified' AND is_active = TRUE")->fetchColumn();
    $stats['active_listings'] = (int)$pdo->query("SELECT COUNT(*) FROM inventory WHERE is_available = TRUE")->fetchColumn();
    
    // Authenticated-only stats
    if ($is_authenticated) {
        $stats['total_inquiries'] = (int)$pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
        $stats['pending_inquiries'] = (int)$pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'pending'")->fetchColumn();
        $stats['completed_inquiries'] = (int)$pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'completed'")->fetchColumn();
        
        // State distribution
        $state_stmt = $pdo->query("
            SELECT state, COUNT(*) as count 
            FROM marketers 
            WHERE verification_status = 'verified' AND is_active = TRUE 
            GROUP BY state 
            ORDER BY count DESC
        ");
        $stats['state_distribution'] = $state_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'type' => 'overview',
        'data' => $stats,
        'authenticated' => $is_authenticated
    ]);
}

function getMarketplaceStats($timeframe) {
    global $pdo;
    
    $time_condition = getTimeCondition($timeframe);
    
    $stats = [];
    
    // Inventory stats
    $inventory_query = "SELECT 
        COUNT(*) as total_listings,
        AVG(price_per_unit) as avg_price,
        SUM(quantity_available) as total_quantity,
        COUNT(DISTINCT species_id) as unique_species
        FROM inventory 
        WHERE is_available = TRUE";
    
    $inventory_stmt = $pdo->query($inventory_query);
    $stats['inventory'] = $inventory_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Price range by species
    $price_stmt = $pdo->query("
        SELECT s.scientific_name, s.common_names,
               MIN(i.price_per_unit) as min_price,
               MAX(i.price_per_unit) as max_price,
               AVG(i.price_per_unit) as avg_price,
               COUNT(i.id) as listing_count
        FROM species s
        JOIN inventory i ON s.id = i.species_id
        WHERE i.is_available = TRUE
        GROUP BY s.id
        ORDER BY listing_count DESC
        LIMIT 10
    ");
    $stats['price_by_species'] = $price_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process JSON fields
    foreach ($stats['price_by_species'] as &$species) {
        $species['common_names'] = json_decode($species['common_names'], true);
    }
    
    echo json_encode([
        'success' => true,
        'type' => 'marketplace',
        'timeframe' => $timeframe,
        'data' => $stats
    ]);
}

function getSpeciesStats() {
    global $pdo;
    
    $stats = [];
    
    // Most popular species by inventory count
    $popular_stmt = $pdo->query("
        SELECT s.id, s.scientific_name, s.common_names, s.durability, s.timber_value_rank,
               COUNT(i.id) as inventory_count,
               SUM(i.quantity_available) as total_quantity
        FROM species s
        LEFT JOIN inventory i ON s.id = i.species_id AND i.is_available = TRUE
        GROUP BY s.id
        ORDER BY inventory_count DESC, total_quantity DESC
        LIMIT 10
    ");
    $stats['most_popular'] = $popular_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Species by durability
    $durability_stmt = $pdo->query("
        SELECT durability, COUNT(*) as species_count
        FROM species
        GROUP BY durability
        ORDER BY species_count DESC
    ");
    $stats['by_durability'] = $durability_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Species by value rank
    $value_stmt = $pdo->query("
        SELECT timber_value_rank, COUNT(*) as species_count
        FROM species
        GROUP BY timber_value_rank
        ORDER BY timber_value_rank DESC
    ");
    $stats['by_value_rank'] = $value_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process JSON fields
    foreach ($stats['most_popular'] as &$species) {
        $species['common_names'] = json_decode($species['common_names'], true);
    }
    
    echo json_encode([
        'success' => true,
        'type' => 'species',
        'data' => $stats
    ]);
}

function getMarketerStats() {
    global $pdo, $auth;
    
    $stats = [];
    
    // Top marketers by inventory
    $top_marketers_stmt = $pdo->query("
        SELECT m.id, m.business_name, m.city, m.state,
               COUNT(i.id) as listing_count,
               SUM(i.quantity_available) as total_quantity,
               COUNT(DISTINCT inv.id) as inquiry_count
        FROM marketers m
        LEFT JOIN inventory i ON m.id = i.marketer_id AND i.is_available = TRUE
        LEFT JOIN inquiries inv ON m.id = inv.marketer_id
        WHERE m.verification_status = 'verified' AND m.is_active = TRUE
        GROUP BY m.id
        ORDER BY listing_count DESC, inquiry_count DESC
        LIMIT 10
    ");
    $stats['top_marketers'] = $top_marketers_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Marketers by state
    $state_stmt = $pdo->query("
        SELECT state, COUNT(*) as marketer_count
        FROM marketers
        WHERE verification_status = 'verified' AND is_active = TRUE
        GROUP BY state
        ORDER BY marketer_count DESC
    ");
    $stats['by_state'] = $state_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If user is a marketer, show their specific stats
    if ($auth->isMarketer()) {
        $marketer_id = $_SESSION['marketer_id'];
        
        $personal_stmt = $pdo->prepare("
            SELECT 
                COUNT(i.id) as total_listings,
                SUM(i.quantity_available) as total_stock,
                COUNT(inv.id) as total_inquiries,
                SUM(CASE WHEN inv.status = 'pending' THEN 1 ELSE 0 END) as pending_inquiries,
                SUM(CASE WHEN inv.status = 'completed' THEN 1 ELSE 0 END) as completed_inquiries
            FROM marketers m
            LEFT JOIN inventory i ON m.id = i.marketer_id
            LEFT JOIN inquiries inv ON m.id = inv.marketer_id
            WHERE m.id = ?
            GROUP BY m.id
        ");
        $personal_stmt->execute([$marketer_id]);
        $stats['personal'] = $personal_stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'type' => 'marketers',
        'data' => $stats,
        'user_role' => $auth->isLoggedIn() ? ($auth->isMarketer() ? 'marketer' : 'admin') : 'guest'
    ]);
}

function getTimeCondition($timeframe) {
    switch ($timeframe) {
        case 'week':
            return " AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        case 'month':
            return " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        case 'year':
            return " AND created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        default:
            return "";
    }
}
?>