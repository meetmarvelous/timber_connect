<?php
require_once '../includes/config.php';
$page_title = "Timber Species Directory - WOOD CONNECT";

// Get all species with counts
try {
  $species_stmt = $pdo->query("
        SELECT s.*, COUNT(i.id) as marketer_count
        FROM species s
        LEFT JOIN inventory i ON s.id = i.species_id AND i.is_available = TRUE
        GROUP BY s.id
        ORDER BY s.timber_value_rank ASC, s.scientific_name ASC
    ");
  $all_species = $species_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $all_species = [];
  error_log("Species directory query error: " . $e->getMessage());
}
$page_title = "Timber Species Directory - WOOD CONNECT";
include '../includes/header.php';
?>

<div class="container py-5">
  <div class="row mb-5">
    <div class="col-lg-8 mx-auto text-center">
      <h1 class="display-4 mb-3">Timber Species Directory</h1>
      <p class="lead text-muted">
        Comprehensive information about timber species available.
      </p>
      <div class="row mt-4">
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body py-3">
              <h4 class="mb-0"><?php echo count($all_species); ?></h4>
              <small>Total Species</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body py-3">
              <h4 class="mb-0"><?php echo array_sum(array_column($all_species, 'marketer_count')); ?></h4>
              <small>Total Listings</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning text-dark">
            <div class="card-body py-3">
              <h4 class="mb-0"><?php echo count(array_filter($all_species, fn($s) => $s['timber_value_rank'] >= 4)); ?></h4>
              <small>Premium Species</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body py-3">
              <h4 class="mb-0"><?php echo count(array_filter($all_species, fn($s) => $s['durability'] === 'Very Durable')); ?></h4>
              <small>Very Durable</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search and Filter -->
  <div class="row mb-4">
    <div class="col-lg-6">
      <div class="input-group">
        <input type="text" class="form-control form-control-lg" placeholder="Search timber species..." id="speciesSearch">
        <button class="btn btn-success" type="button">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="d-flex gap-2 justify-content-end">
        <select class="form-select" style="width: auto;" id="sortSpecies">
          <option value="name">Sort by Name</option>
          <option value="value">Sort by Value</option>
          <option value="durability">Sort by Durability</option>
          <option value="availability">Sort by Availability</option>
        </select>
        <select class="form-select" style="width: auto;" id="filterDurability">
          <option value="">All Durability</option>
          <option value="Very Durable">Very Durable</option>
          <option value="Durable">Durable</option>
          <option value="Moderately Durable">Moderately Durable</option>
          <option value="Perishable">Perishable</option>
        </select>
      </div>
    </div>
  </div>

  <div class="row" id="speciesGrid">
    <?php if (empty($all_species)): ?>
      <div class="col-12">
        <div class="text-center py-5">
          <i class="fas fa-tree fa-3x text-muted mb-3"></i>
          <h4>No Species Available</h4>
          <p class="text-muted">Timber species information will be added soon.</p>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($all_species as $species):
        $common_names = json_decode($species['common_names'], true);
        $common_uses = json_decode($species['common_uses'], true);
        $primary_name = $common_names[0] ?? $species['scientific_name'];
      ?>
        <div class="col-lg-4 col-md-6 mb-4 species-item"
          data-name="<?php echo strtolower($primary_name . ' ' . $species['scientific_name']); ?>"
          data-durability="<?php echo $species['durability']; ?>"
          data-value="<?php echo $species['timber_value_rank']; ?>"
          data-availability="<?php echo $species['marketer_count']; ?>">
          <div class="card h-100 species-card shadow-sm">
            <div class="card-header bg-light">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-success"><?php echo $primary_name; ?></h5>
                <span class="badge bg-<?php
                                      echo $species['timber_value_rank'] >= 4 ? 'warning' : ($species['timber_value_rank'] >= 3 ? 'success' : 'secondary');
                                      ?>">
                  <?php echo str_repeat('★', $species['timber_value_rank']); ?>
                </span>
              </div>
              <small class="text-muted scientific-name">
                <em><?php echo $species['scientific_name']; ?></em>
              </small>
            </div>
            <div class="card-body d-flex flex-column">
              <div class="species-specs mb-3">
                <div class="spec-item">
                  <strong><i class="fas fa-weight me-1"></i>Density:</strong>
                  <?php echo $species['density_range'] ?: 'Not specified'; ?>
                </div>
                <div class="spec-item">
                  <strong><i class="fas fa-shield-alt me-1"></i>Durability:</strong>
                  <span class="badge bg-<?php
                                        echo $species['durability'] === 'Very Durable' ? 'success' : ($species['durability'] === 'Durable' ? 'primary' : ($species['durability'] === 'Moderately Durable' ? 'warning' : 'secondary'));
                                        ?>">
                    <?php echo $species['durability'] ?: 'Not specified'; ?>
                  </span>
                </div>
                <?php if ($common_uses && !empty($common_uses[0])): ?>
                  <div class="spec-item">
                    <strong><i class="fas fa-hammer me-1"></i>Common Use:</strong>
                    <?php echo $common_uses[0]; ?>
                  </div>
                <?php endif; ?>
                <?php if ($species['family']): ?>
                  <div class="spec-item">
                    <strong><i class="fas fa-seedling me-1"></i>Family:</strong>
                    <?php echo $species['family']; ?>
                  </div>
                <?php endif; ?>
              </div>

              <?php if ($species['description']): ?>
                <p class="card-text small flex-grow-1"><?php echo $species['description']; ?></p>
              <?php endif; ?>

              <div class="availability-info mt-auto">
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">
                    <i class="fas fa-store me-1"></i>
                    Available from <?php echo $species['marketer_count']; ?> marketer(s)
                  </small>
                  <?php if ($species['marketer_count'] > 0): ?>
                    <span class="badge bg-success">In Stock</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Out of Stock</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="card-footer bg-transparent">
              <div class="d-grid gap-2">
                <a href="<?php echo url('species/profile.php?id=' . $species['id']); ?>" class="btn btn-outline-success btn-sm">
                  <i class="fas fa-info-circle me-1"></i>View Full Details
                </a>
                <?php if ($species['marketer_count'] > 0): ?>
                  <a href="<?php echo url('marketplace/search.php?species=' . urlencode($primary_name)); ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-shopping-cart me-1"></i>Find Suppliers
                  </a>
                <?php else: ?>
                  <button class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-clock me-1"></i>No Suppliers
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('speciesSearch');
    const sortSelect = document.getElementById('sortSpecies');
    const filterSelect = document.getElementById('filterDurability');
    const speciesItems = document.querySelectorAll('.species-item');

    // Search functionality
    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      filterAndSortSpecies();
    });

    // Sort functionality
    sortSelect.addEventListener('change', filterAndSortSpecies);
    filterSelect.addEventListener('change', filterAndSortSpecies);

    function filterAndSortSpecies() {
      const searchTerm = searchInput.value.toLowerCase();
      const sortBy = sortSelect.value;
      const filterBy = filterSelect.value;

      let visibleItems = [];

      speciesItems.forEach(item => {
        const speciesName = item.getAttribute('data-name');
        const durability = item.getAttribute('data-durability');

        // Filter by search and durability
        const matchesSearch = speciesName.includes(searchTerm);
        const matchesFilter = !filterBy || durability === filterBy;

        if (matchesSearch && matchesFilter) {
          item.style.display = 'block';
          visibleItems.push(item);
        } else {
          item.style.display = 'none';
        }
      });

      // Sort visible items
      visibleItems.sort((a, b) => {
        switch (sortBy) {
          case 'name':
            return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
          case 'value':
            return b.getAttribute('data-value') - a.getAttribute('data-value');
          case 'durability':
            const durabilityOrder = {
              'Very Durable': 4,
              'Durable': 3,
              'Moderately Durable': 2,
              'Perishable': 1
            };
            return durabilityOrder[b.getAttribute('data-durability')] - durabilityOrder[a.getAttribute('data-durability')];
          case 'availability':
            return b.getAttribute('data-availability') - a.getAttribute('data-availability');
          default:
            return 0;
        }
      });

      // Reorder items in DOM
      const grid = document.getElementById('speciesGrid');
      visibleItems.forEach(item => {
        grid.appendChild(item);
      });
    }

    // Initialize
    filterAndSortSpecies();
  });
</script>

<style>
  .species-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .species-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
  }

  .spec-item {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }

  .scientific-name {
    font-size: 0.85rem;
  }

  .species-specs {
    border-left: 3px solid #28a745;
    padding-left: 10px;
  }
</style>
</body>

</html>