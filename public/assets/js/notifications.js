const Notifications = {
    // Get base URL
   getBaseUrl: function() {
    return window.location.origin + '/';
},


    // Load unread count
    loadUnreadCount: function() {
        const baseUrl = this.getBaseUrl();
        
        fetch(baseUrl + 'admin/notifications/unread-count')
            .then(r => {
                if (!r.ok) throw new Error('Network response was not ok');
                return r.json();
            })
            .then(data => {
                const count = data.count || 0;
                const badge = document.getElementById('notifCount');
                
                if (badge) {
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'inline-block' : 'none';
                }
                
                // Update sidebar badge if exists
                const sidebarBadge = document.getElementById('pendingCount');
                if (sidebarBadge && count > 0) {
                    sidebarBadge.textContent = count;
                }
            })
            .catch(e => {
                console.error('Error loading notification count:', e);
            });
    },

    // Load recent notifications for dropdown
    loadRecent: function() {
        const baseUrl = this.getBaseUrl();
        const container = document.getElementById('notificationList');
        if (!container) return;
        
        if (!container) {
            console.warn('Notification list container not found');
            return;
        }

        // Show loading
        container.innerHTML = `
            <li class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </li>
        `;

        fetch(baseUrl + 'admin/notifications/recent?limit=5')
            .then(r => {
                if (!r.ok) throw new Error('Network response was not ok');
                return r.json();
            })
            .then(data => {
                const notifications = data.notifications || [];
                
                if (notifications.length === 0) {
                    container.innerHTML = `
                        <li class="text-center py-3">
                            <small class="text-muted">Tidak ada notifikasi</small>
                        </li>
                    `;
                    return;
                }

                let html = '';
                notifications.forEach(notif => {
                    const isUnread = !notif.is_read;
                    const bgClass = isUnread ? 'bg-light' : '';
                    const complaintId = notif.complaint_id || null;
                    
                    html += `
                        <li>
                            <a class="dropdown-item ${bgClass}" 
                               href="#" 
                               onclick="Notifications.handleClick(${notif.id}, ${complaintId}); return false;"
                               style="white-space: normal; padding: 0.75rem 1rem;">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <div class="mb-1">
                                            ${isUnread ? '<span class="badge bg-primary me-1">Baru</span>' : ''}
                                            <strong>${this.escapeHtml(notif.title)}</strong>
                                        </div>
                                        <div class="text-muted small mb-1">${this.escapeHtml(notif.message)}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> ${this.formatTime(notif.created_at)}
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </li>
                    `;
                });
                
                html += `
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-center text-primary" href="${baseUrl}admin/notifications">
                            <i class="fas fa-list"></i> Lihat Semua Notifikasi
                        </a>
                    </li>
                `;
                
                container.innerHTML = html;
            })
            .catch(e => {
                console.error('Error loading notifications:', e);
                if (container) {
                    container.innerHTML = `
                        <li class="text-center py-3">
                            <small class="text-danger">Gagal memuat notifikasi</small>
                        </li>
                    `;
                }
            });
    },

    // Handle notification click
    handleClick: function(notifId, complaintId) {
        const baseUrl = this.getBaseUrl();
        
        // Mark as read
        fetch(baseUrl + 'admin/notifications/' + notifId + '/read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(() => {
            // Redirect to complaint
            if (complaintId) {
                // Get role from body dataset or default to admin
                const role = document.body.dataset?.userRole || 'admin';
                
                if (role === 'admin' || role === 'superadmin') {
                    window.location.href = baseUrl + 'admin/complaints/' + complaintId;
                } else {
                    window.location.href = baseUrl + 'user/complaints/' + complaintId;
                }
            } else {
                // No complaint, go to notification list
                window.location.href = baseUrl + 'admin/notifications';
            }
        }).catch(e => {
            console.error('Error marking notification as read:', e);
            // Still redirect even if mark as read fails
            if (complaintId) {
                window.location.href = baseUrl + 'admin/complaints/' + complaintId;
            }
        });
    },

    // Format time
    formatTime: function(dateString) {
        try {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // seconds

            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit yang lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam yang lalu';
            if (diff < 604800) return Math.floor(diff / 86400) + ' hari yang lalu';
            
            return date.toLocaleDateString('id-ID');
        } catch (e) {
            return 'Unknown';
        }
    },

    // Escape HTML to prevent XSS
    escapeHtml: function(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    // Initialize
    init: function() {
        // Check if elements exist before initializing
        const notifCount = document.getElementById('notifCount');
        const dropdown = document.getElementById('notificationDropdown');
        
        if (notifCount) {
            this.loadUnreadCount();
        }
        
        // Load recent when dropdown is opened
        if (dropdown) {
            dropdown.addEventListener('click', () => {
                this.loadRecent();
            });
        }

        // Auto-refresh every 30 seconds (only if on admin pages)
        if (window.location.pathname.includes('/admin')) {
            setInterval(() => {
                this.loadUnreadCount();
            }, 30000);
        }
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Notifications.init());
} else {
    Notifications.init();
}

// console.log('✅ Notifications.js loaded successfully!');
