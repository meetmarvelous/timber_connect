<?php
require_once '../includes/config.php';
$auth->requireAdmin();

$page_title = "Manage Marketers - WOOD CONNECT";
$success_message = '';
$error_message = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
  if (isset($_POST['verify_marketer'])) {
    $stmt = $pdo->prepare("UPDATE marketers SET verification_status = 'verified' WHERE id = ?");
    $stmt->execute([$_POST['marketer_id']]);
    $success_message = "Marketer verified successfully!";
  } elseif (isset($_POST['reject_marketer'])) {
    $stmt = $pdo->prepare("UPDATE marketers SET verification_status = 'rejected' WHERE id = ?");
    $stmt->execute([$_POST['marketer_id']]);
    $success_message = "Marketer rejected successfully!";
  } elseif (isset($_POST['toggle_active'])) {
    $stmt = $pdo->prepare("UPDATE marketers SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$_POST['marketer_id']]);
    $success_message = "Marketer status updated!";
  }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$where = "WHERE 1=1";
$params = [];

if ($filter === 'pending') {
  $where .= " AND verification_status = 'pending'";
} elseif ($filter === 'verified') {
  $where .= " AND verification_status = 'verified'";
} elseif ($filter === 'rejected') {
  $where .= " AND verification_status = 'rejected'";
} elseif ($filter === 'inactive') {
  $where .= " AND is_active = FALSE";
}

// Get marketers
$stmt = $pdo->prepare("
    SELECT m.*, 
           COUNT(i.id) as listing_count,
           COUNT(DISTINCT inv.id) as inquiry_count
    FROM marketers m
    LEFT JOIN inventory i ON m.id = i.marketer_id
    LEFT JOIN inquiries inv ON m.id = inv.marketer_id
    $where
    GROUP BY m.id
    ORDER BY m.registration_date DESC
");
$stmt->execute($params);
$marketers = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid py-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title text-success">
            <i class="fas fa-cog me-2"></i>Admin Panel
          </h5>
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
        <h2 class="text-success">Manage Marketers</h2>
        <div class="btn-group">
          <a href="?filter=all" class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
          <a href="?filter=pending" class="btn btn-outline-warning <?php echo $filter === 'pending' ? 'active' : ''; ?>">
            Pending
            <?php
            $pending_count = count(array_filter($marketers, fn($m) => $m['verification_status'] === 'pending'));
            if ($pending_count > 0): ?>
              <span class="badge bg-warning text-dark ms-1"><?php echo $pending_count; ?></span>
            <?php endif; ?>
          </a>
          <a href="?filter=verified" class="btn btn-outline-success <?php echo $filter === 'verified' ? 'active' : ''; ?>">Verified</a>
          <a href="?filter=rejected" class="btn btn-outline-danger <?php echo $filter === 'rejected' ? 'active' : ''; ?>">Rejected</a>
          <a href="?filter=inactive" class="btn btn-outline-secondary <?php echo $filter === 'inactive' ? 'active' : ''; ?>">Inactive</a>
        </div>
      </div>

      <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h5 class="mb-0">
            <?php echo ucfirst($filter); ?> Marketers (<?php echo count($marketers); ?>)
          </h5>
        </div>
        <div class="card-body p-0">
          <?php if (empty($marketers)): ?>
            <div class="text-center py-5">
              <i class="fas fa-users fa-3x text-muted mb-3"></i>
              <h5>No marketers found</h5>
              <p class="text-muted">No marketers match the current filter.</p>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Business Name</th>
                    <th>Owner</th>
                    <th>Location</th>
                    <th>Listings</th>
                    <th>Inquiries</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($marketers as $marketer): ?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars($marketer['business_name']); ?></strong>
                        <br><small class="text-muted"><?php echo $marketer['email']; ?></small>
                      </td>
                      <td><?php echo htmlspecialchars($marketer['owner_name']); ?></td>
                      <td>
                        <small>
                          <?php echo $marketer['city']; ?>, <?php echo $marketer['state']; ?>
                          <br><?php echo $marketer['local_government']; ?>
                        </small>
                      </td>
                      <td>
                        <span class="badge bg-secondary"><?php echo $marketer['listing_count']; ?></span>
                      </td>
                      <td>
                        <span class="badge bg-info"><?php echo $marketer['inquiry_count']; ?></span>
                      </td>
                      <td>
                        <span class="badge bg-<?php
                                              echo $marketer['verification_status'] === 'verified' ? 'success' : ($marketer['verification_status'] === 'pending' ? 'warning' : 'danger');
                                              ?>">
                          <?php echo ucfirst($marketer['verification_status']); ?>
                        </span>
                        <br>
                        <small class="text-muted">
                          <?php echo $marketer['is_active'] ? 'Active' : 'Inactive'; ?>
                        </small>
                      </td>
                      <td>
                        <small class="text-muted">
                          <?php echo date('M j, Y', strtotime($marketer['registration_date'])); ?>
                        </small>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <?php if ($marketer['verification_status'] === 'pending'): ?>
                            <form method="POST" class="d-inline">
                              <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                              <input type="hidden" name="marketer_id" value="<?php echo $marketer['id']; ?>">
                              <button type="submit" name="verify_marketer" class="btn btn-success" title="Verify">
                                <i class="fas fa-check"></i>
                              </button>
                            </form>
                            <form method="POST" class="d-inline">
                              <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                              <input type="hidden" name="marketer_id" value="<?php echo $marketer['id']; ?>">
                              <button type="submit" name="reject_marketer" class="btn btn-danger" title="Reject">
                                <i class="fas fa-times"></i>
                              </button>
                            </form>
                          <?php endif; ?>
                          <form method="POST" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="marketer_id" value="<?php echo $marketer['id']; ?>">
                            <button type="submit" name="toggle_active" class="btn btn-<?php echo $marketer['is_active'] ? 'warning' : 'success'; ?>"
                              title="<?php echo $marketer['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                              <i class="fas fa-<?php echo $marketer['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                            </button>
                          </form>
                          <a href="<?php echo url('marketplace/profile.php?marketer_id=' . $marketer['id']); ?>"
                            class="btn btn-info" title="View Profile" target="_blank">
                            <i class="fas fa-eye"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="row mt-4">
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body text-center py-3">
              <h5 class="mb-1"><?php echo count($marketers); ?></h5>
              <small>Total in Filter</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body text-center py-3">
              <h5 class="mb-1"><?php echo count(array_filter($marketers, fn($m) => $m['is_active'])); ?></h5>
              <small>Active</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning text-dark">
            <div class="card-body text-center py-3">
              <h5 class="mb-1"><?php echo count(array_filter($marketers, fn($m) => $m['listing_count'] > 0)); ?></h5>
              <small>With Listings</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body text-center py-3">
              <h5 class="mb-1"><?php echo count(array_filter($marketers, fn($m) => $m['inquiry_count'] > 0)); ?></h5>
              <small>With Inquiries</small>
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