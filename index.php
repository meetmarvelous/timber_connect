<?php
require_once 'includes/config.php';
$page_title = "WOOD CONNECT - Digital Timber Marketplace";

// Get featured species for the homepage
try {
    $featured_species_stmt = $pdo->query("
        SELECT s.*, COUNT(i.id) as marketer_count
        FROM species s
        LEFT JOIN inventory i ON s.id = i.species_id AND i.is_available = TRUE
        GROUP BY s.id
        ORDER BY s.timber_value_rank ASC, marketer_count DESC
        LIMIT 6
    ");
    $featured_species = $featured_species_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featured_species = [];
    error_log("Featured species query error: " . $e->getMessage());
}

// Get total counts for stats
try {
    $total_marketers = $pdo->query("SELECT COUNT(*) FROM marketers WHERE verification_status = 'verified'")->fetchColumn();
    $total_species = $pdo->query("SELECT COUNT(*) FROM species")->fetchColumn();
    $total_listings = $pdo->query("SELECT COUNT(*) FROM inventory WHERE is_available = TRUE")->fetchColumn();
} catch (PDOException $e) {
    $total_marketers = 21; // Fallback to known data
    $total_species = 37;
    $total_listings = 150;
}
?>

    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-80">
                <div class="col-lg-6">
                    <h1 class="hero-title">Connecting Timber Buyers & Marketers in South-West Nigeria</h1>
                    <p class="hero-subtitle">Digital marketplace for quality timber species with verified suppliers and comprehensive product information.</p>
                    <div class="hero-buttons">
                        <a href="/timber-connect/marketplace/" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-search me-2"></i>Find Timber
                        </a>
                        <a href="/timber-connect/marketer-register.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Register as Marketer
                        </a>
                    </div>
                    <div class="hero-stats mt-5">
                        <div class="row">
                            <div class="col-sm-4">
                                <h4><?php echo $total_marketers; ?>+</h4>
                                <p>Verified Marketers</p>
                            </div>
                            <div class="col-sm-4">
                                <h4><?php echo $total_species; ?>+</h4>
                                <p>Timber Species</p>
                            </div>
                            <div class="col-sm-4">
                                <h4><?php echo $total_listings; ?>+</h4>
                                <p>Active Listings</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="/timber-connect/assets/images/hero-timber.jpg" alt="Quality Timber" class="img-fluid rounded-3 shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-title">Why Choose WOOD CONNECT?</h2>
                    <p class="section-subtitle">Comprehensive solutions for timber trading in South-West Nigeria</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tree"></i>
                        </div>
                        <h4>Species Database</h4>
                        <p>Comprehensive information on <?php echo $total_species; ?>+ timber species with technical specifications and uses.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Verified Marketers</h4>
                        <p>Connect with <?php echo $total_marketers; ?>+ trusted and verified timber marketers across South-West Nigeria.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Mobile Optimized</h4>
                        <p>Access our platform seamlessly on any device, even with limited internet connectivity.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timber Species Preview -->
    <section class="species-preview py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8">
                    <h2 class="section-title">Popular Timber Species</h2>
                    <p class="section-subtitle">Discover the most sought-after timber varieties in our marketplace</p>
                </div>
                <div class="col-lg-4 text-end">
                    <a href="/timber-connect/species/directory.php" class="btn btn-outline-primary">View All Species</a>
                </div>
            </div>

            <?php if (!empty($featured_species)): ?>
                <div class="row g-4">
                    <?php foreach ($featured_species as $species):
                        $common_names = json_decode($species['common_names'], true);
                        $primary_name = $common_names[0] ?? $species['scientific_name'];
                        $common_uses = json_decode($species['common_uses'], true);
                        $primary_use = $common_uses[0] ?? 'Various uses';
                    ?>
                        <div class="col-md-4">
                            <div class="card h-100 species-card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-success"><?php echo $primary_name; ?></h5>
                                    <p class="card-text text-muted scientific-name">
                                        <em><?php echo $species['scientific_name']; ?></em>
                                    </p>
                                    <div class="species-specs mb-3">
                                        <div class="spec-item">
                                            <strong>Density:</strong> <?php echo $species['density_range'] ?: 'N/A'; ?>
                                        </div>
                                        <div class="spec-item">
                                            <strong>Durability:</strong>
                                            <span class="badge bg-<?php
                                                                    echo $species['durability'] === 'Very Durable' ? 'success' : ($species['durability'] === 'Durable' ? 'primary' : 'warning');
                                                                    ?>">
                                                <?php echo $species['durability'] ?: 'N/A'; ?>
                                            </span>
                                        </div>
                                        <div class="spec-item">
                                            <strong>Common Use:</strong> <?php echo $primary_use; ?>
                                        </div>
                                    </div>
                                    <div class="availability-info mt-auto">
                                        <small class="text-muted">
                                            <i class="fas fa-store me-1"></i>
                                            Available from <?php echo $species['marketer_count']; ?> marketer(s)
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                        <a href="/timber-connect/species/profile.php?id=<?php echo $species['id']; ?>" class="btn btn-outline-success btn-sm">
                                            View Details
                                        </a>
                                        <a href="/timber-connect/marketplace/search.php?species=<?php echo urlencode($primary_name); ?>" class="btn btn-success btn-sm">
                                            Find Suppliers
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Timber species information is being loaded. Please check back shortly.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- States Coverage Section -->
    <section class="states-section py-5 bg-success text-white">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-title">Covering South-West Nigeria</h2>
                    <p class="section-subtitle">Connect with timber marketers across all major states</p>
                </div>
            </div>
            <div class="row g-4">
                <?php foreach ($nigerian_states as $state => $lgas): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="state-card text-center">
                            <div class="state-icon mb-3">
                                <i class="fas fa-map-marker-alt fa-2x"></i>
                            </div>
                            <h5><?php echo $state; ?></h5>
                            <p class="small opacity-75">
                                <?php echo count($lgas); ?> Local Governments
                            </p>
                            <a href="/timber-connect/marketplace/search.php?state=<?php echo urlencode($state); ?>" class="btn btn-outline-light btn-sm">
                                Browse Timber
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3>Ready to Transform Your Timber Business?</h3>
                    <p class="mb-0">Join hundreds of timber marketers already growing their business with WOOD CONNECT</p>
                </div>
                <div class="col-lg-4 text-end">
                    <a href="/timber-connect/marketer-register.php" class="btn btn-light btn-lg">Get Started Today</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Simple script for any homepage interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add any homepage-specific JavaScript here
            console.log('WOOD CONNECT homepage loaded successfully');
        });
    </script>
</body>

</html>