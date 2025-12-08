<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard Admin') ?> - Sistem Pengaduan</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #2196F3;
            --admin-color: #1976D2;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2196F3 0%, #1976D2 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .sidebar-header h4 {
            margin: 0;
            font-size: 1.3rem;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 25px;
        }

        .sidebar-menu a i {
            width: 25px;
            margin-right: 10px;
        }

        .sidebar-menu .badge {
            margin-left: auto;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Content Area */
        .content-area {
            padding: 30px;
        }

        /* Card Custom */
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 15px 20px;
            font-weight: 600;
        }

        /* Stat Card */
        .stat-card {
            padding: 20px;
            border-radius: 10px;
            color: white;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Notification Badge */
        .notification-badge {
            position: relative;
        }

        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                left: calc(var(--sidebar-width) * -1);
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block !important;
            }
        }

        .mobile-menu-btn {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <h4>Sistem Pengaduan</h4>
            <small>Admin Panel</small>
        </div>

        <div class="sidebar-menu">
            <?php $role = strtolower((string) session()->get('role')); ?>


            <!-- Dashboard: route depends on role -->
            <?php if ($role === 'superadmin'): ?>
                <a href="<?= base_url('superadmin/dashboard') ?>" class="<?= uri_string() == 'superadmin/dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            <?php elseif ($role === 'admin'): ?>
                <a href="<?= base_url('admin/dashboard') ?>" class="<?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            <?php else: ?>
                <a href="<?= base_url('user/dashboard') ?>" class="<?= uri_string() == 'user/dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            <?php endif; ?>

            <!-- Admin area (accessible by admin + superadmin) -->
            <?php if (in_array($role, ['admin', 'superadmin'])): ?>
                <a href="<?= base_url('admin/complaints') ?>" class="<?= strpos(uri_string(), 'admin/complaints') !== false ? 'active' : '' ?>">
                    <i class="fas fa-list"></i>
                    <span>Kelola Laporan</span>
                    <span class="badge bg-danger" id="pendingCount">0</span>
                </a>
                <a href="<?= base_url('admin/analytics') ?>" class="<?= strpos(uri_string(), 'admin/analytics') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>
                <a href="<?= base_url('admin/reports') ?>" class="<?= strpos(uri_string(), 'admin/reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-contract"></i>
                    <span>Custom Reports</span>
                </a>
                <a href="<?= base_url('admin/knowledge-base') ?>" class="<?= strpos(uri_string(), 'admin/knowledge-base') !== false ? 'active' : '' ?>">
                    <i class="fas fa-book"></i>
                    <span>Knowledge Base</span>
                </a>
            <?php endif; ?>

            <!-- Superadmin area (only visible to superadmin) -->
            <?php if ($role === 'superadmin'): ?>
                <hr style="border-color: rgba(255,255,255,0.2); margin: 20px;">
                <a href="<?= base_url('superadmin/users') ?>" class="<?= strpos(uri_string(), 'superadmin/users') !== false ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                <a href="<?= base_url('superadmin/applications') ?>" class="<?= strpos(uri_string(), 'superadmin/applications') !== false ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Applications</span>
                </a>
                <a href="<?= base_url('superadmin/categories') ?>" class="<?= strpos(uri_string(), 'superadmin/categories') !== false ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="<?= base_url('superadmin/analytics') ?>" class="<?= strpos(uri_string(), 'superadmin/analytics') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>System Analytics</span>
                </a>
            <?php endif; ?>

            <hr style="border-color: rgba(255,255,255,0.2); margin: 20px;">

            <a href="<?= base_url('/') ?>">
                <i class="fas fa-globe"></i>
                <span>Beranda</span>
            </a>
            <a href="<?= base_url('auth/logout') ?>">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="btn btn-primary mobile-menu-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="d-inline-block mb-0 ms-2"><?= esc($page_title ?? 'Dashboard') ?></h5>

                <form action="<?= base_url('search') ?>" method="get" class="ms-1 d-none d-md-flex" style="align-items:center">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Search..." style="width:300px">
                    <button class="btn btn-sm btn-outline-secondary ms-2" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="topbar-user">
                <!-- Notifications -->
                <div class="dropdown">
                    <a href="#" class="text-dark notification-badge" id="notifBadge" data-bs-toggle="dropdown" role="button">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="badge bg-danger" id="notifCount">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" id="notifDropdown" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                        <li>
                            <h6 class="dropdown-header">Notifikasi</h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li id="notifList" style="min-height: 100px;">
                            <p class="dropdown-item-text text-muted text-center py-3">Memuat...</p>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center" href="<?= base_url('admin/notifications') ?>">Lihat Semua</a></li>
                        <li><a class="dropdown-item text-center text-secondary" href="#" onclick="markAllAsReadNotif(event)">Tandai Semua</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <?= strtoupper(substr(session()->get('full_name'), 0, 2)) ?>
                        </div>
                        <div class="ms-2 d-none d-md-block">
                            <strong><?= esc(session()->get('full_name')) ?></strong><br>
                            <small class="text-muted">
                                <?= session()->get('role') == 'superadmin' ? 'Superadmin' : 'Admin' ?>
                            </small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= session()->getFlashdata('warning') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Main Content -->
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Load notification count and list
        function loadNotifications() {
            fetch('<?= base_url('admin/notifications/api/count') ?>')
                .then(r => r.json())
                .then(data => {
                    const countEl = document.getElementById('notifCount');
                    countEl.textContent = data.count || '0';
                    if (data.count > 0) {
                        countEl.classList.remove('d-none');
                    } else {
                        countEl.classList.add('d-none');
                    }
                });

            fetch('<?= base_url('admin/notifications/api/recent') ?>?limit=5')
                .then(r => r.json())
                .then(data => {
                    const listEl = document.getElementById('notifList');
                    if (!data.notifications || data.notifications.length === 0) {
                        listEl.innerHTML = '<p class="dropdown-item-text text-muted text-center py-3">Tidak ada notifikasi</p>';
                        return;
                    }
                    listEl.innerHTML = data.notifications.map(n => `
                        <div class="dropdown-item-text px-3 py-2 border-bottom">
                            <div class="d-flex justify-content-between">
                                <strong>${n.title}</strong>
                                ${n.is_read ? '' : '<span class="badge bg-primary">Baru</span>'}
                            </div>
                            <small class="text-muted">${n.message || ''}</small>
                            <div class="small text-muted mt-1">${new Date(n.created_at).toLocaleString('id-ID')}</div>
                        </div>
                    `).join('');
                });
        }

        function markAllAsReadNotif(e) {
            e.preventDefault();
            fetch('<?= base_url('admin/notifications/read-all') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        loadNotifications();
                    }
                });
        }

        // Load pending count
        document.getElementById('pendingCount').textContent = '0';

        // Load notifications on page load and refresh every 30 seconds
        loadNotifications();
        setInterval(loadNotifications, 30000);
    </script>
    <script>
        // Attach simple suggestions widget to any search input present
        (function() {
            const input = document.querySelector('input[name="q"]');
            if (!input) return;
            let timer = null;
            const box = document.createElement('div');
            box.className = 'list-group position-absolute';
            box.style.zIndex = 2000;
            box.style.width = (input.offsetWidth || 300) + 'px';
            input.parentNode.style.position = 'relative';
            input.parentNode.appendChild(box);

            input.addEventListener('input', function() {
                const v = this.value.trim();
                if (timer) clearTimeout(timer);
                if (!v) {
                    box.innerHTML = '';
                    return;
                }
                timer = setTimeout(() => {
                    fetch('<?= base_url('search/suggestions') ?>?q=' + encodeURIComponent(v))
                        .then(r => r.json())
                        .then(data => {
                            box.innerHTML = '';
                            (data.kb || []).slice(0, 3).forEach(it => {
                                const a = document.createElement('a');
                                a.href = it.url;
                                a.className = 'list-group-item list-group-item-action';
                                a.textContent = 'KB: ' + it.title;
                                box.appendChild(a);
                            });
                            (data.complaints || []).slice(0, 3).forEach(it => {
                                const a = document.createElement('a');
                                a.href = it.url;
                                a.className = 'list-group-item list-group-item-action';
                                a.textContent = 'CMP: ' + it.title;
                                box.appendChild(a);
                            });
                            (data.users || []).slice(0, 3).forEach(it => {
                                const a = document.createElement('a');
                                a.href = it.url;
                                a.className = 'list-group-item list-group-item-action';
                                a.textContent = 'USR: ' + it.name;
                                box.appendChild(a);
                            });
                        });
                }, 200);
            });
        })();
    </script>

    <?= $this->renderSection('scripts') ?>
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>

</html>