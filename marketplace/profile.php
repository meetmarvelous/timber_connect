<?php
require_once '../includes/config.php';
$page_title = "Marketer Profile - WOOD CONNECT";

$marketer_id = $_GET['marketer_id'] ?? 0;

// Get marketer details
$marketer_stmt = $pdo->prepare("
    SELECT m.*, 
           COUNT(i.id) as total_listings,
           COUNT(DISTINCT inv.id) as total_inquiries
    FROM marketers m
    LEFT JOIN inventory i ON m.id = i.marketer_id AND i.is_available = TRUE
    LEFT JOIN inquiries inv ON m.id = inv.marketer_id
    WHERE m.id = ? AND m.is_active = TRUE
    GROUP BY m.id
");
$marketer_stmt->execute([$marketer_id]);
$marketer = $marketer_stmt->fetch(PDO::FETCH_ASSOC);

if (!$marketer) {
  header('Location: ' . url('marketplace/'));
  exit;
}

// Get marketer's inventory
$inventory_stmt = $pdo->prepare("
    SELECT i.*, s.scientific_name, s.common_names
    FROM inventory i
    JOIN species s ON i.species_id = s.id
    WHERE i.marketer_id = ? AND i.is_available = TRUE
    ORDER BY i.updated_at DESC
");
$inventory_stmt->execute([$marketer_id]);
$inventory_items = $inventory_stmt->fetchAll(PDO::FETCH_ASSOC);
$page_title = $marketer['business_name'] . " - WOOD CONNECT";
include '../includes/header.php';
?>

<div class="container py-5">
  <!-- Marketer Header -->
  <div class="row mb-5">
    <div class="col-lg-8 mx-auto">
      <div class="card shadow-sm">
        <div class="card-body text-center p-5">
          <div class="marketer-avatar mb-4">
            <?php if ($marketer['profile_image']): ?>
              <img src="<?php echo upload('marketers/' . $marketer['profile_image']); ?>"
                alt="<?php echo htmlspecialchars($marketer['business_name']); ?>"
                class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
            <?php else: ?>
              <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                style="width: 120px; height: 120px; font-size: 3rem;">
                <i class="fas fa-store"></i>
              </div>
            <?php endif; ?>
          </div>

          <h1 class="display-5 mb-2"><?php echo htmlspecialchars($marketer['business_name']); ?></h1>

          <?php if ($marketer['verification_status'] === 'verified'): ?>
            <span class="badge bg-success mb-3">
              <i class="fas fa-check-circle me-1"></i>Verified Marketer
            </span>
          <?php endif; ?>

          <p class="lead text-muted mb-3">
            <i class="fas fa-map-marker-alt me-1"></i>
            <?php echo htmlspecialchars($marketer['city'] . ', ' . $marketer['state']); ?>
          </p>

          <?php if ($marketer['business_description']): ?>
            <p class="mb-4"><?php echo nl2br(htmlspecialchars($marketer['business_description'])); ?></p>
          <?php endif; ?>

          <div class="row mt-4">
            <div class="col-md-4">
              <div class="stat">
                <h4 class="text-primary mb-1"><?php echo $marketer['total_listings']; ?></h4>
                <small class="text-muted">Active Listings</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat">
                <h4 class="text-primary mb-1"><?php echo $marketer['total_inquiries']; ?></h4>
                <small class="text-muted">Total Inquiries</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat">
                <h4 class="text-primary mb-1">
                  <?php echo date('Y') - date('Y', strtotime($marketer['registration_date'])); ?>+
                </h4>
                <small class="text-muted">Years Experience</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Information -->
  <div class="row mb-5">
    <div class="col-lg-6 mx-auto">
      <div class="card">
        <div class="card-header bg-light">
          <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Contact Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Owner/Manager:</strong><br><?php echo htmlspecialchars($marketer['owner_name']); ?></p>
              <p><strong>Phone:</strong><br>
                <a href="tel:<?php echo htmlspecialchars($marketer['phone']); ?>">
                  <?php echo htmlspecialchars($marketer['phone']); ?>
                </a>
              </p>
            </div>
            <div class="col-md-6">
              <p><strong>Email:</strong><br>
                <?php if ($marketer['email']): ?>
                  <a href="mailto:<?php echo htmlspecialchars($marketer['email']); ?>">
                    <?php echo htmlspecialchars($marketer['email']); ?>
                  </a>
                <?php else: ?>
                  <span class="text-muted">Not provided</span>
                <?php endif; ?>
              </p>
              <p><strong>Location:</strong><br><?php echo htmlspecialchars($marketer['address']); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Available Inventory -->
  <div class="row">
    <div class="col-12">
      <h3 class="mb-4">Available Timber</h3>

      <?php if (empty($inventory_items)): ?>
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <h5>No Active Listings</h5>
            <p class="text-muted">This marketer doesn't have any active timber listings at the moment.</p>
            <a href="<?php echo url('marketplace/'); ?>" class="btn btn-success">Browse Other Marketers</a>
          </div>
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($inventory_items as $item):
            $common_names = json_decode($item['common_names'], true);
            $primary_name = $common_names[0] ?? $item['scientific_name'];
          ?>
            <div class="col-lg-4 col-md-6">
              <div class="card h-100 shadow-sm">
                <div class="card-body">
                  <h6 class="card-title text-success"><?php echo $primary_name; ?></h6>
                  <p class="card-text small text-muted">
                    <i class="fas fa-ruler me-1"></i><?php echo $item['dimensions']; ?> •
                    <i class="fas fa-tag me-1"></i><?php echo ucfirst($item['quality_grade']); ?>
                  </p>
                  <p class="card-text small"><?php echo $item['description'] ?: 'Quality timber available.'; ?></p>
                  <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="h6 text-success mb-0"><?php echo formatNaira($item['price_per_unit']); ?></span>
                    <span class="badge bg-light text-dark"><?php echo $item['quantity_available']; ?> available</span>
                  </div>
                </div>
                <div class="card-footer bg-transparent">
                  <a href="<?php echo url('marketplace/inquiry.php'); ?>?inventory_id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm w-100">
                    Inquire About This Timber
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Back to Marketplace -->
  <div class="row mt-5">
    <div class="col-12 text-center">
      <a href="<?php echo url('marketplace/'); ?>" class="btn btn-outline-success">
        <i class="fas fa-arrow-left me-2"></i>Back to Marketplace
      </a>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>

</html>