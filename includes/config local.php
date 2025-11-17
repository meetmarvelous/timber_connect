<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'timber_connect');
define('DB_USER', 'root');
define('DB_PASS', '');

// SIMPLE Base URL calculation
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// Get the current script directory and go up to project root
$current_dir = dirname($_SERVER['SCRIPT_NAME']);

// If we're in a subdirectory, extract just the project root path
if (strpos($current_dir, '/timber-connect') !== false) {
    $base_path = '/timber-connect';
} else {
    // For domain deployment, use empty base path
    $base_path = '';
}

$base_url = $protocol . '://' . $host . $base_path;

// Application settings
define('SITE_NAME', 'WOOD CONNECT');
define('SITE_URL', $base_url);
define('BASE_URL', $base_url);
define('BASE_PATH', $base_path);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Nigerian states and LGAs
$nigerian_states = [
    'Ondo' => ['Akure South', 'Akure North', 'Ondo West', 'Ondo East', 'Owo', 'Okitipupa'],
    'Ekiti' => ['Ado Ekiti', 'Ikere', 'Ise/Orun', 'Emure', 'Ijero'],
    'Osun' => ['Osogbo', 'Ilesa', 'Ife Central', 'Ife East', 'Iwo'],
    'Oyo' => ['Ibadan North', 'Ibadan South', 'Ibadan South West', 'Ogbomoso North', 'Oyo East'],
    'Ogun' => ['Abeokuta South', 'Abeokuta North', 'Sagamu', 'Ijebu Ode', 'Ilaro'],
    'Lagos' => ['Lagos Island', 'Lagos Mainland', 'Ikeja', 'Surulere', 'Alimosho']
];

// Timber species aliases for normalization
$species_aliases = [
    'Milicia excelsa' => ['Iroko', 'Odum', 'Mvule'],
    'Khaya ivorensis' => ['Mahogany', 'Oganwo', 'African Mahogany'],
    'Tectona grandis' => ['Teak', 'Teakwood'],
    'Gmelina arborea' => ['Gmelina', 'Beechwood'],
    'Nauclea diderrichii' => ['Opepe', 'Bilinga'],
    'Terminalia superba' => ['Afara', 'White Afara', 'Limba']
];

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("
        <!DOCTYPE html>
        <html>
        <head>
            <title>Database Connection Error - WOOD CONNECT</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container py-5'>
                <div class='row justify-content-center'>
                    <div class='col-md-6'>
                        <div class='card shadow'>
                            <div class='card-header bg-danger text-white'>
                                <h4 class='mb-0'><i class='fas fa-database me-2'></i>Database Connection Error</h4>
                            </div>
                            <div class='card-body text-center p-5'>
                                <i class='fas fa-database fa-4x text-muted mb-4'></i>
                                <h3>Database Connection Failed</h3>
                                <p class='text-muted mb-4'>
                                    Unable to connect to the database. Please check your database configuration.
                                </p>
                                <div class='alert alert-info'>
                                    <strong>Error Details:</strong><br>
                                    " . $e->getMessage() . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
    ");
}

// Include other required files
require_once 'functions.php';
require_once 'auth.php';
