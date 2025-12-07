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
            padding: 30px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-container {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .register-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .register-header {
            background: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
            text-align: center;
        }
        
        .register-header i {
            font-size: 4rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        
        .register-body {
            background: white;
            padding: 30px;
            border-radius: 0 0 15px 15px;
        }
        
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        
        .btn-register {
            background: #4CAF50;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
        }
        
        .btn-register:hover {
            background: #45a049;
        }
        
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <!-- Logo/Header -->
            <div class="register-card">
                <div class="register-header">
                    <i class="fas fa-user-plus"></i>
                    <h3 class="mb-0">Daftar Akun</h3>
                    <p class="text-muted mb-0">Buat akun baru untuk melaporkan pengaduan</p>
                </div>
                
                <div class="register-body">
                    <!-- Alert Messages -->
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
                    
                    <!-- Register Form -->
                    <form action="<?= base_url('auth/register') ?>" method="post" id="registerForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Username
                            </label>
                            <input type="text" name="username" class="form-control" 
                                   placeholder="Pilih username" value="<?= old('username') ?>" required autofocus>
                            <small class="text-muted">Username akan digunakan untuk login</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="Masukkan email" value="<?= old('email') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-id-card"></i> Nama Lengkap
                            </label>
                            <input type="text" name="full_name" class="form-control" 
                                   placeholder="Masukkan nama lengkap" value="<?= old('full_name') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Minimal 6 karakter" id="password" required>
                            <div class="password-strength bg-secondary" id="strengthBar"></div>
                            <small class="text-muted" id="strengthText">Kekuatan password</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Konfirmasi Password
                            </label>
                            <input type="password" name="password_confirm" class="form-control" 
                                   placeholder="Ulangi password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-register w-100 mb-3">
                            <i class="fas fa-user-plus"></i> Daftar Sekarang
                        </button>
                        
                        <div class="text-center">
                            <p class="mb-2">Sudah punya akun?</p>
                            <a href="<?= base_url('auth/login') ?>" class="btn btn-outline-success">
                                <i class="fas fa-sign-in-alt"></i> Login
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
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Password Strength Indicator -->
    <script>
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    strengthBar.className = 'password-strength bg-danger';
                    strengthBar.style.width = '25%';
                    strengthText.textContent = 'Password lemah';
                    strengthText.className = 'text-danger';
                    break;
                case 2:
                    strengthBar.className = 'password-strength bg-warning';
                    strengthBar.style.width = '50%';
                    strengthText.textContent = 'Password sedang';
                    strengthText.className = 'text-warning';
                    break;
                case 3:
                case 4:
                    strengthBar.className = 'password-strength bg-info';
                    strengthBar.style.width = '75%';
                    strengthText.textContent = 'Password kuat';
                    strengthText.className = 'text-info';
                    break;
                case 5:
                    strengthBar.className = 'password-strength bg-success';
                    strengthBar.style.width = '100%';
                    strengthText.textContent = 'Password sangat kuat';
                    strengthText.className = 'text-success';
                    break;
            }
        });
    </script>
</body>
</html>