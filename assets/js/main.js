// WOOD CONNECT - Main JavaScript
class TimberConnect {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupServiceWorker();
        this.fixNavbarToggle();
    }

    // Fix for navbar toggle issues
    fixNavbarToggle() {
        // Let Bootstrap handle the navbar toggle natively
        // Remove any custom click handlers that might interfere
        const navbarToggler = document.querySelector('.navbar-toggler');
        if (navbarToggler) {
            // Remove any existing custom event listeners
            navbarToggler.replaceWith(navbarToggler.cloneNode(true));

            // Add smooth close behavior
            document.addEventListener('click', (e) => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                const isNavbarToggle = e.target.closest('.navbar-toggler');
                const isNavbarLink = e.target.closest('.navbar-nav a');

                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    if (isNavbarLink || (!isNavbarToggle && !e.target.closest('.navbar-collapse'))) {
                        // Close navbar when clicking on a link or outside
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: false
                        });
                        bsCollapse.hide();
                    }
                }
            });
        }
    }

    setupEventListeners() {
        // Search functionality
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', this.handleSearch.bind(this));
        }

        // Form validation
        this.setupFormValidation();

        // Image lazy loading
        this.setupLazyLoading();

        // Smooth scrolling for anchor links
        this.setupSmoothScrolling();
    }

    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('SW registered: ', registration);
                })
                .catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
        }
    }

    setupFormValidation() {
        // Nigerian phone validation
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('blur', this.validateNigerianPhone.bind(this));
        });

        // Price formatting
        const priceInputs = document.querySelectorAll('input[data-price]');
        priceInputs.forEach(input => {
            input.addEventListener('input', this.formatPriceInput.bind(this));
        });

        // Form submission handling
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        });
    }

    validateNigerianPhone(e) {
        const phone = e.target.value.trim();
        const nigerianRegex = /^(0|\+234)[7-9][0-1]\d{8}$/;

        if (phone && !nigerianRegex.test(phone)) {
            e.target.classList.add('is-invalid');
            this.showValidationError(e.target, 'Please enter a valid Nigerian phone number');
        } else {
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        }
    }

    formatPriceInput(e) {
        let value = e.target.value.replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('en-NG');
            e.target.value = value;
        }
    }

    handleFormSubmit(e) {
        const form = e.target;
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            this.showNotification('Please fill in all required fields', 'error');
        }
    }

    showValidationError(input, message) {
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

    handleSearch(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const searchParams = new URLSearchParams(formData);

        window.location.href = `/marketplace/search.php?${searchParams.toString()}`;
    }

    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const lazyImageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        lazyImage.classList.remove('lazy');
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            const lazyImages = document.querySelectorAll('img.lazy');
            lazyImages.forEach(lazyImage => {
                lazyImageObserver.observe(lazyImage);
            });
        }
    }

    setupSmoothScrolling() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href');

                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);

                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }

    // AJAX helper methods
    async apiCall(endpoint, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            },
        };

        const mergedOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(`/api/${endpoint}`, mergedOptions);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API call failed:', error);
            this.showNotification('An error occurred. Please try again.', 'error');
            throw error;
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.alert-dismissible');
        existingNotifications.forEach(notification => {
            notification.remove();
        });

        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Image handling
    compressImage(file, maxSize = 200000) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            const img = new Image();
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            reader.onload = function (e) {
                img.onload = function () {
                    // Calculate new dimensions
                    let width = img.width;
                    let height = img.height;
                    const maxWidth = 1200;
                    const maxHeight = 800;

                    if (width > maxWidth || height > maxHeight) {
                        const ratio = width / height;

                        if (ratio > 1) {
                            width = maxWidth;
                            height = maxWidth / ratio;
                        } else {
                            height = maxHeight;
                            width = maxHeight * ratio;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;

                    // Draw and compress
                    ctx.drawImage(img, 0, 0, width, height);

                    let quality = 0.8;
                    let compressedDataUrl;

                    do {
                        compressedDataUrl = canvas.toDataURL('image/jpeg', quality);
                        quality -= 0.1;
                    } while (compressedDataUrl.length > maxSize && quality > 0.1);

                    resolve(compressedDataUrl);
                };
                img.src = e.target.result;
            };

            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    // Utility methods
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    formatNaira(amount) {
        return '₦' + parseInt(amount).toLocaleString('en-NG');
    }

    getQueryParams() {
        const params = new URLSearchParams(window.location.search);
        const result = {};
        for (const [key, value] of params) {
            result[key] = value;
        }
        return result;
    }

    updateQueryParams(params) {
        const url = new URL(window.location);
        Object.keys(params).forEach(key => {
            if (params[key]) {
                url.searchParams.set(key, params[key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        window.history.replaceState({}, '', url);
    }
}

// Initialize application when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    window.timberConnect = new TimberConnect();
});

// Utility functions available globally
const utils = {
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    formatNaira(amount) {
        return '₦' + parseInt(amount).toLocaleString('en-NG');
    },

    getQueryParams() {
        const params = new URLSearchParams(window.location.search);
        const result = {};
        for (const [key, value] of params) {
            result[key] = value;
        }
        return result;
    },

    updateQueryParams(params) {
        const url = new URL(window.location);
        Object.keys(params).forEach(key => {
            if (params[key]) {
                url.searchParams.set(key, params[key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        window.history.replaceState({}, '', url);
    },

    // Mobile menu helper
    initMobileMenu() {
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');

        if (navbarToggler && navbarCollapse) {
            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                const isClickInsideNavbar = navbarCollapse.contains(e.target) || navbarToggler.contains(e.target);

                if (!isClickInsideNavbar && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });

            // Close menu when window is resized to desktop size
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992 && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });
        }
    }
};

// Initialize mobile menu when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    utils.initMobileMenu();
});

// Add CSS for smooth transitions
const style = document.createElement('style');
style.textContent = `
    .navbar-collapse {
        transition: all 0.3s ease-in-out;
    }
    
    .alert.position-fixed {
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    img.lazy {
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    
    img.lazy.loaded {
        opacity: 1;
    }
`;
document.head.appendChild(style);