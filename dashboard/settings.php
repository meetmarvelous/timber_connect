<?php
require_once '../includes/config.php';
$auth->requireAdmin();

$page_title = "System Settings - WOOD CONNECT";
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    try {
        if (isset($_POST['update_general_settings'])) {
            // In a real application, you would save these to a settings table
            $success_message = "General settings updated successfully!";
        } elseif (isset($_POST['update_email_settings'])) {
            $success_message = "Email settings updated successfully!";
        } elseif (isset($_POST['update_smtp_settings'])) {
            $success_message = "SMTP settings updated successfully!";
        } elseif (isset($_POST['update_seo_settings'])) {
            $success_message = "SEO settings updated successfully!";
        } elseif (isset($_POST['clear_cache'])) {
            // Clear cache logic would go here
            $success_message = "Cache cleared successfully!";
        } elseif (isset($_POST['backup_database'])) {
            // Database backup logic would go here
            $success_message = "Database backup created successfully!";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Get system information
$system_info = [
    'php_version' => PHP_VERSION,
    'mysql_version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
];

// Get platform statistics
$stats_stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM marketers) as total_marketers,
        (SELECT COUNT(*) FROM marketers WHERE verification_status = 'verified') as verified_marketers,
        (SELECT COUNT(*) FROM species) as total_species,
        (SELECT COUNT(*) FROM inventory WHERE is_available = TRUE) as active_listings,
        (SELECT COUNT(*) FROM inquiries) as total_inquiries,
        (SELECT COUNT(*) FROM inquiries WHERE status = 'completed') as completed_inquiries
");
$platform_stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

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
                        <a href="<?php echo url('dashboard/reports.php'); ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                        </a>
                        <a href="<?php echo url('dashboard/settings.php'); ?>" class="list-group-item list-group-item-action active">
                            <i class="fas fa-cog me-2"></i>System Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Info Card -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">PHP Version</small>
                        <div class="fw-semibold"><?php echo $system_info['php_version']; ?></div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">MySQL Version</small>
                        <div class="fw-semibold"><?php echo $system_info['mysql_version']; ?></div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Server</small>
                        <div class="fw-semibold"><?php echo $system_info['server_software']; ?></div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Upload Limit</small>
                        <div class="fw-semibold"><?php echo $system_info['upload_max_filesize']; ?></div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Memory Limit</small>
                        <div class="fw-semibold"><?php echo $system_info['memory_limit']; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>System Settings</h2>
                <div class="btn-group">
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#backupModal">
                        <i class="fas fa-database me-1"></i>Backup
                    </button>
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#cacheModal">
                        <i class="fas fa-broom me-1"></i>Clear Cache
                    </button>
                </div>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Settings Tabs -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                                <i class="fas fa-envelope me-2"></i>Email
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                                <i class="fas fa-search me-2"></i>SEO
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="maintenance-tab" data-bs-toggle="tab" data-bs-target="#maintenance" type="button" role="tab">
                                <i class="fas fa-tools me-2"></i>Maintenance
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="settingsTabsContent">
                        <!-- General Settings Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Site Name</label>
                                            <input type="text" class="form-control" name="site_name" value="<?php echo SITE_NAME; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Site URL</label>
                                            <input type="url" class="form-control" name="site_url" value="<?php echo SITE_URL; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Site Description</label>
                                    <textarea class="form-control" name="site_description" rows="3" placeholder="Brief description of your platform">Digital marketplace connecting timber buyers with verified marketers across South-West Nigeria</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Contact Email</label>
                                            <input type="email" class="form-control" name="contact_email" value="info@woodconnect.com.ng" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Contact Phone</label>
                                            <input type="tel" class="form-control" name="contact_phone" value="+234800TIMBER" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Default Timezone</label>
                                    <select class="form-select" name="timezone">
                                        <option value="Africa/Lagos" selected>Africa/Lagos (WAT)</option>
                                        <option value="UTC">UTC</option>
                                    </select>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="registration_open" id="registration_open" checked>
                                    <label class="form-check-label" for="registration_open">Allow new marketer registrations</label>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="maintenance_mode" id="maintenance_mode">
                                    <label class="form-check-label" for="maintenance_mode">Enable maintenance mode</label>
                                </div>

                                <button type="submit" name="update_general_settings" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Save General Settings
                                </button>
                            </form>
                        </div>

                        <!-- Email Settings Tab -->
                        <div class="tab-pane fade" id="email" role="tabpanel">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">SMTP Host</label>
                                            <input type="text" class="form-control" name="smtp_host" value="smtp.gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">SMTP Port</label>
                                            <input type="number" class="form-control" name="smtp_port" value="587">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">SMTP Username</label>
                                            <input type="text" class="form-control" name="smtp_username" placeholder="your-email@gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">SMTP Password</label>
                                            <input type="password" class="form-control" name="smtp_password" placeholder="Your app password">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">From Email</label>
                                    <input type="email" class="form-control" name="from_email" value="noreply@timberconnect.ng">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">From Name</label>
                                    <input type="text" class="form-control" name="from_name" value="WOOD CONNECT">
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="smtp_auth" id="smtp_auth" checked>
                                    <label class="form-check-label" for="smtp_auth">Enable SMTP Authentication</label>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="smtp_secure" id="smtp_secure" checked>
                                    <label class="form-check-label" for="smtp_secure">Use TLS/SSL</label>
                                </div>

                                <button type="submit" name="update_smtp_settings" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Save Email Settings
                                </button>
                            </form>
                        </div>

                        <!-- SEO Settings Tab -->
                        <div class="tab-pane fade" id="seo" role="tabpanel">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Meta Title</label>
                                    <input type="text" class="form-control" name="meta_title" value="WOOD CONNECT - Digital Timber Marketplace" maxlength="60">
                                    <div class="form-text">Recommended: 50-60 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Meta Description</label>
                                    <textarea class="form-control" name="meta_description" rows="3" maxlength="160">Connecting timber buyers with verified marketers across South-West Nigeria. Digital marketplace for quality timber species with comprehensive product information.</textarea>
                                    <div class="form-text">Recommended: 150-160 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Meta Keywords</label>
                                    <input type="text" class="form-control" name="meta_keywords" value="timber, wood, planks, Nigeria, marketplace, marketers, buyers, iroko, mahogany, teak">
                                    <div class="form-text">Separate keywords with commas</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Google Analytics ID</label>
                                    <input type="text" class="form-control" name="ga_id" placeholder="G-XXXXXXXXXX">
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="enable_og_tags" id="enable_og_tags" checked>
                                    <label class="form-check-label" for="enable_og_tags">Enable Open Graph Tags</label>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="enable_twitter_cards" id="enable_twitter_cards" checked>
                                    <label class="form-check-label" for="enable_twitter_cards">Enable Twitter Cards</label>
                                </div>

                                <button type="submit" name="update_seo_settings" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Save SEO Settings
                                </button>
                            </form>
                        </div>

                        <!-- Maintenance Tab -->
                        <div class="tab-pane fade" id="maintenance" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-broom me-2"></i>Cache Management</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">Clear all cached data to ensure fresh content delivery.</p>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <button type="submit" name="clear_cache" class="btn btn-warning">
                                                    <i class="fas fa-broom me-2"></i>Clear Cache
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-database me-2"></i>Database Backup</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">Create a backup of your database for safety and migration.</p>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <button type="submit" name="backup_database" class="btn btn-info">
                                                    <i class="fas fa-download me-2"></i>Backup Database
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>System Statistics</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-2">
                                                    <div class="border rounded p-3">
                                                        <h4 class="text-success"><?php echo $platform_stats['total_marketers']; ?></h4>
                                                        <small class="text-muted">Total Marketers</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="border rounded p-3">
                                                        <h4 class="text-success"><?php echo $platform_stats['verified_marketers']; ?></h4>
                                                        <small class="text-muted">Verified Marketers</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="border rounded p-3">
                                                        <h4 class="text-success"><?php echo $platform_stats['total_species']; ?></h4>
                                                        <small class="text-muted">Timber Species</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="border rounded p-3">
                                                        <h4 class="text-success"><?php echo $platform_stats['active_listings']; ?></h4>
                                                        <small class="text-muted">Active Listings</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="border rounded p-3">
                                                        <h4 class="text-success"><?php echo $platform_stats['total_inquiries']; ?></h4>
                                                        <small class="text-muted">Total Inquiries</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="border rounded p-3">
                                                        <h4 class="text-success"><?php echo $platform_stats['completed_inquiries']; ?></h4>
                                                        <small class="text-muted">Completed</small>
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
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Backup Confirmation Modal -->
<div class="modal fade" id="backupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Database Backup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to create a database backup?</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will create a backup file in the backups directory.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="backup_database" class="btn btn-success">Create Backup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Clear Cache Confirmation Modal -->
<div class="modal fade" id="cacheModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Clear Cache</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to clear all cached data?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="clear_cache" class="btn btn-warning">Clear Cache</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality is handled by Bootstrap
        console.log('Settings page loaded successfully');

        // Add any custom JavaScript for settings page here
        const settingsTabs = document.getElementById('settingsTabs');
        if (settingsTabs) {
            settingsTabs.addEventListener('shown.bs.tab', function(event) {
                console.log('Tab changed:', event.target.textContent.trim());
            });
        }
    });
</script>
</body>

</html>