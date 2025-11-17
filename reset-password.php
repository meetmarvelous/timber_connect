<?php
require_once 'includes/config.php';

if ($auth->isLoggedIn()) {
    header('Location: /dashboard/');
    exit;
}

$page_title = "Reset Password - WOOD CONNECT";
$success_message = '';
$error_message = '';

// Validate reset token
$token = $_GET['token'] ?? '';
if (empty($token)) {
    header('Location: forgot-password.php');
    exit;
}

// Check if token is valid
$stmt = $pdo->prepare("SELECT id FROM marketers WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$marketer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$marketer) {
    $error_message = "Invalid or expired reset token. Please request a new password reset.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token']) && $marketer) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE marketers SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->execute([$password_hash, $marketer['id']]);
            
            $success_message = "Password reset successfully! You can now login with your new password.";
        } catch (Exception $e) {
            $error_message = "Error resetting password: " . $e->getMessage();
        }
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
                        <h4><i class="fas fa-key me-2"></i>Set New Password</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error_message && !$marketer): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <div class="text-center">
                                <a href="forgot-password.php" class="btn btn-primary">Request New Reset Link</a>
                            </div>
                        <?php elseif ($success_message): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary">Proceed to Login</a>
                            </div>
                        <?php else: ?>
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">Password must be at least 6 characters long.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <p class="mb-0">
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