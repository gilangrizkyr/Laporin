<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard User') ?> - Sistem Pengaduan</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #4CAF50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4CAF50 0%, #388E3C 100%);
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

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

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

        .content-area {
            padding: 30px;
        }

        .notification-badge {
            position: relative;
        }

        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
        }

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
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <h4>Sistem Pengaduan</h4>
            <small>User Panel</small>
        </div>

        <div class="sidebar-menu">
            <a href="<?= base_url('user/dashboard') ?>" class="<?= uri_string() == 'user/dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('user/complaints') ?>" class="<?= strpos(uri_string(), 'user/complaints') !== false ? 'active' : '' ?>">
                <i class="fas fa-list"></i>
                <span>Daftar Laporan</span>
            </a>
            <a href="<?= base_url('user/complaints/create') ?>">
                <i class="fas fa-plus-circle"></i>
                <span>Buat Laporan Baru</span>
            </a>
            <a href="<?= base_url('knowledge-base') ?>">
                <i class="fas fa-book"></i>
                <span>Knowledge Base</span>
            </a>

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

    <div class="main-content">
        <div class="topbar">
            <div>
                <button class="btn btn-success mobile-menu-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="d-inline-block mb-0 ms-2"><?= esc($page_title ?? 'Dashboard') ?></h5>
            </div>

            <div class="topbar-user">
                <div class="dropdown">
                    <a href="#" class="text-dark notification-badge" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="badge bg-danger" id="notifCount">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                        <li>
                            <h6 class="dropdown-header">Notifikasi</h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <div id="notificationList">
                            <li class="text-center py-3">
                                <small class="text-muted">Loading...</small>
                            </li>
                        </div>
                        <li><a class="dropdown-item text-center" href="<?= base_url('user/notifications') ?>">Lihat Semua</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <?= strtoupper(substr(session()->get('full_name'), 0, 2)) ?>
                        </div>
                        <div class="ms-2 d-none d-md-block">
                            <strong><?= esc(session()->get('full_name')) ?></strong><br>
                            <small class="text-muted">User</small>
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

        <div class="content-area">
            <!-- Flash Messages -->
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        function loadUserNotifications() {
            fetch('<?= base_url('user/notifications/unread-count') ?>')
                .then(response => response.json())
                .then(data => {
                    const countEl = document.getElementById('notifCount');
                    countEl.textContent = data.count || '0';
                    if (data.count > 0) {
                        countEl.classList.remove('d-none');
                    } else {
                        countEl.classList.add('d-none');
                    }
                });

            fetch('<?= base_url('user/notifications/recent') ?>')
                .then(response => response.json())
                .then(data => {
                    const listEl = document.getElementById('notificationList');
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

        loadUserNotifications();
        setInterval(loadUserNotifications, 30000);
    </script>
</body>

</html>
