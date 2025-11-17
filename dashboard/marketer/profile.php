<?php
require_once '../../includes/config.php';
$auth->requireMarketer();

$page_title = "Business Profile - WOOD CONNECT";
$marketer_id = $_SESSION['marketer_id'];
$success_message = '';
$error_message = '';

// Get marketer details
$marketer_stmt = $pdo->prepare("SELECT * FROM marketers WHERE id = ?");
$marketer_stmt->execute([$marketer_id]);
$marketer = $marketer_stmt->fetch(PDO::FETCH_ASSOC);

if (!$marketer) {
    header('Location: ' . url('dashboard/marketer/'));
    exit;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    try {
        $stmt = $pdo->prepare("UPDATE marketers SET 
            business_name = ?, owner_name = ?, address = ?, city = ?, state = ?, 
            local_government = ?, phone = ?, business_description = ?
            WHERE id = ?");

        $stmt->execute([
            sanitizeInput($_POST['business_name']),
            sanitizeInput($_POST['owner_name']),
            sanitizeInput($_POST['address']),
            sanitizeInput($_POST['city']),
            $_POST['state'],
            sanitizeInput($_POST['local_government']),
            sanitizeInput($_POST['phone']),
            sanitizeInput($_POST['business_description']),
            $marketer_id
        ]);

        // Update session business name
        $_SESSION['marketer_business_name'] = sanitizeInput($_POST['business_name']);

        $success_message = "Profile updated successfully!";

        // Refresh marketer data
        $marketer_stmt->execute([$marketer_id]);
        $marketer = $marketer_stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}

// Get business statistics
$stats_stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_listings,
        SUM(CASE WHEN is_available = TRUE THEN 1 ELSE 0 END) as active_listings,
        SUM(quantity_available) as total_quantity,
        (SELECT COUNT(*) FROM inquiries WHERE marketer_id = ?) as total_inquiries,
        (SELECT COUNT(*) FROM inquiries WHERE marketer_id = ? AND status = 'completed') as completed_inquiries
    FROM inventory 
    WHERE marketer_id = ?
");
$stats_stmt->execute([$marketer_id, $marketer_id, $marketer_id]);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php
$page_title = "Business Profile - WOOD CONNECT";
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
                <h2>Business Profile</h2>
                <div class="verification-status">
                    <span class="badge bg-<?php echo $marketer['verification_status'] === 'verified' ? 'success' : 'warning'; ?>">
                        <i class="fas fa-<?php echo $marketer['verification_status'] === 'verified' ? 'check-circle' : 'clock'; ?> me-1"></i>
                        <?php echo ucfirst($marketer['verification_status']); ?>
                    </span>
                </div>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-building me-2"></i>Business Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Business Name *</label>
                                            <input type="text" class="form-control" name="business_name"
                                                value="<?php echo htmlspecialchars($marketer['business_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Owner/Manager Name *</label>
                                            <input type="text" class="form-control" name="owner_name"
                                                value="<?php echo htmlspecialchars($marketer['owner_name']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">State *</label>
                                            <select class="form-select" name="state" required>
                                                <option value="">Select State</option>
                                                <?php foreach ($nigerian_states as $state => $lgas): ?>
                                                    <option value="<?php echo $state; ?>"
                                                        <?php echo $marketer['state'] === $state ? 'selected' : ''; ?>>
                                                        <?php echo $state; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Local Government *</label>
                                            <select class="form-select" name="local_government" required>
                                                <option value="">Select LGA</option>
                                                <?php if (isset($nigerian_states[$marketer['state']])): ?>
                                                    <?php foreach ($nigerian_states[$marketer['state']] as $lga): ?>
                                                        <option value="<?php echo $lga; ?>"
                                                            <?php echo $marketer['local_government'] === $lga ? 'selected' : ''; ?>>
                                                            <?php echo $lga; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">City/Town *</label>
                                    <input type="text" class="form-control" name="city"
                                        value="<?php echo htmlspecialchars($marketer['city']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Business Address *</label>
                                    <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($marketer['address']); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Phone Number *</label>
                                            <input type="tel" class="form-control" name="phone"
                                                value="<?php echo htmlspecialchars($marketer['phone']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Email Address</label>
                                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($marketer['email']); ?>" readonly>
                                            <small class="text-muted">Email cannot be changed</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Business Description</label>
                                    <textarea class="form-control" name="business_description" rows="4"
                                        placeholder="Describe your timber business, specialties, and experience..."><?php echo htmlspecialchars($marketer['business_description']); ?></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Business Statistics -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Business Stats</h5>
                        </div>
                        <div class="card-body">
                            <div class="stats-grid">
                                <div class="stat-item text-center mb-4">
                                    <div class="stat-value text-success"><?php echo $stats['active_listings']; ?></div>
                                    <div class="stat-label">Active Listings</div>
                                </div>
                                <div class="stat-item text-center mb-4">
                                    <div class="stat-value text-primary"><?php echo $stats['total_quantity']; ?></div>
                                    <div class="stat-label">Total Stock</div>
                                </div>
                                <div class="stat-item text-center mb-4">
                                    <div class="stat-value text-warning"><?php echo $stats['total_inquiries']; ?></div>
                                    <div class="stat-label">Total Inquiries</div>
                                </div>
                                <div class="stat-item text-center mb-4">
                                    <div class="stat-value text-info"><?php echo $stats['completed_inquiries']; ?></div>
                                    <div class="stat-label">Completed Sales</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Account Info</h5>
                        </div>
                        <div class="card-body">
                            <div class="account-info">
                                <div class="info-item mb-3">
                                    <strong>Member Since:</strong><br>
                                    <?php echo date('F j, Y', strtotime($marketer['registration_date'])); ?>
                                </div>
                                <div class="info-item mb-3">
                                    <strong>Last Login:</strong><br>
                                    <?php echo $marketer['last_login'] ? date('F j, Y g:i A', strtotime($marketer['last_login'])) : 'Never'; ?>
                                </div>
                                <div class="info-item">
                                    <strong>Account Status:</strong><br>
                                    <span class="badge bg-<?php echo $marketer['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $marketer['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
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
        const stateSelect = document.querySelector('select[name="state"]');
        const lgaSelect = document.querySelector('select[name="local_government"]');

        // Update LGAs when state changes
        stateSelect.addEventListener('change', function() {
            const state = this.value;
            lgaSelect.innerHTML = '<option value="">Select LGA</option>';

            if (state) {
                fetch(`<?php echo url('api/lgas.php'); ?>?state=${encodeURIComponent(state)}`)
                    .then(response => response.json())
                    .then(lgas => {
                        lgas.forEach(lga => {
                            const option = document.createElement('option');
                            option.value = lga;
                            option.textContent = lga;
                            lgaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading LGAs:', error));
            }
        });
    });
</script>

<style>
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    .stats-grid {
        display: grid;
        gap: 1rem;
    }

    .info-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-item:last-child {
        border-bottom: none;
    }
</style>
</body>

</html>