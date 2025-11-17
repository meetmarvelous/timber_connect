<?php
require_once '../../includes/config.php';
$auth->requireMarketer();

$page_title = "Manage Inventory - WOOD CONNECT";
$marketer_id = $_SESSION['marketer_id'];
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    try {
        if (isset($_POST['add_inventory'])) {
            // Add new inventory item
            $stmt = $pdo->prepare("INSERT INTO inventory 
                (marketer_id, species_id, dimensions, price_per_unit, quantity_available, unit_type, quality_grade, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $marketer_id,
                $_POST['species_id'],
                standardizeDimensions($_POST['dimensions']),
                $_POST['price_per_unit'],
                $_POST['quantity_available'],
                $_POST['unit_type'],
                $_POST['quality_grade'],
                sanitizeInput($_POST['description'])
            ]);
            
            $success_message = "Inventory item added successfully!";
        }
        elseif (isset($_POST['update_inventory'])) {
            // Update inventory item
            $stmt = $pdo->prepare("UPDATE inventory SET 
                species_id = ?, dimensions = ?, price_per_unit = ?, quantity_available = ?, 
                unit_type = ?, quality_grade = ?, description = ?, updated_at = NOW()
                WHERE id = ? AND marketer_id = ?");
            
            $stmt->execute([
                $_POST['species_id'],
                standardizeDimensions($_POST['dimensions']),
                $_POST['price_per_unit'],
                $_POST['quantity_available'],
                $_POST['unit_type'],
                $_POST['quality_grade'],
                sanitizeInput($_POST['description']),
                $_POST['inventory_id'],
                $marketer_id
            ]);
            
            $success_message = "Inventory item updated successfully!";
        }
        elseif (isset($_POST['toggle_availability'])) {
            // Toggle availability
            $stmt = $pdo->prepare("UPDATE inventory SET is_available = NOT is_available WHERE id = ? AND marketer_id = ?");
            $stmt->execute([$_POST['inventory_id'], $marketer_id]);
            $success_message = "Availability status updated!";
        }
        elseif (isset($_POST['delete_inventory'])) {
            // Delete inventory item
            $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ? AND marketer_id = ?");
            $stmt->execute([$_POST['inventory_id'], $marketer_id]);
            $success_message = "Inventory item deleted successfully!";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Get all inventory items
$inventory_stmt = $pdo->prepare("
    SELECT i.*, s.scientific_name, s.common_names
    FROM inventory i
    JOIN species s ON i.species_id = s.id
    WHERE i.marketer_id = ?
    ORDER BY i.updated_at DESC
");
$inventory_stmt->execute([$marketer_id]);
$inventory_items = $inventory_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all species for dropdown
$species_stmt = $pdo->query("SELECT id, scientific_name, common_names FROM species ORDER BY scientific_name");
$all_species = $species_stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Inventory</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                        <i class="fas fa-plus me-1"></i>Add New Item
                    </button>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Inventory Table -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Current Inventory (<?php echo count($inventory_items); ?> items)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($inventory_items)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                <h5>No inventory items yet</h5>
                                <p class="text-muted">Start by adding your first timber listing.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                                    Add Your First Item
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Timber Species</th>
                                            <th>Dimensions</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inventory_items as $item): 
                                            $common_names = json_decode($item['common_names'], true);
                                            $species_name = $common_names[0] ?? $item['scientific_name'];
                                        ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($species_name); ?></strong>
                                                    <br><small class="text-muted"><?php echo $item['quality_grade']; ?> grade</small>
                                                </td>
                                                <td><?php echo $item['dimensions']; ?></td>
                                                <td>
                                                    <strong class="text-primary"><?php echo formatNaira($item['price_per_unit']); ?></strong>
                                                    <br><small class="text-muted">per <?php echo $item['unit_type']; ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $item['quantity_available'] > 10 ? 'success' : ($item['quantity_available'] > 0 ? 'warning' : 'danger'); ?>">
                                                        <?php echo $item['quantity_available']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $item['is_available'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $item['is_available'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, Y', strtotime($item['updated_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#editInventoryModal"
                                                                data-item='<?php echo json_encode($item); ?>'>
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                            <input type="hidden" name="inventory_id" value="<?php echo $item['id']; ?>">
                                                            <button type="submit" name="toggle_availability" class="btn btn-outline-<?php echo $item['is_available'] ? 'warning' : 'success'; ?>">
                                                                <i class="fas fa-<?php echo $item['is_available'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="confirmDelete(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($species_name); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
            </div>
        </div>
    </div>

    <!-- Add Inventory Modal -->
    <div class="modal fade" id="addInventoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Inventory Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Timber Species *</label>
                                    <select name="species_id" class="form-select" required>
                                        <option value="">Select Species</option>
                                        <?php foreach ($all_species as $species): 
                                            $common_names = json_decode($species['common_names'], true);
                                        ?>
                                            <option value="<?php echo $species['id']; ?>">
                                                <?php echo $common_names[0]; ?> (<?php echo $species['scientific_name']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Dimensions *</label>
                                    <select name="dimensions" class="form-select" required>
                                        <option value="">Select Size</option>
                                        <option value="2x2">2×2 inches</option>
                                        <option value="2x4">2×4 inches</option>
                                        <option value="3x4">3×4 inches</option>
                                        <option value="4x4">4×4 inches</option>
                                        <option value="6x6">6×6 inches</option>
                                        <option value="custom">Custom Size</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Price per Unit (₦) *</label>
                                    <input type="number" name="price_per_unit" class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Quantity Available *</label>
                                    <input type="number" name="quantity_available" class="form-control" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Unit Type *</label>
                                    <select name="unit_type" class="form-select" required>
                                        <option value="length">Per Length</option>
                                        <option value="piece">Per Piece</option>
                                        <option value="bundle">Per Bundle</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Quality Grade</label>
                                    <select name="quality_grade" class="form-select">
                                        <option value="premium">Premium</option>
                                        <option value="standard" selected>Standard</option>
                                        <option value="economy">Economy</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe the quality, condition, or any special features of this timber..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_inventory" class="btn btn-primary">Add to Inventory</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Inventory Modal -->
    <div class="modal fade" id="editInventoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="inventory_id" id="edit_inventory_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Inventory Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form content same as add modal, populated via JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_inventory" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="inventory_id" id="delete_inventory_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="delete_item_name"></strong> from your inventory?</p>
                        <p class="text-danger"><small>This action cannot be undone.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_inventory" class="btn btn-danger">Delete Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
    function confirmDelete(inventoryId, itemName) {
        document.getElementById('delete_inventory_id').value = inventoryId;
        document.getElementById('delete_item_name').textContent = itemName;
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    }

    // Edit modal population
    const editModal = document.getElementById('editInventoryModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const itemData = JSON.parse(button.getAttribute('data-item'));
        
        // Populate form fields
        document.getElementById('edit_inventory_id').value = itemData.id;
        // ... populate other fields similarly
    });
    </script>
</body>
</html>