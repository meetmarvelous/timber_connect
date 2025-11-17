<?php
require_once '../includes/config.php';
$auth->requireAdmin();

$page_title = "Admin Dashboard - WOOD CONNECT";
$load_charts = true;

// Get platform statistics
$stats_stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM marketers) as total_marketers,
        (SELECT COUNT(*) FROM marketers WHERE verification_status = 'verified') as verified_marketers,
        (SELECT COUNT(*) FROM marketers WHERE verification_status = 'pending') as pending_marketers,
        (SELECT COUNT(*) FROM species) as total_species,
        (SELECT COUNT(*) FROM inventory WHERE is_available = TRUE) as active_listings,
        (SELECT COUNT(*) FROM inquiries WHERE status = 'pending') as pending_inquiries,
        (SELECT COUNT(*) FROM inquiries WHERE created_at >= CURDATE() - INTERVAL 7 DAY) as weekly_inquiries
");
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get recent activities
$activities_stmt = $pdo->query("
    (SELECT 'marketer_registered' as type, business_name as title, registration_date as date FROM marketers ORDER BY registration_date DESC LIMIT 5)
    UNION ALL
    (SELECT 'inquiry_received' as type, CONCAT(buyer_name, ' - ', quantity, ' units') as title, created_at as date FROM inquiries ORDER BY created_at DESC LIMIT 5)
    ORDER BY date DESC LIMIT 10
");
$recent_activities = $activities_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get pending marketer verifications
$pending_stmt = $pdo->prepare("SELECT * FROM marketers WHERE verification_status = 'pending' ORDER BY registration_date DESC LIMIT 5");
$pending_stmt->execute();
$pending_marketers = $pending_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <?php 
    include '../includes/header.php'; 
    ?>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="fas fa-cog me-2"></i>Admin Panel
                        </h5>
                        <div class="list-group list-group-flush">
                            <a href="<?php echo url('dashboard/'); ?>" class="list-group-item list-group-item-action active">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                            <a href="<?php echo url('dashboard/marketers.php'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-users me-2"></i>Manage Marketers
                                <?php if ($stats['pending_marketers'] > 0): ?>
                                    <span class="badge bg-warning float-end"><?php echo $stats['pending_marketers']; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="<?php echo url('dashboard/species.php'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-tree me-2"></i>Timber Species
                            </a>
                            <a href="<?php echo url('dashboard/inquiries.php'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-envelope me-2"></i>Customer Inquiries
                                <?php if ($stats['pending_inquiries'] > 0): ?>
                                    <span class="badge bg-warning float-end"><?php echo $stats['pending_inquiries']; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="<?php echo url('dashboard/reports.php'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                            </a>
                            <a href="<?php echo url('dashboard/settings.php'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-sliders-h me-2"></i>System Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-success">Admin Dashboard</h2>
                    <div class="text-muted">
                        <i class="fas fa-user-shield me-1"></i>
                        Welcome, <?php echo $_SESSION['admin_username']; ?>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="row g-4 mb-5">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0"><?php echo $stats['total_marketers']; ?></h4>
                                        <p class="mb-0">Total Marketers</p>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <small>
                                        <?php echo $stats['verified_marketers']; ?> verified • 
                                        <?php echo $stats['pending_marketers']; ?> pending
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0"><?php echo $stats['active_listings']; ?></h4>
                                        <p class="mb-0">Active Listings</p>
                                    </div>
                                    <i class="fas fa-boxes fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0"><?php echo $stats['pending_inquiries']; ?></h4>
                                        <p class="mb-0">Pending Inquiries</p>
                                    </div>
                                    <i class="fas fa-envelope fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0"><?php echo $stats['weekly_inquiries']; ?></h4>
                                        <p class="mb-0">This Week</p>
                                    </div>
                                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Pending Verifications -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-clock me-2"></i>Pending Verifications
                                </h5>
                                <a href="<?php echo url('dashboard/marketers.php?filter=pending'); ?>" class="btn btn-sm btn-outline-dark">
                                    View All
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($pending_marketers)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                                        <p class="text-muted mb-0">No pending verifications</p>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($pending_marketers as $marketer): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($marketer['business_name']); ?></h6>
                                                        <p class="mb-1 small text-muted">
                                                            <?php echo $marketer['owner_name']; ?> • 
                                                            <?php echo $marketer['city']; ?>, <?php echo $marketer['state']; ?>
                                                        </p>
                                                        <small class="text-muted">
                                                            Registered: <?php echo date('M j, Y', strtotime($marketer['registration_date'])); ?>
                                                        </small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo url('dashboard/marketer-verify.php?id=' . $marketer['id'] . '&action=verify'); ?>" 
                                                           class="btn btn-success" title="Verify">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="<?php echo url('dashboard/marketer-verify.php?id=' . $marketer['id'] . '&action=reject'); ?>" 
                                                           class="btn btn-danger" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>Recent Activities
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-<?php 
                                                        echo $activity['type'] === 'marketer_registered' ? 'user-plus' : 'envelope';
                                                    ?> text-<?php 
                                                        echo $activity['type'] === 'marketer_registered' ? 'primary' : 'success';
                                                    ?>"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></p>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, g:i A', strtotime($activity['date'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Marketers by State
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="stateChart" width="400" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Weekly Activity
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="activityChart" width="400" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // State distribution chart
        const stateCtx = document.getElementById('stateChart').getContext('2d');
        const stateChart = new Chart(stateCtx, {
            type: 'doughnut',
            data: {
                labels: ['Ondo', 'Ekiti', 'Osun', 'Oyo', 'Lagos', 'Ogun'],
                datasets: [{
                    data: [5, 3, 6, 4, 3, 0], // Sample data - you would get this from your database
                    backgroundColor: [
                        '#228B22', '#8B4513', '#D2691E', '#64748b', '#94a3b8', '#cbd5e1'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Activity chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'New Marketers',
                    data: [2, 3, 1, 4, 2, 1, 0],
                    backgroundColor: '#228B22'
                }, {
                    label: 'New Inquiries',
                    data: [8, 12, 9, 15, 11, 7, 3],
                    backgroundColor: '#8B4513'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>
</body>
</html>