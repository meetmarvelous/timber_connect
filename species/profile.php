<?php
require_once '../includes/config.php';
$page_title = "Timber Species Profile - WOOD CONNECT";

$species_id = $_GET['id'] ?? 0;

// Get species details
try {
  $species_stmt = $pdo->prepare("
        SELECT s.*, COUNT(i.id) as marketer_count
        FROM species s
        LEFT JOIN inventory i ON s.id = i.species_id AND i.is_available = TRUE
        WHERE s.id = ?
        GROUP BY s.id
    ");
  $species_stmt->execute([$species_id]);
  $species = $species_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $species = null;
  error_log("Species profile query error: " . $e->getMessage());
}

if (!$species) {
  header('Location: ' . url('species/directory.php'));
  exit;
}

$common_names = json_decode($species['common_names'], true);
$common_uses = json_decode($species['common_uses'], true);
$primary_name = $common_names[0] ?? $species['scientific_name'];

// Get marketers offering this species
try {
  $marketers_stmt = $pdo->prepare("
        SELECT DISTINCT m.*, COUNT(i.id) as listing_count,
               MIN(i.price_per_unit) as min_price,
               MAX(i.price_per_unit) as max_price
        FROM marketers m
        JOIN inventory i ON m.id = i.marketer_id
        WHERE i.species_id = ? AND i.is_available = TRUE AND m.verification_status = 'verified'
        GROUP BY m.id
        ORDER BY listing_count DESC
        LIMIT 10
    ");
  $marketers_stmt->execute([$species_id]);
  $marketers = $marketers_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $marketers = [];
  error_log("Marketers query error: " . $e->getMessage());
}

// Get available dimensions for this species
try {
  $dimensions_stmt = $pdo->prepare("
        SELECT DISTINCT dimensions, 
               COUNT(*) as availability_count,
               MIN(price_per_unit) as min_price,
               MAX(price_per_unit) as max_price
        FROM inventory 
        WHERE species_id = ? AND is_available = TRUE
        GROUP BY dimensions
        ORDER BY availability_count DESC
    ");
  $dimensions_stmt->execute([$species_id]);
  $available_dimensions = $dimensions_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $available_dimensions = [];
  error_log("Dimensions query error: " . $e->getMessage());
}
$page_title = $primary_name . " - Timber Species";
include '../includes/header.php';
?>

<div class="container py-5">
  <!-- Species Header -->
  <div class="row mb-5">
    <div class="col-lg-8 mx-auto">
      <div class="text-center mb-4">
        <h1 class="display-4 mb-3 text-success"><?php echo $primary_name; ?></h1>
        <p class="lead text-muted scientific-name">
          <em><?php echo $species['scientific_name']; ?></em>
        </p>
        <?php if (count($common_names) > 1): ?>
          <p class="text-muted">
            Also known as: <?php echo implode(', ', array_slice($common_names, 1)); ?>
          </p>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="row mt-4">
          <div class="col-md-3">
            <div class="stat-card">
              <h4 class="text-success mb-1"><?php echo $species['marketer_count']; ?></h4>
              <small class="text-muted">Suppliers</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card">
              <h4 class="text-success mb-1"><?php echo count($available_dimensions); ?></h4>
              <small class="text-muted">Available Sizes</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card">
              <h4 class="text-success mb-1"><?php echo str_repeat('★', $species['timber_value_rank']); ?></h4>
              <small class="text-muted">Value Rating</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card">
              <h4 class="text-success mb-1"><?php echo $species['durability']; ?></h4>
              <small class="text-muted">Durability</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
      <!-- Specifications -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Specifications</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-borderless">
                <?php if ($species['family']): ?>
                  <tr>
                    <th width="40%" class="text-muted">Family:</th>
                    <td><?php echo $species['family']; ?></td>
                  </tr>
                <?php endif; ?>
                <?php if ($species['density_range']): ?>
                  <tr>
                    <th class="text-muted">Density:</th>
                    <td><?php echo $species['density_range']; ?></td>
                  </tr>
                <?php endif; ?>
                <?php if ($species['durability']): ?>
                  <tr>
                    <th class="text-muted">Durability:</th>
                    <td>
                      <span class="badge bg-<?php
                                            echo $species['durability'] === 'Very Durable' ? 'success' : ($species['durability'] === 'Durable' ? 'primary' : 'warning');
                                            ?>">
                        <?php echo $species['durability']; ?>
                      </span>
                    </td>
                  </tr>
                <?php endif; ?>
                <tr>
                  <th class="text-muted">Value Rank:</th>
                  <td>
                    <?php echo str_repeat('★', $species['timber_value_rank']); ?>
                    <small class="text-muted ms-2">(<?php echo $species['timber_value_rank']; ?> out of 5)</small>
                  </td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <?php if ($common_uses && !empty($common_uses)): ?>
                <h6 class="text-muted mb-3">Common Uses:</h6>
                <div class="d-flex flex-wrap gap-2">
                  <?php foreach ($common_uses as $use): ?>
                    <span class="badge bg-light text-dark border"><?php echo $use; ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($species['description']): ?>
            <div class="mt-4">
              <h6 class="text-muted mb-3">Description:</h6>
              <p class="mb-0"><?php echo nl2br($species['description']); ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Available Dimensions -->
      <?php if (!empty($available_dimensions)): ?>
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-ruler-combined me-2"></i>Available Dimensions & Prices</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Dimensions</th>
                    <th>Price Range</th>
                    <th>Availability</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($available_dimensions as $dim): ?>
                    <tr>
                      <td>
                        <strong><?php echo $dim['dimensions']; ?></strong>
                      </td>
                      <td>
                        <?php if ($dim['min_price'] == $dim['max_price']): ?>
                          <span class="text-success fw-bold"><?php echo formatNaira($dim['min_price']); ?></span>
                        <?php else: ?>
                          <span class="text-success fw-bold"><?php echo formatNaira($dim['min_price']); ?> - <?php echo formatNaira($dim['max_price']); ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge bg-success"><?php echo $dim['availability_count']; ?> suppliers</span>
                      </td>
                      <td>
                        <a href="<?php echo url('marketplace/search.php?species=' . urlencode($primary_name) . '&dimensions=' . urlencode($dim['dimensions'])); ?>"
                          class="btn btn-success btn-sm">
                          <i class="fas fa-search me-1"></i>View Suppliers
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Quick Actions -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="<?php echo url('marketplace/search.php?species=' . urlencode($primary_name)); ?>" class="btn btn-success">
              <i class="fas fa-shopping-cart me-2"></i>Browse All Suppliers
            </a>
            <a href="<?php echo url('species/directory.php'); ?>" class="btn btn-outline-success">
              <i class="fas fa-arrow-left me-2"></i>Back to Species Directory
            </a>
          </div>
        </div>
      </div>

      <!-- Top Suppliers -->
      <?php if (!empty($marketers)): ?>
        <div class="card shadow-sm">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-store me-2"></i>Top Suppliers</h5>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              <?php foreach ($marketers as $marketer): ?>
                <div class="list-group-item px-0">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h6 class="mb-1"><?php echo htmlspecialchars($marketer['business_name']); ?></h6>
                      <p class="mb-1 small text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?php echo $marketer['city'] . ', ' . $marketer['state']; ?>
                      </p>
                      <small class="text-muted">
                        <?php echo $marketer['listing_count']; ?> listing(s)
                      </small>
                    </div>
                    <span class="badge bg-success">Verified</span>
                  </div>
                  <div class="mt-2">
                    <a href="<?php echo url('marketplace/profile.php?marketer_id=' . $marketer['id']); ?>" class="btn btn-outline-success btn-sm w-100">
                      View Profile
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <?php if (count($marketers) >= 10): ?>
              <div class="text-center mt-3">
                <a href="<?php echo url('marketplace/search.php?species=' . urlencode($primary_name)); ?>" class="btn btn-outline-primary btn-sm">
                  View All Suppliers
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <div class="card shadow-sm">
          <div class="card-body text-center py-4">
            <i class="fas fa-store fa-2x text-muted mb-3"></i>
            <h6>No Suppliers Available</h6>
            <p class="text-muted small mb-0">No verified suppliers are currently offering this timber species.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
  .stat-card {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
  }

  .scientific-name {
    font-size: 1.1rem;
  }

  .table-borderless th {
    font-weight: 600;
  }
</style>
</body>

</html>