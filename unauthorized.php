<?php
require_once 'includes/config.php';
$page_title = "Access Denied - WOOD CONNECT";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <?php include 'includes/header.php'; ?>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="card shadow-sm border-0">
                    <div class="card-body py-5">
                        <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                        <h1 class="display-4 text-danger">Access Denied</h1>
                        <p class="lead mb-4">You don't have permission to access this page.</p>
                        
                        <?php if ($auth->isLoggedIn()): ?>
                            <p class="text-muted mb-4">
                                You are logged in as 
                                <strong>
                                    <?php echo $auth->isMarketer() ? $_SESSION['marketer_business_name'] : $_SESSION['admin_username']; ?>
                                </strong>
                            </p>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <a href="/" class="btn btn-primary btn-lg px-4">Go Home</a>
                            <?php if (!$auth->isLoggedIn()): ?>
                                <a href="/login.php" class="btn btn-outline-primary btn-lg px-4">Login</a>
                            <?php else: ?>
                                <a href="/logout.php" class="btn btn-outline-secondary btn-lg px-4">Logout</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>