// public/assets/js/main.js

document.addEventListener('DOMContentLoaded', function () {
    // Flash messages auto dismiss
    setTimeout(function () {
        const flashMessages = document.querySelectorAll('.alert.flash-message');
        flashMessages.forEach(function (message) {
            const alert = new bootstrap.Alert(message);
            alert.close();
        });
    }, 5000);

    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target') || this.previousElementSibling.id;
            const passwordInput = document.getElementById(targetId);

            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    // Scroll to top button
    const scrollToTopBtn = document.getElementById('scrollToTop');
    if (scrollToTopBtn) {
        // Show button when user scrolls down 300px
        window.addEventListener('scroll', function () {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        // Scroll to top when button clicked
        scrollToTopBtn.addEventListener('click', function () {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        });
    }

    // Movie cards hover effect
    const movieCards = document.querySelectorAll('.movie-card');
    movieCards.forEach(function (card) {
        card.addEventListener('mouseenter', function () {
            this.classList.add('card-hover-active');
        });

        card.addEventListener('mouseleave', function () {
            this.classList.remove('card-hover-active');
        });
    });

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-toggle="tooltip"]');
    tooltips.forEach(function (tooltip) {
        new bootstrap.Tooltip(tooltip);
    });

    // Initialize popovers
    const popovers = document.querySelectorAll('[data-toggle="popover"]');
    popovers.forEach(function (popover) {
        new bootstrap.Popover(popover);
    });

    // Lazy load images
    const lazyImages = document.querySelectorAll('img.lazy');
    if ('IntersectionObserver' in window) {
        const lazyImageObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
                    if (lazyImage.dataset.srcset) {
                        lazyImage.srcset = lazyImage.dataset.srcset;
                    }
                    lazyImage.classList.remove('lazy');
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });

        lazyImages.forEach(function (lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    } else {
        // Fallback for browsers without IntersectionObserver support
        let lazyLoadThrottleTimeout;

        function lazyLoad() {
            if (lazyLoadThrottleTimeout) {
                clearTimeout(lazyLoadThrottleTimeout);
            }

            lazyLoadThrottleTimeout = setTimeout(function () {
                const scrollTop = window.pageYOffset;
                lazyImages.forEach(function (lazyImage) {
                    if (lazyImage.offsetTop < (window.innerHeight + scrollTop)) {
                        lazyImage.src = lazyImage.dataset.src;
                        if (lazyImage.dataset.srcset) {
                            lazyImage.srcset = lazyImage.dataset.srcset;
                        }
                        lazyImage.classList.remove('lazy');
                    }
                });

                if (lazyImages.length === 0) {
                    document.removeEventListener('scroll', lazyLoad);
                    window.removeEventListener('resize', lazyLoad);
                    window.removeEventListener('orientationChange', lazyLoad);
                }
            }, 20);
        }

        document.addEventListener('scroll', lazyLoad);
        window.addEventListener('resize', lazyLoad);
        window.addEventListener('orientationChange', lazyLoad);
    }

    // Format currency inputs
    const currencyInputs = document.querySelectorAll('.currency-input');
    currencyInputs.forEach(function (input) {
        input.addEventListener('input', function (e) {
            // Remove non-digit characters
            let value = this.value.replace(/\D/g, '');

            // Format with thousand separator
            if (value.length > 0) {
                value = parseInt(value).toLocaleString('vi-VN');
            }

            // Update input value
            this.value = value;
        });
    });

    // Handle mobile menu
    const mobileMenuToggle = document.querySelector('.navbar-toggler');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function () {
            document.body.classList.toggle('mobile-menu-open');
        });
    }
});

// Helper function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

// Helper function to format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('vi-VN', options);
}

// Helper function to format time
function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = Math.floor(seconds % 60);

    return [
        hours > 0 ? String(hours).padStart(2, '0') : null,
        String(minutes).padStart(2, '0'),
        String(secs).padStart(2, '0')
    ].filter(Boolean).join(':');
}

// Helper function to parse time string to seconds
function parseTimeToSeconds(timeString) {
    const parts = timeString.split(':');
    let seconds = 0;

    if (parts.length === 3) {
        // HH:MM:SS
        seconds = parseInt(parts[0]) * 3600 + parseInt(parts[1]) * 60 + parseInt(parts[2]);
    } else if (parts.length === 2) {
        // MM:SS
        seconds = parseInt(parts[0]) * 60 + parseInt(parts[1]);
    } else if (parts.length === 1) {
        // SS
        seconds = parseInt(parts[0]);
    }

    return seconds;
}

// Show toast notification
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        // Create toast container if not exists
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }

    const id = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = id;
    toast.className = `toast fade-in bg-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body text-white">
            ${message}
        </div>
    `;

    document.getElementById('toast-container').appendChild(toast);

    // Initialize Bootstrap toast
    const bsToast = new bootstrap.Toast(toast, {
        delay: 5000
    });

    bsToast.show();

    // Remove from DOM after hiding
    toast.addEventListener('hidden.bs.toast', function () {
        this.remove();
    });
}