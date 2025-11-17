<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Sanitize input
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'password' => $_POST['password'],
        ];

        // Register the buyer
        $buyer_id = $auth->registerBuyer($data);
        $_SESSION['buyer_id'] = $buyer_id;
        $_SESSION['buyer_name'] = $data['name'];
        $_SESSION['user_role'] = 'buyer';

        // Redirect to dashboard or login
        header('Location: /dashboard.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Registration</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Buyer Registration</h2>
        <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
        <form action="buyer-register.php" method="POST">
            <label for="name">Full Name</label>
            <input type="text" name="name" required>

            <label for="email">Email</label>
            <input type="email" name="email" required>

            <label for="phone">Phone Number</label>
            <input type="text" name="phone" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
