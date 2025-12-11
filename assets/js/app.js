// ========== GLOBAL APP OBJECT ==========
const App = {
    // Toast notification
    toast: function(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type} animate-slide-down`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutUp 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    },

    createToastContainer: function() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    },

    // Loading overlay
    showLoading: function() {
        let overlay = document.getElementById('loadingOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="spinner-border text-light" style="width: 3rem; height: 3rem;"></div>
            `;
            document.body.appendChild(overlay);
        }
        setTimeout(() => overlay.classList.add('show'), 10);
    },

    hideLoading: function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('show');
        }
    },

    // Confirm dialog
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },

    // Copy to clipboard
    copyToClipboard: function(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.toast('Text copied to clipboard!', 'success');
        }).catch(() => {
            this.toast('Failed to copy text', 'error');
        });
    },

    // Format date
    formatDate: function(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return date.toLocaleDateString('id-ID', options);
    },

    // Format file size
    formatFileSize: function(bytes) {
        if (bytes >= 1073741824) {
            return (bytes / 1073741824).toFixed(2) + ' GB';
        } else if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        } else {
            return bytes + ' bytes';
        }
    }
};

// ========== AUTO-DISMISS ALERTS ==========
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'fadeOut 0.5s ease-out';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// ========== FORM VALIDATION ENHANCEMENT ==========
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                App.toast('Please fill all required fields', 'error');
            }
        });
    });
});

// ========== TEXTAREA AUTO-RESIZE ==========
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea.auto-resize');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});

// ========== IMAGE PREVIEW ==========
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(previewId).style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ========== CONFIRM DELETE ==========
function confirmDelete(id, url, message = 'Are you sure you want to delete this item?') {
    if (confirm(message)) {
        App.showLoading();
        window.location.href = url;
    }
}

// ========== BACK TO TOP BUTTON ==========
document.addEventListener('DOMContentLoaded', function() {
    const backToTop = document.createElement('button');
    backToTop.id = 'backToTop';
    backToTop.className = 'back-to-top';
    backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTop.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        border: none;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    `;
    document.body.appendChild(backToTop);

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.style.opacity = '1';
            backToTop.style.visibility = 'visible';
        } else {
            backToTop.style.opacity = '0';
            backToTop.style.visibility = 'hidden';
        }
    });

    backToTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

// ========== ADDITIONAL TOAST STYLES ==========
const toastStyles = `
<style>
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.toast-notification {
    min-width: 300px;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.toast-success {
    border-left: 4px solid #28a745;
}

.toast-error {
    border-left: 4px solid #dc3545;
}

.toast-info {
    border-left: 4px solid #17a2b8;
}

.toast-icon i {
    font-size: 1.5rem;
}

.toast-success .toast-icon {
    color: #28a745;
}

.toast-error .toast-icon {
    color: #dc3545;
}

.toast-info .toast-icon {
    color: #17a2b8;
}

.toast-message {
    flex: 1;
    font-weight: 500;
}

.toast-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #999;
    font-size: 1.2rem;
    padding: 0;
    width: 24px;
    height: 24px;
}

.toast-close:hover {
    color: #333;
}

@keyframes slideOutUp {
    to {
        opacity: 0;
        transform: translateY(-30px);
    }
}

@keyframes fadeOut {
    to {
        opacity: 0;
        transform: scale(0.9);
    }
}
</style>
`;

if (!document.getElementById('toastStyles')) {
    const styleTag = document.createElement('div');
    styleTag.id = 'toastStyles';
    styleTag.innerHTML = toastStyles;
    document.head.appendChild(styleTag);
}

// ========== USAGE EXAMPLES ==========

/*
// Show toast notification
App.toast('Data saved successfully!', 'success');
App.toast('An error occurred', 'error');
App.toast('Please note this information', 'info');

// Show loading
App.showLoading();
// Hide loading after operation
App.hideLoading();

// Confirm action
App.confirm('Delete this item?', function() {
    // Do delete action
});

// Copy to clipboard
App.copyToClipboard('Text to copy');

// Format date
const formatted = App.formatDate('2024-01-01 10:30:00');

// Format file size
const size = App.formatFileSize(1048576); // Returns "1.00 MB"
*/

// console.log('✅ App.js loaded successfully!');