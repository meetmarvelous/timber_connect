<?php
require_once 'includes/config.php';

if ($auth->isLoggedIn()) {
    header('Location: /dashboard/');
    exit;
}

$page_title = "Forgot Password - WOOD CONNECT";
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    $email = sanitizeInput($_POST['email']);
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id, business_name FROM marketers WHERE email = ? AND is_active = TRUE");
        $stmt->execute([$email]);
        $marketer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($marketer) {
            // Generate reset token (in real app, you'd email this)
            $reset_token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("UPDATE marketers SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$reset_token, $expires, $marketer['id']]);
            
            $success_message = "Password reset instructions have been sent to your email.";
        } else {
            $error_message = "No account found with that email address.";
        }
    } catch (Exception $e) {
        $error_message = "Error processing your request: " . $e->getMessage();
    }
}
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
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h4><i class="fas fa-key me-2"></i>Reset Your Password</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary">Back to Login</a>
                            </div>
                        <?php else: ?>
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>

                            <p class="text-muted mb-4">
                                Enter your email address and we'll send you instructions to reset your password.
                            </p>

                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required 
                                           value="<?php echo $_POST['email'] ?? ''; ?>">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Send Reset Instructions</button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <p class="mb-0">
                                    Remember your password? 
                                    <a href="login.php">Back to Login</a>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>