<?php
require_once '../includes/config.php';
$auth->requireAdmin();

$page_title = "Reports & Analytics - WOOD CONNECT";
$load_charts = true;

// Get statistics
$stats_stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM marketers WHERE verification_status = 'verified') as verified_marketers,
        (SELECT COUNT(*) FROM marketers WHERE verification_status = 'pending') as pending_marketers,
        (SELECT COUNT(*) FROM species) as total_species,
        (SELECT COUNT(*) FROM inventory WHERE is_available = TRUE) as active_listings,
        (SELECT COUNT(*) FROM inquiries WHERE status = 'pending') as pending_inquiries,
        (SELECT COUNT(*) FROM inquiries WHERE status = 'completed') as completed_inquiries,
        (SELECT COUNT(*) FROM inquiries WHERE created_at >= CURDATE() - INTERVAL 7 DAY) as weekly_inquiries,
        (SELECT COUNT(*) FROM inquiries WHERE created_at >= CURDATE() - INTERVAL 30 DAY) as monthly_inquiries
");
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get inquiries by status for chart
$inquiry_stats = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM inquiries 
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

// Get marketers by state
$state_stats = $pdo->query("
    SELECT state, COUNT(*) as count 
    FROM marketers 
    WHERE verification_status = 'verified'
    GROUP BY state
")->fetchAll(PDO::FETCH_ASSOC);

// Get popular species
$species_stats = $pdo->query("
    SELECT s.scientific_name, s.common_names, COUNT(i.id) as listing_count
    FROM species s
    LEFT JOIN inventory i ON s.id = i.species_id AND i.is_available = TRUE
    GROUP BY s.id
    ORDER BY listing_count DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid py-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Admin Navigation</h5>
          <div class="list-group list-group-flush">
            <a href="<?php echo url('dashboard/'); ?>" class="list-group-item list-group-item-action">
              <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="<?php echo url('dashboard/marketers.php'); ?>" class="list-group-item list-group-item-action">
              <i class="fas fa-users me-2"></i>Manage Marketers
            </a>
            <a href="<?php echo url('dashboard/species.php'); ?>" class="list-group-item list-group-item-action">
              <i class="fas fa-tree me-2"></i>Timber Species
            </a>
            <a href="<?php echo url('dashboard/inquiries.php'); ?>" class="list-group-item list-group-item-action">
              <i class="fas fa-envelope me-2"></i>Customer Inquiries
            </a>
            <a href="<?php echo url('dashboard/reports.php'); ?>" class="list-group-item list-group-item-action active">
              <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
            </a>
            <a href="<?php echo url('dashboard/settings.php'); ?>" class="list-group-item list-group-item-action">
              <i class="fas fa-cog me-2"></i>System Settings
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
      <h2 class="mb-4">Reports & Analytics</h2>

      <!-- Quick Stats -->
      <div class="row g-4 mb-5">
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h4 class="mb-0"><?php echo $stats['verified_marketers']; ?></h4>
                  <p class="mb-0">Verified Marketers</p>
                </div>
                <i class="fas fa-users fa-2x opacity-50"></i>
              </div>
              <small class="opacity-75"><?php echo $stats['pending_marketers']; ?> pending</small>
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
              <small class="opacity-75"><?php echo $stats['total_species']; ?> species</small>
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
              <small class="opacity-75"><?php echo $stats['completed_inquiries']; ?> completed</small>
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
              <small class="opacity-75"><?php echo $stats['monthly_inquiries']; ?> this month</small>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Inquiries Chart -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="mb-0">Inquiries by Status</h5>
            </div>
            <div class="card-body">
              <canvas id="inquiriesChart" width="400" height="300"></canvas>
            </div>
          </div>
        </div>

        <!-- Marketers by State -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="mb-0">Marketers by State</h5>
            </div>
            <div class="card-body">
              <canvas id="stateChart" width="400" height="300"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Popular Species -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="mb-0">Most Popular Timber Species</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Species</th>
                      <th>Scientific Name</th>
                      <th>Active Listings</th>
                      <th>Market Share</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($species_stats as $species):
                      $common_names = json_decode($species['common_names'], true);
                      $primary_name = $common_names[0] ?? $species['scientific_name'];
                      $percentage = $stats['active_listings'] > 0 ? round(($species['listing_count'] / $stats['active_listings']) * 100, 1) : 0;
                    ?>
                      <tr>
                        <td><strong><?php echo $primary_name; ?></strong></td>
                        <td><em><?php echo $species['scientific_name']; ?></em></td>
                        <td>
                          <span class="badge bg-primary"><?php echo $species['listing_count']; ?></span>
                        </td>
                        <td>
                          <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar"
                              style="width: <?php echo $percentage; ?>%"
                              aria-valuenow="<?php echo $percentage; ?>"
                              aria-valuemin="0"
                              aria-valuemax="100">
                              <?php echo $percentage; ?>%
                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="mb-0">Recent Platform Activity</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h6>Recent Marketer Registrations</h6>
                  <?php
                  $recent_marketers = $pdo->query("
                                            SELECT business_name, city, state, registration_date 
                                            FROM marketers 
                                            ORDER BY registration_date DESC 
                                            LIMIT 5
                                        ")->fetchAll(PDO::FETCH_ASSOC);
                  ?>
                  <div class="list-group list-group-flush">
                    <?php foreach ($recent_marketers as $marketer): ?>
                      <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <strong><?php echo htmlspecialchars($marketer['business_name']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo $marketer['city']; ?>, <?php echo $marketer['state']; ?></small>
                          </div>
                          <small class="text-muted"><?php echo date('M j', strtotime($marketer['registration_date'])); ?></small>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <h6>Recent Inquiries</h6>
                  <?php
                  $recent_inquiries = $pdo->query("
                                            SELECT i.buyer_name, s.common_names, i.created_at
                                            FROM inquiries i
                                            JOIN species s ON i.species_id = s.id
                                            ORDER BY i.created_at DESC 
                                            LIMIT 5
                                        ")->fetchAll(PDO::FETCH_ASSOC);
                  ?>
                  <div class="list-group list-group-flush">
                    <?php foreach ($recent_inquiries as $inquiry):
                      $common_names = json_decode($inquiry['common_names'], true);
                      $species_name = $common_names[0] ?? 'Unknown';
                    ?>
                      <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <strong><?php echo htmlspecialchars($inquiry['buyer_name']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo $species_name; ?></small>
                          </div>
                          <small class="text-muted"><?php echo date('M j', strtotime($inquiry['created_at'])); ?></small>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
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
    // Inquiries Chart
    const inquiriesCtx = document.getElementById('inquiriesChart').getContext('2d');
    const inquiriesChart = new Chart(inquiriesCtx, {
      type: 'doughnut',
      data: {
        labels: [<?php echo implode(',', array_map(function ($stat) {
                    return "'" . ucfirst($stat['status']) . "'";
                  }, $inquiry_stats)); ?>],
        datasets: [{
          data: [<?php echo implode(',', array_map(function ($stat) {
                    return $stat['count'];
                  }, $inquiry_stats)); ?>],
          backgroundColor: [
            '#ffc107', // pending - yellow
            '#0dcaf0', // contacted - blue
            '#198754', // completed - green
            '#dc3545' // cancelled - red
          ]
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

    // State Distribution Chart
    const stateCtx = document.getElementById('stateChart').getContext('2d');
    const stateChart = new Chart(stateCtx, {
      type: 'bar',
      data: {
        labels: [<?php echo implode(',', array_map(function ($stat) {
                    return "'" . $stat['state'] . "'";
                  }, $state_stats)); ?>],
        datasets: [{
          label: 'Marketers',
          data: [<?php echo implode(',', array_map(function ($stat) {
                    return $stat['count'];
                  }, $state_stats)); ?>],
          backgroundColor: '#228B22'
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