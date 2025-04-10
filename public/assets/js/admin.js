// public/assets/js/admin.js

document.addEventListener('DOMContentLoaded', function () {
    // Toggle sidebar on mobile
    const sidebarToggle = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    if (sidebarToggle && sidebar && content) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        });
    }

    // Auto hide alerts after 5 seconds
    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function (alert) {
            const bsAlert = bootstrap.Alert.getInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            } else {
                alert.classList.add('fade');
                setTimeout(function () {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }
        });
    }, 5000);

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

    // Custom file inputs
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(function (input) {
        input.addEventListener('change', function (e) {
            const fileName = (this.files[0] && this.files[0].name) || 'Chọn file';
            const label = this.nextElementSibling;

            if (label) {
                label.textContent = fileName;
            }
        });
    });

    // Select all checkboxes
    const selectAllCheckboxes = document.querySelectorAll('.select-all-checkbox');
    selectAllCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const target = this.getAttribute('data-target');
            const checkboxes = document.querySelectorAll(target);

            checkboxes.forEach(function (item) {
                item.checked = checkbox.checked;
            });
        });
    });

    // Toggle form sections
    const toggleButtons = document.querySelectorAll('.form-section-toggle');
    toggleButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const target = document.getElementById(targetId);

            if (target) {
                target.classList.toggle('d-none');

                // Toggle icon
                const icon = this.querySelector('i.toggle-icon');
                if (icon) {
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                }
            }
        });
    });

    // Date range picker initialization
    const dateRangePickers = document.querySelectorAll('.date-range-picker');
    if (dateRangePickers.length > 0 && typeof DateRangePicker !== 'undefined') {
        dateRangePickers.forEach(function (picker) {
            new DateRangePicker(picker, {
                startDate: new Date(),
                endDate: new Date(new Date().setDate(new Date().getDate() + 7)),
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });
        });
    }

    // DataTables initialization
    const dataTables = document.querySelectorAll('.datatable');
    if (dataTables.length > 0 && typeof $.fn.DataTable !== 'undefined') {
        dataTables.forEach(function (table) {
            $(table).DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json'
                }
            });
        });
    }

    // CKEditor initialization
    const richTextEditors = document.querySelectorAll('.rich-text-editor');
    if (richTextEditors.length > 0 && typeof ClassicEditor !== 'undefined') {
        richTextEditors.forEach(function (editor) {
            ClassicEditor
                .create(editor)
                .catch(error => {
                    console.error(error);
                });
        });
    }

    // Select2 initialization
    const select2Inputs = document.querySelectorAll('.select2');
    if (select2Inputs.length > 0 && typeof $.fn.select2 !== 'undefined') {
        $(select2Inputs).select2({
            theme: 'bootstrap4'
        });
    }

    // Image preview
    const imageInputs = document.querySelectorAll('.image-input');
    imageInputs.forEach(function (input) {
        input.addEventListener('change', function (e) {
            const previewId = this.getAttribute('data-preview');
            const preview = document.getElementById(previewId);

            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }

                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });

    // Currency formatter
    const currencyInputs = document.querySelectorAll('.currency-input');
    currencyInputs.forEach(function (input) {
        input.addEventListener('input', function (e) {
            // Remove non-digit characters
            const value = this.value.replace(/\D/g, '');

            // Format with thousand separator
            if (value.length > 0) {
                this.value = parseInt(value).toLocaleString('vi-VN');
            }
        });

        // Initial formatting
        if (input.value) {
            const value = input.value.replace(/\D/g, '');
            if (value.length > 0) {
                input.value = parseInt(value).toLocaleString('vi-VN');
            }
        }
    });

    // Confirm delete
    const confirmDeleteButtons = document.querySelectorAll('[data-confirm-delete]');
    confirmDeleteButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
            const message = this.getAttribute('data-confirm-message') || 'Bạn có chắc chắn muốn xóa mục này?';

            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Format time for display
function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Format currency for display
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Extract value from formatted currency
function extractCurrencyValue(formattedValue) {
    return parseInt(formattedValue.replace(/\D/g, '')) || 0;
}

// Show loading overlay
function showLoading() {
    let loadingOverlay = document.getElementById('loading-overlay');

    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(loadingOverlay);
    }

    loadingOverlay.style.display = 'flex';
}

// Hide loading overlay
function hideLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        // Create toast container if not exists
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1050';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    document.getElementById('toast-container').appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, {
        delay: 3000
    });

    bsToast.show();
}

// Generate random string
function generateRandomString(length = 8) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';

    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    return result;
}