<?php
require_once '../../includes/config.php';
$auth->requireMarketer();

$page_title = "Account Settings - WOOD CONNECT";
$marketer_id = $_SESSION['marketer_id'];
$success_message = '';
$error_message = '';

// Get current marketer data
$stmt = $pdo->prepare("SELECT * FROM marketers WHERE id = ?");
$stmt->execute([$marketer_id]);
$marketer = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    try {
        if (isset($_POST['update_profile'])) {
            // Update profile information
            $stmt = $pdo->prepare("UPDATE marketers SET 
                business_name = ?, owner_name = ?, address = ?, city = ?, state = ?, 
                local_government = ?, business_description = ?
                WHERE id = ?");
            
            $stmt->execute([
                sanitizeInput($_POST['business_name']),
                sanitizeInput($_POST['owner_name']),
                sanitizeInput($_POST['address']),
                sanitizeInput($_POST['city']),
                $_POST['state'],
                sanitizeInput($_POST['local_government']),
                sanitizeInput($_POST['business_description']),
                $marketer_id
            ]);
            
            // Update session
            $_SESSION['marketer_business_name'] = $_POST['business_name'];
            
            $success_message = "Profile updated successfully!";
        }
        elseif (isset($_POST['change_password'])) {
            // Change password
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if (!password_verify($current_password, $marketer['password_hash'])) {
                $error_message = "Current password is incorrect.";
            } elseif ($new_password !== $confirm_password) {
                $error_message = "New passwords do not match.";
            } elseif (strlen($new_password) < 6) {
                $error_message = "New password must be at least 6 characters long.";
            } else {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE marketers SET password_hash = ? WHERE id = ?");
                $stmt->execute([$new_password_hash, $marketer_id]);
                $success_message = "Password changed successfully!";
            }
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
    
    // Refresh marketer data
    $stmt = $pdo->prepare("SELECT * FROM marketers WHERE id = ?");
    $stmt->execute([$marketer_id]);
    $marketer = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
    <?php include '../../includes/header.php'; ?>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <h2 class="mb-4">Account Settings</h2>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Profile Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Business Profile</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Business Name *</label>
                                                <input type="text" class="form-control" name="business_name" 
                                                       value="<?php echo htmlspecialchars($marketer['business_name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Owner/Manager Name *</label>
                                                <input type="text" class="form-control" name="owner_name" 
                                                       value="<?php echo htmlspecialchars($marketer['owner_name']); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">State *</label>
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
                                                <label class="form-label">Local Government Area *</label>
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
                                        <label class="form-label">City/Town *</label>
                                        <input type="text" class="form-control" name="city" 
                                               value="<?php echo htmlspecialchars($marketer['city']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Business Address *</label>
                                        <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($marketer['address']); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Business Description</label>
                                        <textarea class="form-control" name="business_description" rows="4"><?php echo htmlspecialchars($marketer['business_description']); ?></textarea>
                                        <div class="form-text">Describe your timber business, specialties, and experience.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Contact Information</label>
                                        <div class="bg-light p-3 rounded">
                                            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($marketer['email']); ?></p>
                                            <p class="mb-0"><strong>Phone:</strong> <?php echo htmlspecialchars($marketer['phone']); ?></p>
                                            <small class="text-muted">Contact information cannot be changed for security reasons.</small>
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Change Password -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Current Password *</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">New Password *</label>
                                                <input type="password" class="form-control" name="new_password" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Confirm New Password *</label>
                                                <input type="password" class="form-control" name="confirm_password" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-text mb-3">
                                        Password must be at least 6 characters long.
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Account Status -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Account Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="avatar-placeholder bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 80px; font-size: 2rem;">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <h5 class="mt-3 mb-1"><?php echo htmlspecialchars($marketer['business_name']); ?></h5>
                                    <span class="badge bg-<?php echo $marketer['verification_status'] === 'verified' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($marketer['verification_status']); ?>
                                    </span>
                                </div>

                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Registration Date</span>
                                        <small class="text-muted"><?php echo date('M j, Y', strtotime($marketer['registration_date'])); ?></small>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Last Login</span>
                                        <small class="text-muted">
                                            <?php echo $marketer['last_login'] ? date('M j, Y g:i A', strtotime($marketer['last_login'])) : 'Never'; ?>
                                        </small>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Account Status</span>
                                        <span class="badge bg-<?php echo $marketer['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $marketer['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if ($marketer['verification_status'] === 'pending'): ?>
                                    <div class="alert alert-warning mt-3">
                                        <h6><i class="fas fa-clock me-2"></i>Verification Pending</h6>
                                        <p class="small mb-0">Your account is under review. You'll be notified once verified.</p>
                                    </div>
                                <?php elseif ($marketer['verification_status'] === 'rejected'): ?>
                                    <div class="alert alert-danger mt-3">
                                        <h6><i class="fas fa-times-circle me-2"></i>Verification Rejected</h6>
                                        <p class="small mb-0">Please contact support for more information.</p>
                                    </div>
                                <?php endif; ?>
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
                fetch(`../../api/lgas.php?state=${encodeURIComponent(state)}`)
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
</body>
</html>