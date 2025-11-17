<?php
require_once 'includes/config.php';
$page_title = "Privacy Policy - WOOD CONNECT";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap 5.3.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6.4.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php 
    $page_title = "Privacy Policy - WOOD CONNECT";
    include 'includes/header.php'; 
    ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <!-- Header Section -->
                <div class="text-center mb-5">
                    <h1 class="display-4 text-success mb-3">Privacy Policy</h1>
                    <p class="lead text-muted">Last updated: <?php echo date('F j, Y'); ?></p>
                </div>

                <!-- Introduction -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Introduction</h2>
                        <p class="mb-0">
                            Welcome to WOOD CONNECT. We are committed to protecting your privacy and ensuring 
                            the security of your personal information. This Privacy Policy explains how we collect, 
                            use, disclose, and safeguard your information when you use our platform.
                        </p>
                    </div>
                </div>

                <!-- Information We Collect -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Information We Collect</h2>
                        
                        <h5 class="mt-4">Personal Information</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Business name and contact information</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Owner/manager name and contact details</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Phone numbers and email addresses</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Business address and location data</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Timber inventory and pricing information</li>
                        </ul>

                        <h5 class="mt-4">Automatically Collected Information</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>IP address and browser type</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Device information and operating system</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Usage data and platform interactions</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Cookies and similar tracking technologies</li>
                        </ul>
                    </div>
                </div>

                <!-- How We Use Your Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">How We Use Your Information</h2>
                        <p>We use the information we collect for the following purposes:</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-store text-success mt-1 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Platform Operations</h6>
                                        <p class="small text-muted mb-0">Facilitating timber trading between buyers and marketers</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-shield-alt text-success mt-1 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Verification & Security</h6>
                                        <p class="small text-muted mb-0">Verifying marketer identities and ensuring platform security</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-comments text-success mt-1 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Customer Support</h6>
                                        <p class="small text-muted mb-0">Providing support and responding to inquiries</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-chart-line text-success mt-1 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Analytics & Improvement</h6>
                                        <p class="small text-muted mb-0">Analyzing usage patterns to improve our services</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-bell text-success mt-1 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Notifications</h6>
                                        <p class="small text-muted mb-0">Sending important updates and market information</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-gavel text-success mt-1 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Legal Compliance</h6>
                                        <p class="small text-muted mb-0">Complying with legal obligations and regulations</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Sharing -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Information Sharing & Disclosure</h2>
                        <p>We may share your information in the following circumstances:</p>
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Business Listings</h6>
                            <p class="mb-0">Your business information and timber listings are visible to other platform users to facilitate trading.</p>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Legal Requirements</h6>
                            <p class="mb-0">We may disclose information when required by law or to protect our rights and safety.</p>
                        </div>
                        
                        <div class="alert alert-success">
                            <h6 class="alert-heading"><i class="fas fa-cog me-2"></i>Service Providers</h6>
                            <p class="mb-0">We may share information with trusted service providers who assist in platform operations.</p>
                        </div>
                    </div>
                </div>

                <!-- Data Security -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Data Security</h2>
                        <p>We implement appropriate security measures to protect your personal information:</p>
                        
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <i class="fas fa-lock fa-2x text-success mb-2"></i>
                                    <h6>Encryption</h6>
                                    <p class="small text-muted mb-0">Data encryption for secure transmission</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                    <h6>Access Control</h6>
                                    <p class="small text-muted mb-0">Strict access controls and authentication</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <i class="fas fa-database fa-2x text-success mb-2"></i>
                                    <h6>Secure Storage</h6>
                                    <p class="small text-muted mb-0">Secure servers and regular backups</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Your Rights -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Your Rights</h2>
                        <p>You have the following rights regarding your personal information:</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-eye text-success me-2"></i><strong>Right to Access:</strong> View your personal data</li>
                                    <li class="mb-2"><i class="fas fa-edit text-success me-2"></i><strong>Right to Correct:</strong> Update inaccurate information</li>
                                    <li class="mb-2"><i class="fas fa-trash text-success me-2"></i><strong>Right to Delete:</strong> Request data deletion</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-download text-success me-2"></i><strong>Right to Portability:</strong> Export your data</li>
                                    <li class="mb-2"><i class="fas fa-ban text-success me-2"></i><strong>Right to Object:</strong> Object to data processing</li>
                                    <li class="mb-2"><i class="fas fa-sliders-h text-success me-2"></i><strong>Right to Restrict:</strong> Limit data processing</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-3 bg-light rounded">
                            <p class="mb-0 small">
                                <strong>To exercise these rights:</strong> Contact us at 
                                <a href="mailto:privacy@timberconnect.ng" class="text-success">privacy@timberconnect.ng</a> 
                                or through your account dashboard.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cookies -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Cookies & Tracking</h2>
                        <p>We use cookies and similar technologies to enhance your experience:</p>
                        
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Cookie Type</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Essential Cookies</strong></td>
                                    <td>Platform functionality and security</td>
                                    <td>Session</td>
                                </tr>
                                <tr>
                                    <td><strong>Preference Cookies</strong></td>
                                    <td>Remember your settings and preferences</td>
                                    <td>1 Year</td>
                                </tr>
                                <tr>
                                    <td><strong>Analytics Cookies</strong></td>
                                    <td>Understand how you use our platform</td>
                                    <td>2 Years</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="alert alert-info mt-3">
                            <p class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                You can control cookies through your browser settings. However, disabling essential cookies may affect platform functionality.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Data Retention -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Data Retention</h2>
                        <p>We retain your personal information only for as long as necessary:</p>
                        
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-user-clock text-success me-2"></i><strong>Active Accounts:</strong> While your account remains active</li>
                            <li class="mb-2"><i class="fas fa-history text-success me-2"></i><strong>Inactive Accounts:</strong> 2 years after last activity</li>
                            <li class="mb-2"><i class="fas fa-receipt text-success me-2"></i><strong>Transaction Records:</strong> 7 years for legal compliance</li>
                            <li class="mb-2"><i class="fas fa-comment text-success me-2"></i><strong>Customer Support:</strong> 3 years after resolution</li>
                        </ul>
                    </div>
                </div>

                <!-- Children's Privacy -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Children's Privacy</h2>
                        <p class="mb-0">
                            WOOD CONNECT is not intended for individuals under the age of 18. We do not knowingly 
                            collect personal information from children. If you believe we have collected information 
                            from a child, please contact us immediately.
                        </p>
                    </div>
                </div>

                <!-- Changes to Policy -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 text-success mb-3">Changes to This Policy</h2>
                        <p class="mb-0">
                            We may update this Privacy Policy from time to time. We will notify you of any changes 
                            by posting the new policy on this page and updating the "Last updated" date. 
                            Continued use of our platform after changes constitutes acceptance of the updated policy.
                        </p>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card shadow-sm border-success">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-envelope me-2"></i>Contact Us</h4>
                    </div>
                    <div class="card-body p-4">
                        <p>If you have any questions about this Privacy Policy, please contact us:</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-envelope text-success mt-1 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Email</h6>
                                        <p class="mb-0">
                                            <a href="mailto:privacy@timberconnect.ng" class="text-success">privacy@timberconnect.ng</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-phone text-success mt-1 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Phone</h6>
                                        <p class="mb-0">+234 800 TIMBER</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <i class="fas fa-map-marker-alt text-success mt-1 me-3"></i>
                            <div>
                                <h6 class="mb-1">Address</h6>
                                <p class="mb-0">South-West Nigeria</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back to Top -->
                <div class="text-center mt-5">
                    <a href="#top" class="btn btn-success">
                        <i class="fas fa-arrow-up me-2"></i>Back to Top
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <style>
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .list-unstyled li {
        padding: 5px 0;
    }
    
    .table th {
        border-color: #dee2e6;
        background-color: #f8f9fa;
    }
    </style>

    <script>
    // Smooth scroll to top
    document.querySelector('a[href="#top"]').addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    </script>
</body>
</html>