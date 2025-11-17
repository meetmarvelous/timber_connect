<?php
// Nigerian-specific validation functions
function validateNigerianPhone($phone)
{
    $phone = preg_replace('/\s+/', '', $phone);
    return preg_match('/^(0|\\+234)[7-9][0-1]\\d{8}$/', $phone);
}

function formatNaira($amount)
{
    return '₦' . number_format($amount, 2);
}

function formatNigerianPhone($phone)
{
    if (strpos($phone, '+234') === 0) {
        return '0' . substr($phone, 4);
    }
    return $phone;
}

// Timber industry specific functions
function standardizeDimensions($dim) {
    if (empty($dim)) return '';
    
    // Convert various separators to 'x' and remove spaces
    $dim = str_replace(['×', 'by', ' ', 'X', '*'], 'x', strtolower(trim($dim)));
    
    // Remove any extra characters and ensure proper format
    $dim = preg_replace('/[^0-9x]/', '', $dim);
    
    // Ensure we have the format like "2x4"
    if (preg_match('/(\d+)x(\d+)/', $dim, $matches)) {
        return $matches[1] . 'x' . $matches[2];
    }
    
    return $dim;
}

/**
 * Get all unique dimensions from inventory for dropdown
 */
function getAvailableDimensions($pdo) {
    try {
        $stmt = $pdo->query("SELECT DISTINCT dimensions FROM inventory WHERE is_available = TRUE ORDER BY dimensions");
        $dimensions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_filter($dimensions); // Remove empty values
    } catch (PDOException $e) {
        error_log("Error getting dimensions: " . $e->getMessage());
        return [];
    }
}



function normalizeSpeciesName($name)
{
    global $species_aliases;

    $name = trim(strtolower($name));

    foreach ($species_aliases as $scientific => $aliases) {
        foreach ($aliases as $alias) {
            if (strtolower($alias) === $name) {
                return $scientific;
            }
        }
    }

    return ucwords($name);
}

// Image handling functions
function compressImage($source, $destination, $quality = 75, $maxSize = 200000)
{
    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } else {
        return false;
    }

    // Resize if larger than 1200px on longest side
    $maxWidth = 1200;
    $maxHeight = 800;

    $width = imagesx($image);
    $height = imagesy($image);

    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = $width / $height;

        if ($ratio > 1) {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $ratio;
        } else {
            $newHeight = $maxHeight;
            $newWidth = $maxHeight * $ratio;
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $image = $resized;
    }

    imagejpeg($image, $destination, $quality);
    imagedestroy($image);

    return filesize($destination) <= $maxSize;
}

// Security functions
function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    return hash_equals($_SESSION['csrf_token'], $token);
}
// SIMPLE URL functions - No complex logic
function url($path = '')
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset($path = '')
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function upload($path = '')
{
    return BASE_URL . '/uploads/' . ltrim($path, '/');
}


/**
 * Get the base path (for form actions, etc.)
 */
function base_path($path = '')
{
    return BASE_PATH . '/' . ltrim($path, '/');
}

/**
 * Get the current URL
 */
function current_url()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Redirect to a URL
 */
function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Get query parameters as an array
 */
function get_query_params()
{
    $params = [];
    parse_str($_SERVER['QUERY_STRING'] ?? '', $params);
    return $params;
}

/**
 * Build URL with query parameters
 */
function build_url($path, $params = [])
{
    $url = url($path);
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}

// Database helper functions
/**
 * Get marketer by ID
 */
function get_marketer($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM marketers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get species by ID
 */
function get_species($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM species WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get inventory item by ID
 */
function get_inventory_item($id)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT i.*, s.scientific_name, s.common_names, m.business_name 
        FROM inventory i 
        JOIN species s ON i.species_id = s.id 
        JOIN marketers m ON i.marketer_id = m.id 
        WHERE i.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get common names as array from species JSON
 */
function get_common_names($species)
{
    if (is_array($species) && isset($species['common_names'])) {
        return json_decode($species['common_names'], true);
    }
    return [];
}

/**
 * Get primary common name from species
 */
function get_primary_common_name($species)
{
    $common_names = get_common_names($species);
    return $common_names[0] ?? (is_array($species) ? $species['scientific_name'] : 'Unknown Species');
}

// Form validation functions
/**
 * Validate email format
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate required fields
 */
function validate_required($fields, $data)
{
    $errors = [];
    foreach ($fields as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    return $errors;
}

/**
 * Validate password strength
 */
function validate_password_strength($password)
{
    $errors = [];
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }
    return $errors;
}

// File upload functions
/**
 * Validate uploaded image
 */
function validate_image_upload($file, $max_size = 2097152)
{ // 2MB default
    $errors = [];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed';
        return $errors;
    }

    // Check file size
    if ($file['size'] > $max_size) {
        $errors[] = 'File size must be less than ' . ($max_size / 1024 / 1024) . 'MB';
    }

    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        $errors[] = 'Only JPG, PNG, and GIF images are allowed';
    }

    return $errors;
}

/**
 * Generate unique filename
 */
function generate_unique_filename($original_name, $directory)
{
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    return $filename;
}

// Utility functions
/**
 * Format date for display
 */
function format_date($date_string, $format = 'M j, Y')
{
    return date($format, strtotime($date_string));
}

/**
 * Get time ago string
 */
function time_ago($datetime)
{
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return format_date($datetime);
    }
}

/**
 * Truncate text
 */
function truncate_text($text, $length = 100, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get Nigerian states
 */
function get_nigerian_states()
{
    global $nigerian_states;
    return array_keys($nigerian_states);
}

/**
 * Get LGAs for a state
 */
function get_lgas_for_state($state)
{
    global $nigerian_states;
    return $nigerian_states[$state] ?? [];
}

// Response functions
/**
 * JSON response helper
 */
function json_response($data, $status_code = 200)
{
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Success response
 */
function success_response($message, $data = [])
{
    return json_response([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * Error response
 */
function error_response($message, $errors = [], $status_code = 400)
{
    return json_response([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], $status_code);
}

// Debug functions (remove in production)
/**
 * Debug variable
 */
function debug($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

/**
 * Log message
 */
function log_message($message, $level = 'INFO')
{
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $level: $message" . PHP_EOL;
    file_put_contents(__DIR__ . '/../logs/app.log', $log_entry, FILE_APPEND | LOCK_EX);
}
