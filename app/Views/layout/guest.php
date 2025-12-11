<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sistem Pengaduan') ?></title>
    <link rel="shortcut icon" href="<?= base_url('img/logo-dpmptsp.png') ?>" type="image/x-icon">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2196F3;
            --dark-color: #212529;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .stats-card {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card .number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stats-card .label {
            font-size: 1rem;
            color: #666;
            margin-top: 10px;
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .btn-custom {
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 25px;
        }

        .complaint-card {
            border-left: 4px solid var(--primary-color);
            margin-bottom: 15px;
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 30px 0;
            margin-top: 80px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <!-- LOGO + TULISAN DI SEBELAHNYA -->
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
                <!-- LOGO -->
                <!-- <img src="<?= base_url('img/beraksi2.png') ?>" -->
                    <!-- alt="Logo Laporin" -->
                    <!-- style="height: 40px; width: auto; margin-right: 12px; border-radius: 8px;"> -->
                <span style="font-weight: 700; font-size: 1.4rem; color: #1e40af;">
                    Laporin
                </span>
                <span style="font-size: 0.9rem; color: #64748b; margin-left: 6px;">
                    | Sistem Pengaduan Aplikasi
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">

                    <!-- BERANDA -->
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary btn-sm px-3 me-2" href="<?= base_url('/') ?>">
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>

                    <!-- BASIS PENGETAHUAN -->
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-info btn-sm px-3 me-2" href="<?= base_url('knowledge-base') ?>">
                            <i class="fas fa-book-open me-1"></i> Basis Pengetahuan
                        </a>
                    </li>

                    <?php if (session()->has('user_id')): ?>
                        <!-- DASHBOARD -->
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-success btn-sm px-3 me-2"
                                href="<?= base_url(session()->get('role') . '/dashboard') ?>">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>

                        <!-- LOGOUT -->
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger btn-sm px-3" href="<?= base_url('auth/logout') ?>">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </li>

                    <?php else: ?>
                        <!-- LOGIN -->
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-secondary btn-sm px-3 me-2" href="<?= base_url('auth/login') ?>">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>

                        <!-- REGISTER -->
                        <li class="nav-item">
                            <a class="nav-link btn btn-success btn-sm px-4 shadow-sm" href="<?= base_url('auth/register') ?>">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <?= $this->renderSection('content') ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h5><i class="fas fa-clipboard-list"></i> Laporin</h5>
                    <p>Sistem Pengaduan Aplikasi Internal untuk melaporkan kendala, error, dan bug pada aplikasi DPMPTSP Tanah Bumbu.</p>
                </div>
                <div class="col-md-3">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('/') ?>" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li><a href="<?= base_url('knowledge-base') ?>" class="text-white-50 text-decoration-none">Knowledge Base</a></li>
                        <li><a href="<?= base_url('auth/login') ?>" class="text-white-50 text-decoration-none">Login</a></li>
                    </ul>
                </div> <!-- <div class="col-md-3"> <h5>Kontak</h5> <p class="text-white-50"> <i class="fas fa-envelope"></i> support@example.com<br> <i class="fas fa-phone"></i> (021) 1234-5678 </p> </div> -->
            </div>
            <hr class="bg-white">
            <div class="text-center text-white-50">
                <p>&copy; <?= date('Y') ?> Sistem Pengaduan Aplikasi DPMPTSP - Tanah Bumbu</p>
            </div>
        </div>
    </footer>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>

</html>
