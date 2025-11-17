<?php
require_once '../../includes/config.php';
$auth->requireMarketer();

$page_title = "Business Analytics - WOOD CONNECT";
$marketer_id = $_SESSION['marketer_id'];
$load_charts = true;

// Get analytics data
try {
    // Total statistics
    $stats_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_listings,
            SUM(CASE WHEN is_available = TRUE THEN 1 ELSE 0 END) as active_listings,
            SUM(quantity_available) as total_quantity,
            AVG(price_per_unit) as avg_price,
            (SELECT COUNT(*) FROM inquiries WHERE marketer_id = ? AND status = 'pending') as pending_inquiries,
            (SELECT COUNT(*) FROM inquiries WHERE marketer_id = ? AND status = 'completed') as completed_inquiries,
            (SELECT COUNT(*) FROM inquiries WHERE marketer_id = ? AND status = 'contacted') as contacted_inquiries
        FROM inventory 
        WHERE marketer_id = ?
    ");
    $stats_stmt->execute([$marketer_id, $marketer_id, $marketer_id, $marketer_id]);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    // Monthly inquiry trends (last 6 months)
    $trends_stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as inquiry_count,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
        FROM inquiries 
        WHERE marketer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $trends_stmt->execute([$marketer_id]);
    $monthly_trends = $trends_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top performing species
    $species_stmt = $pdo->prepare("
        SELECT 
            s.scientific_name,
            s.common_names,
            COUNT(i.id) as inquiry_count,
            SUM(CASE WHEN i.status = 'completed' THEN 1 ELSE 0 END) as completed_count
        FROM inquiries i
        JOIN species s ON i.species_id = s.id
        WHERE i.marketer_id = ?
        GROUP BY s.id
        ORDER BY inquiry_count DESC
        LIMIT 5
    ");
    $species_stmt->execute([$marketer_id]);
    $top_species = $species_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inventory value by species
    $inventory_value_stmt = $pdo->prepare("
        SELECT 
            s.scientific_name,
            s.common_names,
            COUNT(inv.id) as listing_count,
            SUM(inv.quantity_available) as total_quantity,
            SUM(inv.quantity_available * inv.price_per_unit) as total_value
        FROM inventory inv
        JOIN species s ON inv.species_id = s.id
        WHERE inv.marketer_id = ? AND inv.is_available = TRUE
        GROUP BY s.id
        ORDER BY total_value DESC
    ");
    $inventory_value_stmt->execute([$marketer_id]);
    $inventory_value = $inventory_value_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Analytics query error: " . $e->getMessage());
    $stats = [];
    $monthly_trends = [];
    $top_species = [];
    $inventory_value = [];
}
?>
    <?php 
    $page_title = "Business Analytics - WOOD CONNECT";
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
                    <h2>Business Analytics</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-success active" data-period="month">This Month</button>
                        <button type="button" class="btn btn-outline-success" data-period="quarter">This Quarter</button>
                        <button type="button" class="btn btn-outline-success" data-period="year">This Year</button>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="row g-4 mb-5">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0"><?php echo $stats['active_listings'] ?? 0; ?></h4>
                                        <p class="mb-0">Active Listings</p>
                                    </div>
                                    <i class="fas fa-boxes fa-2x opacity-50"></i>
                                </div>
                                <small class="opacity-75">Total: <?php echo $stats['total_listings'] ?? 0; ?> listings</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0"><?php echo $stats['total_quantity'] ?? 0; ?></h4>
                                        <p class="mb-0">Total Stock</p>
                                    </div>
                                    <i class="fas fa-cubes fa-2x opacity-50"></i>
                                </div>
                                <small class="opacity-75">Available units</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0"><?php echo $stats['pending_inquiries'] ?? 0; ?></h4>
                                        <p class="mb-0">Pending Inquiries</p>
                                    </div>
                                    <i class="fas fa-envelope fa-2x opacity-50"></i>
                                </div>
                                <small class="opacity-75">
                                    <?php echo $stats['contacted_inquiries'] ?? 0; ?> contacted
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0"><?php echo $stats['completed_inquiries'] ?? 0; ?></h4>
                                        <p class="mb-0">Completed Sales</p>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                </div>
                                <small class="opacity-75">Successful transactions</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Monthly Trends Chart -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Inquiry Trends (Last 6 Months)</h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="trendToggle" checked>
                                    <label class="form-check-label" for="trendToggle">Show Completed</label>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="trendsChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performing Species -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Top Performing Species</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($top_species)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-chart-pie fa-2x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No inquiry data yet</p>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($top_species as $species): 
                                            $common_names = json_decode($species['common_names'], true);
                                            $species_name = $common_names[0] ?? $species['scientific_name'];
                                            $completion_rate = $species['inquiry_count'] > 0 ? 
                                                round(($species['completed_count'] / $species['inquiry_count']) * 100) : 0;
                                        ?>
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0"><?php echo $species_name; ?></h6>
                                                    <span class="badge bg-primary"><?php echo $species['inquiry_count']; ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted"><?php echo $species['completed_count']; ?> completed</small>
                                                    <small class="text-success"><?php echo $completion_rate; ?>% rate</small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Value -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Inventory Value by Species</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($inventory_value)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-boxes fa-2x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No inventory data available</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Timber Species</th>
                                                    <th>Listings</th>
                                                    <th>Quantity</th>
                                                    <th>Total Value</th>
                                                    <th>Avg Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $total_inventory_value = 0;
                                                foreach ($inventory_value as $item): 
                                                    $common_names = json_decode($item['common_names'], true);
                                                    $species_name = $common_names[0] ?? $item['scientific_name'];
                                                    $avg_price = $item['listing_count'] > 0 ? 
                                                        $item['total_value'] / $item['total_quantity'] : 0;
                                                    $total_inventory_value += $item['total_value'];
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $species_name; ?></strong>
                                                            <br><small class="text-muted"><?php echo $item['scientific_name']; ?></small>
                                                        </td>
                                                        <td><?php echo $item['listing_count']; ?></td>
                                                        <td><?php echo number_format($item['total_quantity']); ?></td>
                                                        <td class="fw-bold text-success"><?php echo formatNaira($item['total_value']); ?></td>
                                                        <td><?php echo formatNaira($avg_price); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <td colspan="3" class="text-end fw-bold">Total Inventory Value:</td>
                                                    <td colspan="2" class="fw-bold text-success"><?php echo formatNaira($total_inventory_value); ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Insights -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Quick Insights</h5>
                            </div>
                            <div class="card-body">
                                <div class="insights-list">
                                    <?php if ($stats['pending_inquiries'] > 0): ?>
                                        <div class="alert alert-warning d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                                            <div>
                                                <strong><?php echo $stats['pending_inquiries']; ?> pending inquiries</strong>
                                                <br><small>Respond to customers quickly to increase sales</small>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (($stats['total_quantity'] ?? 0) < 100): ?>
                                        <div class="alert alert-info d-flex align-items-center">
                                            <i class="fas fa-box me-3 fa-lg"></i>
                                            <div>
                                                <strong>Low inventory alert</strong>
                                                <br><small>Consider restocking popular species</small>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (($stats['avg_price'] ?? 0) > 0): ?>
                                        <div class="alert alert-success d-flex align-items-center">
                                            <i class="fas fa-chart-line me-3 fa-lg"></i>
                                            <div>
                                                <strong>Average price: <?php echo formatNaira($stats['avg_price']); ?></strong>
                                                <br><small>Your pricing is competitive in the market</small>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (empty($top_species)): ?>
                                        <div class="alert alert-secondary d-flex align-items-center">
                                            <i class="fas fa-info-circle me-3 fa-lg"></i>
                                            <div>
                                                <strong>No sales data yet</strong>
                                                <br><small>Your analytics will populate as you receive inquiries</small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Performance Metrics</h5>
                            </div>
                            <div class="card-body">
                                <div class="metrics-grid">
                                    <div class="metric-item text-center p-3 border rounded">
                                        <div class="metric-value h4 text-primary mb-1">
                                            <?php echo $stats['active_listings'] ?? 0; ?>
                                        </div>
                                        <div class="metric-label small text-muted">Active Listings</div>
                                    </div>
                                    <div class="metric-item text-center p-3 border rounded">
                                        <div class="metric-value h4 text-success mb-1">
                                            <?php echo $stats['completed_inquiries'] ?? 0; ?>
                                        </div>
                                        <div class="metric-label small text-muted">Completed Sales</div>
                                    </div>
                                    <div class="metric-item text-center p-3 border rounded">
                                        <div class="metric-value h4 text-info mb-1">
                                            <?php echo $stats['total_quantity'] ?? 0; ?>
                                        </div>
                                        <div class="metric-label small text-muted">Total Stock</div>
                                    </div>
                                    <div class="metric-item text-center p-3 border rounded">
                                        <div class="metric-value h4 text-warning mb-1">
                                            <?php 
                                            $total_inquiries = ($stats['pending_inquiries'] ?? 0) + 
                                                             ($stats['contacted_inquiries'] ?? 0) + 
                                                             ($stats['completed_inquiries'] ?? 0);
                                            echo $total_inquiries; 
                                            ?>
                                        </div>
                                        <div class="metric-label small text-muted">Total Inquiries</div>
                                    </div>
                                </div>
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
        // Monthly trends chart
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        
        // Prepare chart data from PHP
        const months = <?php echo json_encode(array_column($monthly_trends, 'month')); ?>;
        const inquiryCounts = <?php echo json_encode(array_column($monthly_trends, 'inquiry_count')); ?>;
        const completedCounts = <?php echo json_encode(array_column($monthly_trends, 'completed_count')); ?>;
        
        const trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Total Inquiries',
                    data: inquiryCounts,
                    borderColor: '#228B22',
                    backgroundColor: 'rgba(34, 139, 34, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Completed Inquiries',
                    data: completedCounts,
                    borderColor: '#8B4513',
                    backgroundColor: 'rgba(139, 69, 19, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Period filter buttons
        const periodButtons = document.querySelectorAll('[data-period]');
        periodButtons.forEach(button => {
            button.addEventListener('click', function() {
                periodButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                // Here you would typically reload data for the selected period
                console.log('Period changed to:', this.dataset.period);
            });
        });

        // Trend toggle
        const trendToggle = document.getElementById('trendToggle');
        trendToggle.addEventListener('change', function() {
            const completedDataset = trendsChart.data.datasets[1];
            completedDataset.hidden = !this.checked;
            trendsChart.update();
        });

        // Metrics grid layout
        const metricsGrid = document.querySelector('.metrics-grid');
        if (metricsGrid) {
            metricsGrid.style.display = 'grid';
            metricsGrid.style.gridTemplateColumns = 'repeat(2, 1fr)';
            metricsGrid.style.gap = '1rem';
        }
    });
    </script>

    <style>
    .stat-card {
        transition: transform 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .insights-list .alert {
        border-left: 4px solid;
        margin-bottom: 1rem;
    }
    .metric-item {
        transition: all 0.2s ease;
    }
    .metric-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    </style>
</body>
</html>