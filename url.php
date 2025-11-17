<?php
// Simple page to check base URL configuration
session_start();

// Include config to get the dynamic base URL logic
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base URL Check - WOOD CONNECT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .info-card { border-left: 4px solid #0d6efd; }
        .success-card { border-left: 4px solid #198754; }
        .warning-card { border-left: 4px solid #ffc107; }
        .code-block { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-5">
                    <h1 class="display-4 text-primary">
                        <i class="fas fa-link me-2"></i>Base URL Check
                    </h1>
                    <p class="lead">Testing Dynamic Base URL Configuration</p>
                </div>

                <!-- Server Information -->
                <div class="card info-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-server me-2"></i>Server Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>HTTP_HOST:</strong><br>
                                <code><?php echo $_SERVER['HTTP_HOST']; ?></code>
                            </div>
                            <div class="col-md-6">
                                <strong>SCRIPT_NAME:</strong><br>
                                <code><?php echo $_SERVER['SCRIPT_NAME']; ?></code>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>HTTPS:</strong><br>
                                <code><?php echo isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'Not set'; ?></code>
                            </div>
                            <div class="col-md-6">
                                <strong>Protocol:</strong><br>
                                <code><?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"; ?></code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Base URL Configuration -->
                <div class="card success-card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Base URL Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>BASE_URL:</strong><br>
                                <code class="text-success"><?php echo BASE_URL; ?></code>
                            </div>
                            <div class="col-md-6">
                                <strong>BASE_PATH:</strong><br>
                                <code class="text-success"><?php echo BASE_PATH; ?></code>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>SITE_URL:</strong><br>
                                <code class="text-success"><?php echo SITE_URL; ?></code>
                            </div>
                            <div class="col-md-6">
                                <strong>UPLOAD_PATH:</strong><br>
                                <code><?php echo UPLOAD_PATH; ?></code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Helper Functions Test -->
                <div class="card warning-card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-functions me-2"></i>Helper Functions Test</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>url('marketplace/'):</strong><br>
                                <code><?php echo url('marketplace/'); ?></code>
                            </div>
                            <div class="col-md-6">
                                <strong>asset('css/style.css'):</strong><br>
                                <code><?php echo asset('css/style.css'); ?></code>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>base_path('login.php'):</strong><br>
                                <code><?php echo base_path('login.php'); ?></code>
                            </div>
                            <div class="col-md-6">
                                <strong>upload('images/test.jpg'):</strong><br>
                                <code><?php echo upload('images/test.jpg'); ?></code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Link Tests -->
                <div class="card info-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Link Tests</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Test if these links work correctly:</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?php echo url('/'); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-home me-1"></i>Homepage
                            </a>
                            <a href="<?php echo url('marketplace/'); ?>" class="btn btn-outline-success">
                                <i class="fas fa-store me-1"></i>Marketplace
                            </a>
                            <a href="<?php echo url('login.php'); ?>" class="btn btn-outline-warning">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                            <a href="<?php echo asset('css/style.css'); ?>" class="btn btn-outline-info" target="_blank">
                                <i class="fas fa-palette me-1"></i>CSS File
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Current Page URL:</h6>
                            <code><?php echo $base_url . $_SERVER['REQUEST_URI']; ?></code>
                        </div>
                    </div>
                </div>

                <!-- Quick Fix Instructions -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5><i class="fas fa-lightbulb me-2 text-warning"></i>What to Look For:</h5>
                        <ul class="mb-0">
                            <li><strong>BASE_URL</strong> should match your website's domain and path</li>
                            <li><strong>BASE_PATH</strong> should be the directory path from web root</li>
                            <li>All helper functions should generate correct URLs</li>
                            <li>Links should work when clicked</li>
                        </ul>
                    </div>
                </div>

                <!-- Debug Information -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bug me-2"></i>Debug Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="code-block">
                            <strong>Full $_SERVER array:</strong><br>
                            <pre><?php 
                            $debug_info = [
                                'HTTP_HOST' => $_SERVER['HTTP_HOST'],
                                'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],
                                'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                                'HTTPS' => $_SERVER['HTTPS'] ?? 'Not set',
                                'SERVER_NAME' => $_SERVER['SERVER_NAME'],
                                'PHP_SELF' => $_SERVER['PHP_SELF']
                            ];
                            print_r($debug_info); 
                            ?></pre>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?php echo url('/'); ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Homepage
                    </a>
                    <button onclick="location.reload()" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>Reload Page
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>