<?php
require_once '../includes/config.php';
$auth->requireAdmin();

$page_title = "Verify Marketer - WOOD CONNECT";

$marketer_id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? 'view';

// Get marketer details
$marketer_stmt = $pdo->prepare("
    SELECT m.*, 
           COUNT(i.id) as listing_count,
           COUNT(DISTINCT inv.id) as inquiry_count
    FROM marketers m
    LEFT JOIN inventory i ON m.id = i.marketer_id
    LEFT JOIN inquiries inv ON m.id = inv.marketer_id
    WHERE m.id = ?
    GROUP BY m.id
");
$marketer_stmt->execute([$marketer_id]);
$marketer = $marketer_stmt->fetch(PDO::FETCH_ASSOC);

if (!$marketer) {
  header('Location: marketers.php?error=marketer_not_found');
  exit;
}

$success_message = '';
$error_message = '';

// Handle verification actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
  $verification_status = $_POST['verification_status'];
  $verification_notes = sanitizeInput($_POST['verification_notes']);

  try {
    $stmt = $pdo->prepare("UPDATE marketers SET verification_status = ?, verification_notes = ? WHERE id = ?");
    $stmt->execute([$verification_status, $verification_notes, $marketer_id]);

    $success_message = "Marketer verification status updated successfully!";

    // Redirect if action was from quick actions
    if (isset($_GET['from']) && $_GET['from'] === 'list') {
      header('Location: marketers.php?success=verified');
      exit;
    }
  } catch (Exception $e) {
    $error_message = "Error updating marketer: " . $e->getMessage();
  }
}

// Handle quick actions from URL
if ($action === 'verify' && !$_POST) {
  $stmt = $pdo->prepare("UPDATE marketers SET verification_status = 'verified' WHERE id = ?");
  $stmt->execute([$marketer_id]);
  header('Location: marketers.php?success=verified');
  exit;
}

if ($action === 'reject' && !$_POST) {
  $stmt = $pdo->prepare("UPDATE marketers SET verification_status = 'rejected' WHERE id = ?");
  $stmt->execute([$marketer_id]);
  header('Location: marketers.php?success=rejected');
  exit;
}
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
            <a href="<?php echo url('dashboard/marketers.php'); ?>" class="list-group-item list-group-item-action active">
              <i class="fas fa-users me-2"></i>Manage Marketers
            </a>
            <a href="<?php echo url('dashboard/species.php'); ?>" class="list-group-item list-group-item-action">
              <i class="fas fa-tree me-2"></i>Timber Species
            </a>
            <a href="<?php echo url('dashboard/inquiries.php'); ?>" class="list-group-item list-group-item-action">
              <i class="fas fa-envelope me-2"></i>Customer Inquiries
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Verify Marketer</h2>
        <a href="<?php echo url('dashboard/marketers.php'); ?>" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-1"></i>Back to Marketers
        </a>
      </div>

      <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <div class="row">
        <!-- Marketer Information -->
        <div class="col-lg-6">
          <div class="card mb-4">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0"><i class="fas fa-building me-2"></i>Business Information</h5>
            </div>
            <div class="card-body">
              <table class="table table-borderless">
                <tr>
                  <th width="40%">Business Name:</th>
                  <td><strong><?php echo htmlspecialchars($marketer['business_name']); ?></strong></td>
                </tr>
                <tr>
                  <th>Owner/Manager:</th>
                  <td><?php echo htmlspecialchars($marketer['owner_name']); ?></td>
                </tr>
                <tr>
                  <th>Phone:</th>
                  <td><?php echo htmlspecialchars($marketer['phone']); ?></td>
                </tr>
                <tr>
                  <th>Email:</th>
                  <td><?php echo htmlspecialchars($marketer['email']); ?></td>
                </tr>
                <tr>
                  <th>Location:</th>
                  <td><?php echo htmlspecialchars($marketer['city'] . ', ' . $marketer['state']); ?></td>
                </tr>
                <tr>
                  <th>Local Government:</th>
                  <td><?php echo htmlspecialchars($marketer['local_government']); ?></td>
                </tr>
                <tr>
                  <th>Address:</th>
                  <td><?php echo nl2br(htmlspecialchars($marketer['address'])); ?></td>
                </tr>
                <tr>
                  <th>Registered:</th>
                  <td><?php echo date('M j, Y', strtotime($marketer['registration_date'])); ?></td>
                </tr>
              </table>
            </div>
          </div>

          <?php if ($marketer['business_description']): ?>
            <div class="card mb-4">
              <div class="card-header bg-light">
                <h6 class="mb-0">Business Description</h6>
              </div>
              <div class="card-body">
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($marketer['business_description'])); ?></p>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- Verification Form -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Verification Status</h5>
            </div>
            <div class="card-body">
              <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="mb-4">
                  <label class="form-label fw-semibold">Current Status</label>
                  <div class="d-flex gap-3">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="verification_status"
                        value="pending" id="statusPending"
                        <?php echo $marketer['verification_status'] === 'pending' ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="statusPending">
                        <span class="badge bg-warning">Pending</span>
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="verification_status"
                        value="verified" id="statusVerified"
                        <?php echo $marketer['verification_status'] === 'verified' ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="statusVerified">
                        <span class="badge bg-success">Verified</span>
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="verification_status"
                        value="rejected" id="statusRejected"
                        <?php echo $marketer['verification_status'] === 'rejected' ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="statusRejected">
                        <span class="badge bg-danger">Rejected</span>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="mb-4">
                  <label for="verification_notes" class="form-label fw-semibold">Verification Notes</label>
                  <textarea class="form-control" id="verification_notes" name="verification_notes"
                    rows="4" placeholder="Add any notes about the verification process..."><?php echo htmlspecialchars($marketer['verification_notes'] ?? ''); ?></textarea>
                  <div class="form-text">These notes are for internal use only and won't be shown to the marketer.</div>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-semibold">Business Statistics</label>
                  <div class="row text-center">
                    <div class="col-4">
                      <div class="border rounded p-3">
                        <h4 class="text-primary mb-1"><?php echo $marketer['listing_count']; ?></h4>
                        <small class="text-muted">Listings</small>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="border rounded p-3">
                        <h4 class="text-success mb-1"><?php echo $marketer['inquiry_count']; ?></h4>
                        <small class="text-muted">Inquiries</small>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="border rounded p-3">
                        <h4 class="text-info mb-1">
                          <?php echo date('Y') - date('Y', strtotime($marketer['registration_date'])); ?>
                        </h4>
                        <small class="text-muted">Years</small>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save me-2"></i>Update Verification Status
                  </button>
                  <a href="<?php echo url('marketplace/profile.php?marketer_id=' . $marketer_id); ?>"
                    class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-eye me-2"></i>View Public Profile
                  </a>
                </div>
              </form>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="card mt-4">
            <div class="card-header bg-warning">
              <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
              <div class="d-grid gap-2">
                <a href="<?php echo url('dashboard/marketer-verify.php?id=' . $marketer_id . '&action=verify&from=list'); ?>"
                  class="btn btn-success btn-sm">
                  <i class="fas fa-check me-1"></i>Quick Verify
                </a>
                <a href="<?php echo url('dashboard/marketer-verify.php?id=' . $marketer_id . '&action=reject&from=list'); ?>"
                  class="btn btn-danger btn-sm">
                  <i class="fas fa-times me-1"></i>Quick Reject
                </a>
                <?php if ($marketer['is_active']): ?>
                  <a href="<?php echo url('dashboard/marketers.php?deactivate=' . $marketer_id); ?>"
                    class="btn btn-warning btn-sm">
                    <i class="fas fa-eye-slash me-1"></i>Deactivate Account
                  </a>
                <?php else: ?>
                  <a href="<?php echo url('dashboard/marketers.php?activate=' . $marketer_id); ?>"
                    class="btn btn-info btn-sm">
                    <i class="fas fa-eye me-1"></i>Activate Account
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>

</html>