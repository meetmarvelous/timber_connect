<?php
require_once '../../includes/config.php';
$auth->requireMarketer();

$page_title = "Customer Inquiries - WOOD CONNECT";
$marketer_id = $_SESSION['marketer_id'];
$success_message = '';
$error_message = '';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    if (isset($_POST['update_status'])) {
        $stmt = $pdo->prepare("UPDATE inquiries SET status = ?, updated_at = NOW() WHERE id = ? AND marketer_id = ?");
        $stmt->execute([$_POST['status'], $_POST['inquiry_id'], $marketer_id]);
        $success_message = "Inquiry status updated successfully!";
    }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$where = "WHERE i.marketer_id = ?";
$params = [$marketer_id];

if ($filter === 'pending') {
    $where .= " AND i.status = 'pending'";
} elseif ($filter === 'contacted') {
    $where .= " AND i.status = 'contacted'";
} elseif ($filter === 'completed') {
    $where .= " AND i.status = 'completed'";
} elseif ($filter === 'cancelled') {
    $where .= " AND i.status = 'cancelled'";
}

// Get inquiries with species information
$inquiries_stmt = $pdo->prepare("
    SELECT i.*, s.scientific_name, s.common_names,
           (SELECT COUNT(*) FROM inquiries WHERE marketer_id = ? AND status = 'pending') as pending_count
    FROM inquiries i
    JOIN species s ON i.species_id = s.id
    $where
    ORDER BY i.created_at DESC
");
$params[] = $marketer_id; // For pending_count
$inquiries_stmt->execute($params);
$inquiries = $inquiries_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get pending count for badge
$pending_count = $inquiries[0]['pending_count'] ?? 0;
?>
    <?php 
    $page_title = "Customer Inquiries - WOOD CONNECT";
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
                    <h2>Customer Inquiries</h2>
                    <div class="btn-group">
                        <a href="<?php echo url('dashboard/marketer/inquiries.php'); ?>" 
                           class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">
                            All
                        </a>
                        <a href="<?php echo url('dashboard/marketer/inquiries.php?filter=pending'); ?>" 
                           class="btn btn-outline-warning <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                            Pending 
                            <?php if ($pending_count > 0): ?>
                                <span class="badge bg-warning text-dark ms-1"><?php echo $pending_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo url('dashboard/marketer/inquiries.php?filter=contacted'); ?>" 
                           class="btn btn-outline-info <?php echo $filter === 'contacted' ? 'active' : ''; ?>">
                            Contacted
                        </a>
                        <a href="<?php echo url('dashboard/marketer/inquiries.php?filter=completed'); ?>" 
                           class="btn btn-outline-success <?php echo $filter === 'completed' ? 'active' : ''; ?>">
                            Completed
                        </a>
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
                            <i class="fas fa-envelope me-2"></i>
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
                                            <th>Customer</th>
                                            <th>Timber Details</th>
                                            <th>Contact Info</th>
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
                                                </td>
                                                <td>
                                                    <div class="timber-details">
                                                        <strong><?php echo $species_name; ?></strong><br>
                                                        <small class="text-muted">
                                                            <?php echo $inquiry['dimensions']; ?> • 
                                                            Qty: <?php echo $inquiry['quantity']; ?>
                                                        </small>
                                                        <?php if ($inquiry['message']): ?>
                                                            <br><small>"<?php echo htmlspecialchars(substr($inquiry['message'], 0, 50)); ?><?php echo strlen($inquiry['message']) > 50 ? '...' : ''; ?>"</small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="contact-info">
                                                        <small>
                                                            <i class="fas fa-phone text-success me-1"></i>
                                                            <a href="tel:<?php echo htmlspecialchars($inquiry['buyer_phone']); ?>" class="text-decoration-none">
                                                                <?php echo htmlspecialchars($inquiry['buyer_phone']); ?>
                                                            </a>
                                                        </small>
                                                        <?php if ($inquiry['buyer_email']): ?>
                                                            <br>
                                                            <small>
                                                                <i class="fas fa-envelope text-primary me-1"></i>
                                                                <a href="mailto:<?php echo htmlspecialchars($inquiry['buyer_email']); ?>" class="text-decoration-none">
                                                                    <?php echo htmlspecialchars($inquiry['buyer_email']); ?>
                                                                </a>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                        <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 120px;">
                                                            <option value="pending" <?php echo $inquiry['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="contacted" <?php echo $inquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                            <option value="completed" <?php echo $inquiry['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                            <option value="cancelled" <?php echo $inquiry['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                        </select>
                                                        <input type="hidden" name="update_status">
                                                    </form>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?><br>
                                                        <small><?php echo date('g:i A', strtotime($inquiry['created_at'])); ?></small>
                                                    </small>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" data-bs-target="#inquiryModal<?php echo $inquiry['id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Inquiry Detail Modal -->
                                            <div class="modal fade" id="inquiryModal<?php echo $inquiry['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title">Inquiry Details</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6>Customer Information</h6>
                                                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($inquiry['buyer_name']); ?></p>
                                                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($inquiry['buyer_phone']); ?></p>
                                                                    <?php if ($inquiry['buyer_email']): ?>
                                                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($inquiry['buyer_email']); ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6>Order Details</h6>
                                                                    <p><strong>Timber:</strong> <?php echo $species_name; ?></p>
                                                                    <p><strong>Dimensions:</strong> <?php echo $inquiry['dimensions']; ?></p>
                                                                    <p><strong>Quantity:</strong> <?php echo $inquiry['quantity']; ?></p>
                                                                    <p><strong>Status:</strong> 
                                                                        <span class="badge bg-<?php 
                                                                            echo $inquiry['status'] === 'pending' ? 'warning' : 
                                                                                ($inquiry['status'] === 'completed' ? 'success' : 
                                                                                ($inquiry['status'] === 'contacted' ? 'info' : 'danger')); 
                                                                        ?>">
                                                                            <?php echo ucfirst($inquiry['status']); ?>
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <?php if ($inquiry['message']): ?>
                                                                <div class="mt-3">
                                                                    <h6>Customer Message</h6>
                                                                    <div class="alert alert-light border">
                                                                        <?php echo nl2br(htmlspecialchars($inquiry['message'])); ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="mt-3">
                                                                <small class="text-muted">
                                                                    Inquiry received: <?php echo date('F j, Y g:i A', strtotime($inquiry['created_at'])); ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <a href="tel:<?php echo htmlspecialchars($inquiry['buyer_phone']); ?>" class="btn btn-success">
                                                                <i class="fas fa-phone me-1"></i>Call Customer
                                                            </a>
                                                        </div>
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

                <!-- Statistics Cards -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4><?php echo count($inquiries); ?></h4>
                                <p class="mb-0">Total Inquiries</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4><?php echo $pending_count; ?></h4>
                                <p class="mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4><?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'contacted')); ?></h4>
                                <p class="mb-0">Contacted</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4><?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'completed')); ?></h4>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>