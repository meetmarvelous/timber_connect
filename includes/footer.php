    <!-- Footer -->
    <footer class="footer bg-dark text-light pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <!-- Brand Column -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-brand">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-tree fa-2x text-success me-2"></i>
                            <h4 class="fw-bold text-white mb-0">WOOD CONNECT</h4>
                        </div>
                        <p class="text-light opacity-75 mb-4">
                            Connecting timber buyers with verified marketers across South-West Nigeria.
                            Your trusted digital marketplace for quality timber trading.
                        </p>
                        <div class="social-links">
                            <a href="#" class="social-link" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="text-white fw-semibold mb-3">Quick Links</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2">
                            <a href="<?php echo url('/'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-home me-2 small"></i>Home
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo url('marketplace/'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-store me-2 small"></i>Marketplace
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo url('species/directory.php'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-tree me-2 small"></i>Timber Species
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo url('about.php'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-info-circle me-2 small"></i>About Us
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo url('contact.php'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-envelope me-2 small"></i>Contact
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- For Marketers -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-white fw-semibold mb-3">For Marketers</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2">
                            <a href="<?php echo url('marketer-register.php'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-user-plus me-2 small"></i>Register Business
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo url('login.php'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-sign-in-alt me-2 small"></i>Marketer Login
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo url('marketplace/'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-search me-2 small"></i>Browse Listings
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo url('contact.php'); ?>" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-headset me-2 small"></i>Support
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-white fw-semibold mb-3">Contact Info</h5>
                    <div class="contact-info">
                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-map-marker-alt text-success mt-1 me-3 small"></i>
                            <div>
                                <h6 class="text-white mb-1 small fw-semibold">Office Location</h6>
                                <p class="text-light opacity-75 mb-0 small">South-West Nigeria</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-phone text-success mt-1 me-3 small"></i>
                            <div>
                                <h6 class="text-white mb-1 small fw-semibold">Phone Numbers</h6>
                                <p class="text-light opacity-75 mb-0 small">
                                    +234 800 TIMBER<br>
                                    +234 901 234 5678
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <i class="fas fa-envelope text-success mt-1 me-3 small"></i>
                            <div>
                                <h6 class="text-white mb-1 small fw-semibold">Email Address</h6>
                                <p class="text-light opacity-75 mb-0 small">
                                    info@woodconnect.com.ng<br>
                                    support@woodconnect.com.ng
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr class="border-secondary my-4">

            <!-- Bottom Bar -->
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-light opacity-75 mb-0 small">
                        &copy; <?php echo date('Y'); ?> WOOD CONNECT. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-light opacity-75 mb-0 small">
                        Digital Timber Marketplace
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-success back-to-top" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>