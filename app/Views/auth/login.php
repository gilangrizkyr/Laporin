<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #4CAF50 0%, #2196F3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 450px;
            margin: 0 auto;
        }

        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .login-header {
            background: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
            text-align: center;
        }

        .login-header i {
            font-size: 4rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .login-body {
            background: white;
            padding: 30px;
            border-radius: 0 0 15px 15px;
        }

        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .btn-login {
            background: #4CAF50;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
        }

        .btn-login:hover {
            background: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <!-- Logo/Header -->
            <div class="login-card">
                <div class="login-header">
                    <i class="fas fa-clipboard-check"></i>
                    <h3 class="mb-0">Login</h3>
                    <p class="text-muted mb-0">Sistem Pengaduan Aplikasi Internal</p>
                </div>

                <div class="login-body">
                    <!-- Alert Messages -->
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

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="<?= base_url('auth/login') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" name="email" class="form-control form-control-lg"
                                placeholder="Masukkan email" value="<?= old('email') ?>" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" name="password" class="form-control form-control-lg"
                                placeholder="Masukkan password" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Ingat saya
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success btn-login w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>

                        <div class="text-center">
                            <p class="mb-2">Belum punya akun?</p>
                            <a href="<?= base_url('auth/register') ?>" class="btn btn-outline-success">
                                <i class="fas fa-user-plus"></i> Daftar Sekarang
                            </a>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="<?= base_url('/') ?>" class="text-decoration-none">
                                <i class="fas fa-home"></i> Kembali ke Beranda
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Demo Credentials -->
            <div class="card mt-3 bg-light">
                <div class="card-body">
                    <h6 class="text-center mb-3">Demo Akun:</h6>
                    <small class="d-block mb-1"><strong>Superadmin:</strong> superadmin@example.com / superadmin123</small>
                    <small class="d-block mb-1"><strong>Admin:</strong> admin@example.com / admin123</small>
                    <small class="d-block"><strong>User:</strong> user1@example.com / user123</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>