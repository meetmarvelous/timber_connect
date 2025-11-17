<?php
require_once '../../includes/config.php';
$auth->requireMarketer();

$page_title = "Marketer Dashboard - WOOD CONNECT";
$load_charts = true;

// Get marketer statistics
$marketer_id = $_SESSION['marketer_id'];
$stats_stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_listings,
        SUM(CASE WHEN is_available = TRUE THEN 1 ELSE 0 END) as active_listings,
        SUM(quantity_available) as total_quantity,
        (SELECT COUNT(*) FROM inquiries WHERE marketer_id = ? AND status = 'pending') as pending_inquiries,
        (SELECT COUNT(*) FROM inquiries WHERE marketer_id = ? AND status = 'completed') as completed_inquiries
    FROM inventory 
    WHERE marketer_id = ?
");
$stats_stmt->execute([$marketer_id, $marketer_id, $marketer_id]);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get recent inquiries
$inquiries_stmt = $pdo->prepare("
    SELECT i.*, s.common_names, s.scientific_name
    FROM inquiries i
    JOIN species s ON i.species_id = s.id
    WHERE i.marketer_id = ?
    ORDER BY i.created_at DESC
    LIMIT 5
");
$inquiries_stmt->execute([$marketer_id]);
$recent_inquiries = $inquiries_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get low stock items
$low_stock_stmt = $pdo->prepare("
    SELECT * FROM inventory 
    WHERE marketer_id = ? AND is_available = TRUE AND quantity_available < 10
    ORDER BY quantity_available ASC
    LIMIT 5
");
$low_stock_stmt->execute([$marketer_id]);
$low_stock_items = $low_stock_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php 
include '../../includes/header.php'; 
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Business Dashboard</h2>
                <div class="d-flex gap-2">
                    <a href="inventory.php?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New Listing
                    </a>
                </div>
            </div>

            <!-- Verification Status Alert -->
            <?php if ($_SESSION['marketer_verified']): ?>
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-1">Your business is verified!</h5>
                        <p class="mb-0">Your listings are visible to buyers across the platform.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-clock fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-1">Verification Pending</h5>
                        <p class="mb-0">Your account is under review. You can add inventory, but it won't be visible to buyers until verified.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card stat-card bg-primary text-white">
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
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0"><?php echo $stats['total_quantity']; ?></h4>
                                    <p class="mb-0">Total Stock</p>
                                </div>
                                <i class="fas fa-cubes fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-warning text-dark">
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
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0"><?php echo $stats['completed_inquiries']; ?></h4>
                                    <p class="mb-0">Completed Sales</p>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Recent Inquiries -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Inquiries</h5>
                            <a href="inquiries.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recent_inquiries)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-envelope-open-text fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No inquiries yet</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recent_inquiries as $inquiry):
                                        $common_names = json_decode($inquiry['common_names'], true);
                                        $species_name = $common_names[0] ?? $inquiry['scientific_name'];
                                    ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($inquiry['buyer_name']); ?></h6>
                                                    <p class="mb-1 small text-muted">
                                                        <?php echo $species_name; ?> • <?php echo $inquiry['dimensions']; ?> • Qty: <?php echo $inquiry['quantity']; ?>
                                                    </p>
                                                    <span class="badge bg-<?php
                                                                            echo $inquiry['status'] === 'pending' ? 'warning' : ($inquiry['status'] === 'completed' ? 'success' : 'secondary');
                                                                            ?>">
                                                        <?php echo ucfirst($inquiry['status']); ?>
                                                    </span>
                                                </div>
                                                <small class="text-muted"><?php echo date('M j', strtotime($inquiry['created_at'])); ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Low Stock Alert</h5>
                            <a href="inventory.php" class="btn btn-sm btn-outline-primary">Manage</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($low_stock_items)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                                    <p class="text-muted mb-0">All items are well stocked</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($low_stock_items as $item): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><?php
                                                                        $species_stmt = $pdo->prepare("SELECT common_names FROM species WHERE id = ?");
                                                                        $species_stmt->execute([$item['species_id']]);
                                                                        $species = $species_stmt->fetch(PDO::FETCH_ASSOC);
                                                                        $common_names = json_decode($species['common_names'], true);
                                                                        echo $common_names[0] ?? 'Unknown Species';
                                                                        ?></h6>
                                                    <p class="mb-0 small text-muted">
                                                        <?php echo $item['dimensions']; ?> • ₦<?php echo number_format($item['price_per_unit'], 2); ?>
                                                    </p>
                                                </div>
                                                <span class="badge bg-danger"><?php echo $item['quantity_available']; ?> left</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Monthly Sales Overview</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" width="400" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize sales chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Inquiries Received',
                    data: [12, 19, 15, 25, 22, 30],
                    borderColor: '#228B22',
                    backgroundColor: 'rgba(34, 139, 34, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<style>
    .stat-card {
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }
</style>
</body>

</html>