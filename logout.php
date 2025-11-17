<?php
require_once 'includes/config.php';

// Destroy all session data
session_destroy();

// Redirect to homepage with success message
$_SESSION['success_message'] = "You have been logged out successfully.";
header('Location: ' . url('/'));
exit;
?>