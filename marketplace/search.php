<?php
require_once '../includes/config.php';

$page_title = "Timber Marketplace - WOOD CONNECT";

// Get search parameters
$state = $_GET['state'] ?? '';
$species = $_GET['species'] ?? '';
$dimensions = $_GET['dimensions'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$city = $_GET['city'] ?? '';

// Build query
$query = "SELECT i.*, s.scientific_name, s.common_names, 
                 m.business_name, m.city, m.state, m.verification_status,
                 m.profile_image as marketer_image
          FROM inventory i
          JOIN species s ON i.species_id = s.id
          JOIN marketers m ON i.marketer_id = m.id
          WHERE i.is_available = TRUE AND m.verification_status = 'verified' AND m.is_active = TRUE";

$params = [];

if (!empty($state)) {
  $query .= " AND m.state = ?";
  $params[] = $state;
}

if (!empty($species)) {
  $query .= " AND (s.scientific_name = ? OR JSON_CONTAINS(s.common_names, ?))";
  $params[] = $species;
  $params[] = json_encode($species);
}

if (!empty($dimensions)) {
  $query .= " AND i.dimensions = ?";
  $params[] = standardizeDimensions($dimensions);
}

if (!empty($min_price)) {
  $query .= " AND i.price_per_unit >= ?";
  $params[] = (float)$min_price;
}

if (!empty($max_price)) {
  $query .= " AND i.price_per_unit <= ?";
  $params[] = (float)$max_price;
}

if (!empty($city)) {
  $query .= " AND m.city LIKE ?";
  $params[] = "%$city%";
}

$query .= " ORDER BY i.updated_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$inventory_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all species for filter dropdown
$species_stmt = $pdo->query("SELECT scientific_name, common_names FROM species ORDER BY scientific_name");
$all_species = $species_stmt->fetchAll(PDO::FETCH_ASSOC);
$page_title = "Timber Marketplace - WOOD CONNECT";
include '../includes/header.php';
?>

<div class="container-fluid py-4">
  <div class="row">
    <!-- Filters Sidebar -->
    <div class="col-lg-3">
      <div class="card shadow-sm sticky-top" style="top: 20px;">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Search Filters</h5>
        </div>
        <div class="card-body">
          <form id="searchFilters" method="GET">
            <div class="mb-3">
              <label class="form-label fw-semibold">State</label>
              <select name="state" class="form-select" onchange="this.form.submit()">
                <option value="">All States</option>
                <?php foreach ($nigerian_states as $state_option => $lgas): ?>
                  <option value="<?php echo $state_option; ?>"
                    <?php echo $state === $state_option ? 'selected' : ''; ?>>
                    <?php echo $state_option; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">City</label>
              <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>"
                placeholder="Enter city name">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Timber Species</label>
              <select name="species" class="form-select" onchange="this.form.submit()">
                <option value="">All Species</option>
                <?php foreach ($all_species as $spec): ?>
                  <?php
                  $common_names = json_decode($spec['common_names'], true);
                  $primary_name = $common_names[0] ?? $spec['scientific_name'];
                  ?>
                  <option value="<?php echo $primary_name; ?>"
                    <?php echo $species === $primary_name ? 'selected' : ''; ?>>
                    <?php echo $primary_name; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Dimensions</label>
              <select name="dimensions" class="form-select" onchange="this.form.submit()">
                <option value="">All Sizes</option>
                <option value="2x2" <?php echo $dimensions === '2x2' ? 'selected' : ''; ?>>2×2 inches</option>
                <option value="2x3" <?php echo $dimensions === '2x3' ? 'selected' : ''; ?>>2×3 inches</option>
                <option value="2x4" <?php echo $dimensions === '2x4' ? 'selected' : ''; ?>>2×4 inches</option>
                <option value="2x6" <?php echo $dimensions === '2x6' ? 'selected' : ''; ?>>2×6 inches</option>
                <option value="2x8" <?php echo $dimensions === '2x8' ? 'selected' : ''; ?>>2×8 inches</option>
                <option value="2x12" <?php echo $dimensions === '2x12' ? 'selected' : ''; ?>>2×12 inches</option>
                <option value="3x4" <?php echo $dimensions === '3x4' ? 'selected' : ''; ?>>3×4 inches</option>
                <option value="3x6" <?php echo $dimensions === '3x6' ? 'selected' : ''; ?>>3×6 inches</option>
                <option value="3x8" <?php echo $dimensions === '3x8' ? 'selected' : ''; ?>>3×8 inches</option>
                <option value="4x6" <?php echo $dimensions === '4x6' ? 'selected' : ''; ?>>4×6 inches</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Price Range (₦)</label>
              <div class="row">
                <div class="col-6">
                  <input type="number" name="min_price" class="form-control" placeholder="Min"
                    value="<?php echo htmlspecialchars($min_price); ?>">
                </div>
                <div class="col-6">
                  <input type="number" name="max_price" class="form-control" placeholder="Max"
                    value="<?php echo htmlspecialchars($max_price); ?>">
                </div>
              </div>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-success">
                <i class="fas fa-search me-1"></i>Apply Filters
              </button>
              <a href="<?php echo url('marketplace/search.php'); ?>" class="btn btn-outline-secondary">Clear All</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Results -->
    <div class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="text-success">Timber Marketplace</h2>
          <p class="text-muted mb-0">
            <strong><?php echo count($inventory_items); ?></strong> listing(s) found
            <?php
            $filters = array_filter([
              $state ? "State: $state" : '',
              $species ? "Species: $species" : '',
              $dimensions ? "Size: $dimensions" : ''
            ]);
            if ($filters) {
              echo ' • ' . implode(' • ', $filters);
            }
            ?>
          </p>
        </div>
        <div class="d-flex gap-2">
          <select class="form-select" style="width: auto;" onchange="sortResults(this.value)">
            <option value="newest">Newest First</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="quantity">Most Available</option>
          </select>
        </div>
      </div>

      <?php if (empty($inventory_items)): ?>
        <div class="text-center py-5">
          <i class="fas fa-search fa-3x text-muted mb-3"></i>
          <h4>No listings found</h4>
          <p class="text-muted">Try adjusting your search filters or browse all available timber.</p>
          <a href="<?php echo url('marketplace/search.php'); ?>" class="btn btn-success">Clear Filters</a>
        </div>
      <?php else: ?>
        <div class="row g-4" id="inventoryResults">
          <?php foreach ($inventory_items as $item):
            $common_names = json_decode($item['common_names'], true);
            $primary_name = $common_names[0] ?? $item['scientific_name'];
          ?>
            <div class="col-xl-4 col-lg-6">
              <div class="card h-100 inventory-card shadow-sm">
                <div class="card-header bg-light">
                  <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-success"><?php echo $primary_name; ?></h6>
                    <?php if ($item['verification_status'] === 'verified'): ?>
                      <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>Verified
                      </span>
                    <?php endif; ?>
                  </div>
                  <small class="text-muted"><?php echo $item['scientific_name']; ?></small>
                </div>
                <div class="card-body d-flex flex-column">
                  <div class="mb-3">
                    <p class="card-text text-muted small mb-2">
                      <i class="fas fa-ruler-combined me-1"></i><strong>Dimensions:</strong> <?php echo $item['dimensions']; ?>
                    </p>
                    <p class="card-text text-muted small mb-2">
                      <i class="fas fa-tag me-1"></i><strong>Grade:</strong> <?php echo ucfirst($item['quality_grade']); ?>
                    </p>
                    <?php if ($item['description']): ?>
                      <p class="card-text small"><?php echo $item['description']; ?></p>
                    <?php endif; ?>
                  </div>

                  <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="h5 text-success mb-0"><?php echo formatNaira($item['price_per_unit']); ?></span>
                      <span class="badge bg-primary">/length</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                      <span>
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?php echo $item['city'] . ', ' . $item['state']; ?>
                      </span>
                      <span>
                        <i class="fas fa-box me-1"></i>
                        <?php echo $item['quantity_available']; ?> available
                      </span>
                    </div>
                    <div class="d-grid gap-2">
                      <a href="<?php echo url('marketplace/inquiry.php'); ?>?inventory_id=<?php echo $item['id']; ?>"
                        class="btn btn-success btn-sm">
                        <i class="fas fa-envelope me-1"></i>Contact Seller
                      </a>
                      <a href="<?php echo url('marketplace/profile.php'); ?>?marketer_id=<?php echo $item['marketer_id']; ?>"
                        class="btn btn-outline-success btn-sm">
                        <i class="fas fa-store me-1"></i>View Business
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
  function sortResults(sortBy) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortBy);
    window.location.href = url.toString();
  }

  // Debounced search for city input
  const cityInput = document.querySelector('input[name="city"]');
  const debouncedSubmit = debounce(() => {
    document.getElementById('searchFilters').submit();
  }, 500);

  cityInput.addEventListener('input', debouncedSubmit);

  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }
</script>
</body>

</html>