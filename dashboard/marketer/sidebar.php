<?php
// Marketer dashboard sidebar
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="card">
    <div class="card-body">
        <div class="text-center mb-4">
            <div class="avatar-placeholder bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                 style="width: 80px; height: 80px; font-size: 2rem;">
                <i class="fas fa-store"></i>
            </div>
            <h5 class="mt-3 mb-1"><?php echo $_SESSION['marketer_business_name']; ?></h5>
            <span class="badge bg-<?php echo $_SESSION['marketer_verified'] ? 'success' : 'warning'; ?>">
                <?php echo $_SESSION['marketer_verified'] ? 'Verified' : 'Pending Verification'; ?>
            </span>
        </div>

        <div class="list-group list-group-flush">
            <a href="index.php" class="list-group-item list-group-item-action <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="inventory.php" class="list-group-item list-group-item-action <?php echo $current_page === 'inventory.php' ? 'active' : ''; ?>">
                <i class="fas fa-boxes me-2"></i>Inventory Management
            </a>
            <a href="inquiries.php" class="list-group-item list-group-item-action <?php echo $current_page === 'inquiries.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope me-2"></i>Customer Inquiries
                <?php
                $pending_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM inquiries WHERE marketer_id = ? AND status = 'pending'");
                $pending_count_stmt->execute([$_SESSION['marketer_id']]);
                $pending_count = $pending_count_stmt->fetchColumn();
                if ($pending_count > 0): ?>
                    <span class="badge bg-warning float-end"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="profile.php" class="list-group-item list-group-item-action <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user me-2"></i>Business Profile
            </a>
            <a href="analytics.php" class="list-group-item list-group-item-action <?php echo $current_page === 'analytics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar me-2"></i>Analytics
            </a>
            <a href="settings.php" class="list-group-item list-group-item-action <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog me-2"></i>Settings
            </a>
        </div>
    </div>
</div>

<style>
.avatar-placeholder {
    background: linear-gradient(135deg, var(--primary-green), var(--forest-dark));
}
.list-group-item.active {
    background-color: var(--primary-green);
    border-color: var(--primary-green);
}
</style>