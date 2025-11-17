<?php
require_once '../includes/config.php';
$page_title = "Contact Marketer - WOOD CONNECT";

$inventory_id = $_GET['inventory_id'] ?? 0;
$success_message = '';
$error_message = '';

// Get inventory item details
$inventory_stmt = $pdo->prepare("
    SELECT i.*, s.scientific_name, s.common_names, m.business_name, m.id as marketer_id
    FROM inventory i
    JOIN species s ON i.species_id = s.id
    JOIN marketers m ON i.marketer_id = m.id
    WHERE i.id = ? AND i.is_available = TRUE AND m.is_active = TRUE
");
$inventory_stmt->execute([$inventory_id]);
$inventory_item = $inventory_stmt->fetch(PDO::FETCH_ASSOC);

if (!$inventory_item) {
    header('Location: ' . url('marketplace/'));
    exit;
}

$common_names = json_decode($inventory_item['common_names'], true);
$species_name = $common_names[0] ?? $inventory_item['scientific_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO inquiries 
            (buyer_name, buyer_phone, buyer_email, marketer_id, species_id, dimensions, quantity, message)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            sanitizeInput($_POST['buyer_name']),
            sanitizeInput($_POST['buyer_phone']),
            sanitizeInput($_POST['buyer_email']),
            $inventory_item['marketer_id'],
            $inventory_item['species_id'],
            $inventory_item['dimensions'],
            $_POST['quantity'],
            sanitizeInput($_POST['message'])
        ]);
        
        $success_message = "Your inquiry has been sent successfully! The marketer will contact you soon.";
    } catch (Exception $e) {
        $error_message = "Error sending inquiry: " . $e->getMessage();
    }
}

    $page_title = "Contact Marketer - WOOD CONNECT";
    include '../includes/header.php'; 
    ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-envelope me-2"></i>Contact Timber Marketer</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                                <div class="mt-3">
                                    <a href="<?php echo url('marketplace/search.php'); ?>" class="btn btn-success me-2">Browse More Timber</a>
                                    <a href="<?php echo url('marketplace/profile.php'); ?>?marketer_id=<?php echo $inventory_item['marketer_id']; ?>" class="btn btn-outline-success">
                                        View Marketer Profile
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>

                            <!-- Timber Item Summary -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6>You're inquiring about:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Timber Species:</strong> <?php echo $species_name; ?><br>
                                            <strong>Dimensions:</strong> <?php echo $inventory_item['dimensions']; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Price:</strong> <?php echo formatNaira($inventory_item['price_per_unit']); ?><br>
                                            <strong>Marketer:</strong> <?php echo htmlspecialchars($inventory_item['business_name']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="buyer_name" class="form-label fw-semibold">Your Name *</label>
                                            <input type="text" class="form-control" id="buyer_name" name="buyer_name" required
                                                   value="<?php echo $_POST['buyer_name'] ?? ''; ?>" placeholder="Enter your full name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="buyer_phone" class="form-label fw-semibold">Phone Number *</label>
                                            <input type="tel" class="form-control" id="buyer_phone" name="buyer_phone" required
                                                   value="<?php echo $_POST['buyer_phone'] ?? ''; ?>" placeholder="08012345678">
                                            <div class="form-text">Format: 08012345678 or +2348012345678</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="buyer_email" class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control" id="buyer_email" name="buyer_email"
                                           value="<?php echo $_POST['buyer_email'] ?? ''; ?>" placeholder="your@email.com">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label fw-semibold">Quantity Needed *</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                                   min="1" max="<?php echo $inventory_item['quantity_available']; ?>" required
                                                   value="<?php echo $_POST['quantity'] ?? 1; ?>">
                                            <div class="form-text">
                                                Maximum available: <?php echo $inventory_item['quantity_available']; ?> units
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label fw-semibold">Your Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required
                                              placeholder="Tell the marketer about your requirements, delivery preferences, or any specific questions..."><?php echo $_POST['message'] ?? ''; ?></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg py-3 fw-semibold">Send Inquiry to Marketer</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Nigerian phone validation
        const phoneInput = document.getElementById('buyer_phone');
        phoneInput.addEventListener('blur', function() {
            const phone = this.value.trim();
            const nigerianRegex = /^(0|\+234)[7-9][0-1]\d{8}$/;
            
            if (phone && !nigerianRegex.test(phone)) {
                this.classList.add('is-invalid');
                showValidationError(this, 'Please enter a valid Nigerian phone number');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });

        function showValidationError(input, message) {
            // Remove existing error
            const existingError = input.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }

            // Add new error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            input.parentNode.appendChild(errorDiv);
        }
    });
    </script>
</body>
</html>