<?php
require_once '../includes/config.php';
$auth->requireAdmin();

$page_title = "Manage Inquiries - WOOD CONNECT";

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    if (isset($_POST['update_status'])) {
        $stmt = $pdo->prepare("UPDATE inquiries SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['admin_notes'], $_POST['inquiry_id']]);
        $success_message = "Inquiry status updated successfully!";
    }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$where = "WHERE 1=1";
$params = [];

if ($filter === 'pending') {
    $where .= " AND i.status = 'pending'";
} elseif ($filter === 'contacted') {
    $where .= " AND i.status = 'contacted'";
} elseif ($filter === 'completed') {
    $where .= " AND i.status = 'completed'";
} elseif ($filter === 'cancelled') {
    $where .= " AND i.status = 'cancelled'";
}

// Get inquiries with marketer and species info
$inquiries_stmt = $pdo->prepare("
    SELECT i.*, 
           m.business_name as marketer_business,
           m.phone as marketer_phone,
           s.scientific_name,
           s.common_names
    FROM inquiries i
    JOIN marketers m ON i.marketer_id = m.id
    JOIN species s ON i.species_id = s.id
    $where
    ORDER BY i.created_at DESC
");
$inquiries_stmt->execute($params);
$inquiries = $inquiries_stmt->fetchAll(PDO::FETCH_ASSOC);

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
                        <a href="<?php echo url('dashboard/inquiries.php'); ?>" class="list-group-item list-group-item-action active">
                            <i class="fas fa-envelope me-2"></i>Customer Inquiries
                        </a>
                        <a href="<?php echo url('dashboard/reports.php'); ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                        </a>
                        <a href="<?php echo url('dashboard/settings.php'); ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog me-2"></i>System Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Customer Inquiries</h2>
                <div class="btn-group">
                    <a href="?filter=all" class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
                    <a href="?filter=pending" class="btn btn-outline-warning <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                        Pending <span class="badge bg-warning text-dark"><?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'pending')); ?></span>
                    </a>
                    <a href="?filter=contacted" class="btn btn-outline-info <?php echo $filter === 'contacted' ? 'active' : ''; ?>">Contacted</a>
                    <a href="?filter=completed" class="btn btn-outline-success <?php echo $filter === 'completed' ? 'active' : ''; ?>">Completed</a>
                    <a href="?filter=cancelled" class="btn btn-outline-danger <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
                </div>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <?php echo ucfirst($filter); ?> Inquiries (<?php echo count($inquiries); ?>)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($inquiries)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                            <h5>No inquiries found</h5>
                            <p class="text-muted">No customer inquiries match the current filter.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Buyer Info</th>
                                        <th>Timber Details</th>
                                        <th>Marketer</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inquiries as $inquiry):
                                        $common_names = json_decode($inquiry['common_names'], true);
                                        $species_name = $common_names[0] ?? $inquiry['scientific_name'];
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($inquiry['buyer_name']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    <a href="tel:<?php echo htmlspecialchars($inquiry['buyer_phone']); ?>">
                                                        <?php echo htmlspecialchars($inquiry['buyer_phone']); ?>
                                                    </a>
                                                </small>
                                                <?php if ($inquiry['buyer_email']): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        <a href="mailto:<?php echo htmlspecialchars($inquiry['buyer_email']); ?>">
                                                            <?php echo htmlspecialchars($inquiry['buyer_email']); ?>
                                                        </a>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo $species_name; ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo $inquiry['dimensions']; ?> • Qty: <?php echo $inquiry['quantity']; ?>
                                                </small>
                                                <?php if ($inquiry['message']): ?>
                                                    <br>
                                                    <small class="text-muted" title="<?php echo htmlspecialchars($inquiry['message']); ?>">
                                                        <em>"<?php echo htmlspecialchars(substr($inquiry['message'], 0, 50)); ?><?php echo strlen($inquiry['message']) > 50 ? '...' : ''; ?>"</em>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($inquiry['marketer_business']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    <?php echo htmlspecialchars($inquiry['marketer_phone']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php
                                                                        echo $inquiry['status'] === 'pending' ? 'warning' : ($inquiry['status'] === 'completed' ? 'success' : ($inquiry['status'] === 'contacted' ? 'info' : 'danger'));
                                                                        ?>">
                                                    <?php echo ucfirst($inquiry['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?>
                                                    <br>
                                                    <?php echo date('g:i A', strtotime($inquiry['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#inquiryModal<?php echo $inquiry['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Inquiry Modal -->
                                        <div class="modal fade" id="inquiryModal<?php echo $inquiry['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                        <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Manage Inquiry</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6>Buyer Information</h6>
                                                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($inquiry['buyer_name']); ?></p>
                                                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($inquiry['buyer_phone']); ?></p>
                                                                    <?php if ($inquiry['buyer_email']): ?>
                                                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($inquiry['buyer_email']); ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6>Order Details</h6>
                                                                    <p><strong>Species:</strong> <?php echo $species_name; ?></p>
                                                                    <p><strong>Dimensions:</strong> <?php echo $inquiry['dimensions']; ?></p>
                                                                    <p><strong>Quantity:</strong> <?php echo $inquiry['quantity']; ?></p>
                                                                    <p><strong>Marketer:</strong> <?php echo htmlspecialchars($inquiry['marketer_business']); ?></p>
                                                                </div>
                                                            </div>

                                                            <?php if ($inquiry['message']): ?>
                                                                <div class="mt-3">
                                                                    <h6>Buyer Message</h6>
                                                                    <p class="border p-3 rounded"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                                                </div>
                                                            <?php endif; ?>

                                                            <div class="row mt-3">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Status</label>
                                                                        <select name="status" class="form-select" required>
                                                                            <option value="pending" <?php echo $inquiry['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                            <option value="contacted" <?php echo $inquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                                            <option value="completed" <?php echo $inquiry['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                                            <option value="cancelled" <?php echo $inquiry['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Admin Notes</label>
                                                                <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add any notes about this inquiry..."><?php echo htmlspecialchars($inquiry['admin_notes'] ?? ''); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" name="update_status" class="btn btn-primary">Update Inquiry</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>

</html>