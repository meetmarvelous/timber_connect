<?php
require_once '../includes/config.php';
$auth->requireAdmin();

$page_title = "Manage Timber Species - WOOD CONNECT";
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    try {
        if (isset($_POST['add_species'])) {
            $common_names = json_encode(array_map('trim', explode(',', $_POST['common_names'])));
            $common_uses = json_encode(array_map('trim', explode(',', $_POST['common_uses'])));

            $stmt = $pdo->prepare("INSERT INTO species 
                (scientific_name, common_names, family, density_range, durability, timber_value_rank, common_uses, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                sanitizeInput($_POST['scientific_name']),
                $common_names,
                sanitizeInput($_POST['family']),
                sanitizeInput($_POST['density_range']),
                sanitizeInput($_POST['durability']),
                (int)$_POST['timber_value_rank'],
                $common_uses,
                sanitizeInput($_POST['description'])
            ]);

            $success_message = "Timber species added successfully!";
        } elseif (isset($_POST['update_species'])) {
            $common_names = json_encode(array_map('trim', explode(',', $_POST['common_names'])));
            $common_uses = json_encode(array_map('trim', explode(',', $_POST['common_uses'])));

            $stmt = $pdo->prepare("UPDATE species SET 
                scientific_name = ?, common_names = ?, family = ?, density_range = ?, 
                durability = ?, timber_value_rank = ?, common_uses = ?, description = ?, 
                updated_at = NOW()
                WHERE id = ?");

            $stmt->execute([
                sanitizeInput($_POST['scientific_name']),
                $common_names,
                sanitizeInput($_POST['family']),
                sanitizeInput($_POST['density_range']),
                sanitizeInput($_POST['durability']),
                (int)$_POST['timber_value_rank'],
                $common_uses,
                sanitizeInput($_POST['description']),
                $_POST['species_id']
            ]);

            $success_message = "Timber species updated successfully!";
        } elseif (isset($_POST['delete_species'])) {
            // Check if species is used in inventory
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM inventory WHERE species_id = ?");
            $check_stmt->execute([$_POST['species_id']]);
            $usage_count = $check_stmt->fetchColumn();

            if ($usage_count > 0) {
                $error_message = "Cannot delete species. It is used in " . $usage_count . " inventory items.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM species WHERE id = ?");
                $stmt->execute([$_POST['species_id']]);
                $success_message = "Timber species deleted successfully!";
            }
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Get all species with inventory counts
$species_stmt = $pdo->query("
    SELECT s.*, COUNT(i.id) as inventory_count
    FROM species s
    LEFT JOIN inventory i ON s.id = i.species_id
    GROUP BY s.id
    ORDER BY s.scientific_name
");
$all_species = $species_stmt->fetchAll(PDO::FETCH_ASSOC);

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
                        <a href="<?php echo url('dashboard/species.php'); ?>" class="list-group-item list-group-item-action active">
                            <i class="fas fa-tree me-2"></i>Timber Species
                        </a>
                        <a href="<?php echo url('dashboard/inquiries.php'); ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-envelope me-2"></i>Customer Inquiries
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
                <h2>Manage Timber Species</h2>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSpeciesModal">
                    <i class="fas fa-plus me-1"></i>Add New Species
                </button>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Timber Species (<?php echo count($all_species); ?>)</h5>
                    <span class="badge bg-primary"><?php echo array_sum(array_column($all_species, 'inventory_count')); ?> total listings</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($all_species)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-tree fa-3x text-muted mb-3"></i>
                            <h5>No timber species found</h5>
                            <p class="text-muted">Add your first timber species to get started.</p>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSpeciesModal">
                                Add First Species
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Scientific Name</th>
                                        <th>Common Names</th>
                                        <th>Family</th>
                                        <th>Durability</th>
                                        <th>Value Rank</th>
                                        <th>Listings</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_species as $species):
                                        $common_names = json_decode($species['common_names'], true);
                                        $primary_name = $common_names[0] ?? 'N/A';
                                        $all_names = implode(', ', array_slice($common_names, 0, 3));
                                        if (count($common_names) > 3) {
                                            $all_names .= '...';
                                        }
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $species['scientific_name']; ?></strong>
                                            </td>
                                            <td>
                                                <?php echo $all_names; ?>
                                            </td>
                                            <td><?php echo $species['family'] ?: '—'; ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                                        echo $species['durability'] === 'Very Durable' ? 'success' : ($species['durability'] === 'Durable' ? 'primary' : ($species['durability'] === 'Moderately Durable' ? 'warning' : 'secondary'));
                                                                        ?>">
                                                    <?php echo $species['durability'] ?: '—'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-warning">
                                                    <?php echo str_repeat('★', $species['timber_value_rank']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $species['inventory_count'] > 0 ? 'success' : 'secondary'; ?>">
                                                    <?php echo $species['inventory_count']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info"
                                                        data-bs-toggle="modal" data-bs-target="#viewSpeciesModal"
                                                        data-species='<?php echo htmlspecialchars(json_encode($species), ENT_QUOTES, 'UTF-8'); ?>'>
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning"
                                                        data-bs-toggle="modal" data-bs-target="#editSpeciesModal"
                                                        data-species='<?php echo htmlspecialchars(json_encode($species), ENT_QUOTES, 'UTF-8'); ?>'>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="confirmDelete(<?php echo $species['id']; ?>, '<?php echo addslashes($species['scientific_name']); ?>')">
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

<!-- Add Species Modal -->
<div class="modal fade" id="addSpeciesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Timber Species</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Scientific Name *</label>
                                <input type="text" class="form-control" name="scientific_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Family</label>
                                <input type="text" class="form-control" name="family" placeholder="e.g., Moraceae, Fabaceae">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Common Names *</label>
                        <input type="text" class="form-control" name="common_names" required
                            placeholder="Iroko, Odum, Mvule (comma separated)">
                        <div class="form-text">Enter common names separated by commas</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Density Range</label>
                                <input type="text" class="form-control" name="density_range"
                                    placeholder="640-720 kg/m³">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Durability</label>
                                <select class="form-select" name="durability">
                                    <option value="">Select Durability</option>
                                    <option value="Extremely Durable">Extremely Durable</option>
                                    <option value="Very Durable">Very Durable</option>
                                    <option value="Durable">Durable</option>
                                    <option value="Moderately Durable">Moderately Durable</option>
                                    <option value="Perishable">Perishable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Timber Value Rank *</label>
                                <select class="form-select" name="timber_value_rank" required>
                                    <option value="1">1 Star (Economy)</option>
                                    <option value="2">2 Stars (Standard)</option>
                                    <option value="3" selected>3 Stars (Good)</option>
                                    <option value="4">4 Stars (Premium)</option>
                                    <option value="5">5 Stars (Premium Luxury)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Common Uses</label>
                        <input type="text" class="form-control" name="common_uses"
                            placeholder="Furniture, Flooring, Construction (comma separated)">
                        <div class="form-text">Enter common uses separated by commas</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="description" rows="4"
                            placeholder="Detailed description of the timber species, its characteristics, and typical applications..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_species" class="btn btn-success">Add Species</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Species Modal -->
<div class="modal fade" id="viewSpeciesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Species Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewSpeciesContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Species Modal -->
<div class="modal fade" id="editSpeciesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="species_id" id="edit_species_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Timber Species</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editSpeciesContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_species" class="btn btn-success">Update Species</button>
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
                <input type="hidden" name="species_id" id="delete_species_id">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="delete_species_name"></strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone. Make sure no inventory items are using this species.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_species" class="btn btn-danger">Delete Species</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
    function confirmDelete(speciesId, speciesName) {
        document.getElementById('delete_species_id').value = speciesId;
        document.getElementById('delete_species_name').textContent = speciesName;
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    }

    // View Species Modal
    const viewModal = document.getElementById('viewSpeciesModal');
    viewModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const speciesData = JSON.parse(button.getAttribute('data-species'));
        const commonNames = JSON.parse(speciesData.common_names);
        const commonUses = JSON.parse(speciesData.common_uses);

        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Scientific Name</h6>
                    <p><strong>${speciesData.scientific_name}</strong></p>
                    
                    <h6>Common Names</h6>
                    <p>${commonNames.join(', ')}</p>
                    
                    <h6>Family</h6>
                    <p>${speciesData.family || '—'}</p>
                    
                    <h6>Durability</h6>
                    <p><span class="badge bg-success">${speciesData.durability || '—'}</span></p>
                </div>
                <div class="col-md-6">
                    <h6>Density Range</h6>
                    <p>${speciesData.density_range || '—'}</p>
                    
                    <h6>Value Rank</h6>
                    <p class="text-warning">${'★'.repeat(speciesData.timber_value_rank)}</p>
                    
                    <h6>Common Uses</h6>
                    <p>${commonUses ? commonUses.join(', ') : '—'}</p>
                    
                    <h6>Inventory Listings</h6>
                    <p><span class="badge bg-primary">${speciesData.inventory_count}</span> items</p>
                </div>
            </div>
            ${speciesData.description ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Description</h6>
                    <p>${speciesData.description}</p>
                </div>
            </div>
            ` : ''}
        `;

        document.getElementById('viewSpeciesContent').innerHTML = content;
    });

    // Edit Species Modal
    const editModal = document.getElementById('editSpeciesModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const speciesData = JSON.parse(button.getAttribute('data-species'));
        const commonNames = JSON.parse(speciesData.common_names);
        const commonUses = JSON.parse(speciesData.common_uses);

        document.getElementById('edit_species_id').value = speciesData.id;

        const content = `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Scientific Name *</label>
                        <input type="text" class="form-control" name="scientific_name" value="${speciesData.scientific_name}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Family</label>
                        <input type="text" class="form-control" name="family" value="${speciesData.family || ''}" placeholder="e.g., Moraceae, Fabaceae">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Common Names *</label>
                <input type="text" class="form-control" name="common_names" value="${commonNames.join(', ')}" required>
                <div class="form-text">Enter common names separated by commas</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Density Range</label>
                        <input type="text" class="form-control" name="density_range" value="${speciesData.density_range || ''}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Durability</label>
                        <select class="form-select" name="durability">
                            <option value="">Select Durability</option>
                            <option value="Extremely Durable" ${speciesData.durability === 'Extremely Durable' ? 'selected' : ''}>Extremely Durable</option>
                            <option value="Very Durable" ${speciesData.durability === 'Very Durable' ? 'selected' : ''}>Very Durable</option>
                            <option value="Durable" ${speciesData.durability === 'Durable' ? 'selected' : ''}>Durable</option>
                            <option value="Moderately Durable" ${speciesData.durability === 'Moderately Durable' ? 'selected' : ''}>Moderately Durable</option>
                            <option value="Perishable" ${speciesData.durability === 'Perishable' ? 'selected' : ''}>Perishable</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Timber Value Rank *</label>
                        <select class="form-select" name="timber_value_rank" required>
                            <option value="1" ${speciesData.timber_value_rank == 1 ? 'selected' : ''}>1 Star (Economy)</option>
                            <option value="2" ${speciesData.timber_value_rank == 2 ? 'selected' : ''}>2 Stars (Standard)</option>
                            <option value="3" ${speciesData.timber_value_rank == 3 ? 'selected' : ''}>3 Stars (Good)</option>
                            <option value="4" ${speciesData.timber_value_rank == 4 ? 'selected' : ''}>4 Stars (Premium)</option>
                            <option value="5" ${speciesData.timber_value_rank == 5 ? 'selected' : ''}>5 Stars (Premium Luxury)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Common Uses</label>
                <input type="text" class="form-control" name="common_uses" value="${commonUses ? commonUses.join(', ') : ''}">
                <div class="form-text">Enter common uses separated by commas</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea class="form-control" name="description" rows="4">${speciesData.description || ''}</textarea>
            </div>
        `;

        document.getElementById('editSpeciesContent').innerHTML = content;
    });
</script>
</body>

</html>