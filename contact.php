<?php
require_once 'includes/config.php';
$page_title = "Contact Us - WOOD CONNECT";
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    // Handle contact form submission
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    // In a real application, you would send an email here
    $success_message = "Thank you for your message! We'll get back to you soon.";
}
?>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-5">Contact Us</h1>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title">Send us a Message</h5>
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Subject</label>
                                        <select class="form-select" id="subject" name="subject" required>
                                            <option value="">Select a subject</option>
                                            <option value="general">General Inquiry</option>
                                            <option value="support">Technical Support</option>
                                            <option value="partnership">Partnership Opportunity</option>
                                            <option value="verification">Marketer Verification</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body p-4">
                                <h5 class="card-title">Get in Touch</h5>
                                
                                <div class="d-flex align-items-start mb-4">
                                    <i class="fas fa-map-marker-alt text-primary mt-1 me-3"></i>
                                    <div>
                                        <h6>Office Location</h6>
                                        <p class="text-muted mb-0">South-West Nigeria<br>Covering all major timber markets</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-4">
                                    <i class="fas fa-phone text-primary mt-1 me-3"></i>
                                    <div>
                                        <h6>Phone Numbers</h6>
                                        <p class="text-muted mb-0">
                                            +234 800 TIMBER (846237)<br>
                                            +234 901 234 5678
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-4">
                                    <i class="fas fa-envelope text-primary mt-1 me-3"></i>
                                    <div>
                                        <h6>Email Address</h6>
                                        <p class="text-muted mb-0">
                                            info@woodconnect.com.ng<br>
                                            support@woodconnect.com.ng
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-clock text-primary mt-1 me-3"></i>
                                    <div>
                                        <h6>Business Hours</h6>
                                        <p class="text-muted mb-0">
                                            Monday - Friday: 8:00 AM - 6:00 PM<br>
                                            Saturday: 9:00 AM - 4:00 PM<br>
                                            Sunday: Closed
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>