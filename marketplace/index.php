<?php
require_once '../includes/config.php';
$page_title = "Timber Marketplace - WOOD CONNECT";

// Get featured inventory items
try {
    $featured_stmt = $pdo->query("
        SELECT i.*, s.scientific_name, s.common_names, 
               m.business_name, m.city, m.state, m.verification_status
        FROM inventory i
        JOIN species s ON i.species_id = s.id
        JOIN marketers m ON i.marketer_id = m.id
        WHERE i.is_available = TRUE 
        AND m.verification_status = 'verified' 
        AND m.is_active = TRUE
        AND i.quantity_available > 0
        ORDER BY i.updated_at DESC
        LIMIT 6
    ");
    $featured_items = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featured_items = [];
    error_log("Marketplace query error: " . $e->getMessage());
}

// Get species for dropdown
try {
    $species_stmt = $pdo->query("SELECT id, scientific_name, common_names FROM species ORDER BY scientific_name");
    $species_list = $species_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $species_list = [];
    error_log("Species query error: " . $e->getMessage());
}

// Get total counts for stats
try {
    $total_inventory = $pdo->query("SELECT COUNT(*) FROM inventory WHERE is_available = TRUE AND quantity_available > 0")->fetchColumn();
} catch (PDOException $e) {
    $total_inventory = 0;
}

    $page_title = "Timber Marketplace - WOOD CONNECT";
    include '../includes/header.php';
    ?>

    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 mb-3">Timber Marketplace</h1>
                <p class="lead text-muted">Discover quality timber from verified marketers across South-West Nigeria</p>

                <!-- Quick Search Form -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                    <form action="<?php echo url('marketplace/search.php'); ?>" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <select name="species" class="form-select">
                                    <option value="">All Species</option>
                                    <?php foreach ($species_list as $species):
                                        $common_names = json_decode($species['common_names'], true);
                                        if (!empty($common_names)):
                                    ?>
                                            <option value="<?php echo $common_names[0]; ?>"><?php echo $common_names[0]; ?></option>
                                    <?php endif;
                                    endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="state" class="form-select">
                                    <option value="">All States</option>
                                    <?php foreach ($nigerian_states as $state => $lgas): ?>
                                        <option value="<?php echo $state; ?>"><?php echo $state; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="dimensions" class="form-select">
                                    <option value="">All Sizes</option>
                                    <option value="2x2">2×2 inches</option>
                                    <option value="2x3">2×3 inches</option>
                                    <option value="2x4">2×4 inches</option>
                                    <option value="2x6">2×6 inches</option>
                                    <option value="2x8">2×8 inches</option>
                                    <option value="2x12">2×12 inches</option>
                                    <option value="3x4">3×4 inches</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Listings -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4">Featured Timber Listings</h2>

                <?php if (empty($featured_items)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No timber listings available at the moment. Check back later or
                        <a href="<?php echo url('marketplace/search.php'); ?>" class="alert-link">browse all listings</a>.
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($featured_items as $item):
                            $common_names = json_decode($item['common_names'], true);
                            $primary_name = $common_names[0] ?? $item['scientific_name'];
                        ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title text-success"><?php echo $primary_name; ?></h5>
                                            <?php if ($item['verification_status'] === 'verified'): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Verified
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-ruler-combined me-1"></i><?php echo $item['dimensions']; ?> •
                                                <i class="fas fa-tag me-1"></i><?php echo ucfirst($item['quality_grade']); ?>
                                            </small>
                                        </p>
                                        <p class="card-text"><?php echo $item['description'] ?: 'Quality timber available.'; ?></p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <span class="h5 text-success mb-0"><?php echo formatNaira($item['price_per_unit']); ?></span>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo $item['city']; ?>, <?php echo $item['state']; ?>
                                            </small>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-box me-1"></i>
                                                <?php echo $item['quantity_available']; ?> units available
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-grid gap-2">
                                        <a href="<?php echo url('marketplace/inquiry.php'); ?>?inventory_id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm">
                                                <i class="fas fa-envelope me-1"></i>Contact Seller
                                            </a>
                                            <a href="<?php echo url('marketplace/profile.php'); ?>?marketer_id=<?php echo $item['marketer_id']; ?>" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-store me-1"></i>View Business
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row">
            <div class="col-12 text-center">
                <div class="card bg-success text-white">
                    <div class="card-body py-5">
                        <h3>Ready to Find Your Perfect Timber?</h3>
                        <p class="mb-4">Browse our complete marketplace with advanced search and filtering options.</p>
                        <a href="<?php echo url('marketplace/search.php'); ?>" class="btn btn-light btn-lg">Explore Full Marketplace</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>