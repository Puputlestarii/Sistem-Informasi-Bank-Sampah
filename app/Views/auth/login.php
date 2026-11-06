<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MasJOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            font-family: 'Segoe UI', Roboto, sans-serif;
        }
        .login-card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
            width: 100%; 
            max-width: 420px; 
            background: white;
            overflow: hidden;
        }
        .login-header {
            background-color: #ffffff;
            padding: 30px 20px 10px 20px;
            text-align: center;
        }
        .logo-img {
            max-width: 90px;
            margin-bottom: 15px;
            transition: transform 0.3s;
        }
        .logo-img:hover { transform: scale(1.05); }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #eee;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.1);
            border-color: #1abc9c;
        }
        .btn-masuk { 
            background: linear-gradient(to right, #1abc9c, #16a085);
            color: white; 
            border: none; 
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-masuk:hover { 
            background: linear-gradient(to right, #16a085, #148f77);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 188, 156, 0.3);
        }
        .input-group-text {
            background-color: transparent;
            border-left: none;
            cursor: pointer;
            color: #95a5a6;
        }
        .pass-input { border-right: none; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card login-card shadow-lg">
        <div class="login-header">
            <h5 class="fw-bold text-uppercase text-secondary mb-3" style="letter-spacing: 1px; font-size: 0.9rem;">
                Sistem Informasi Bank Sampah
            </h5>
            <img src="<?= base_url('images/logo_pemkot_yk.png') ?>" alt="Logo MasJOS" class="logo-img">
            <p class="text-muted small">Masyarakat Jogja Olah Sampah</p>
        </div>

        <div class="card-body px-4 pb-4">
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show small py-2" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" style="font-size: 0.5rem;"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/login') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="Masukkan username" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control bg-light border-start-0 pass-input" placeholder="Password" required>
                        <span class="input-group-text bg-light" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-masuk shadow-sm">
                        MASUK KE SISTEM <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>

            <div class="position-relative my-4">
                <hr class="text-muted">
                <span class="position-absolute top-50 start-50 translate-middle bg-white px-2 small text-muted">ATAU</span>
            </div>

            <div class="text-center">
                <p class="small text-muted mb-2">Belum jadi nasabah? Mari beraksi!</p>
                <a href="<?= base_url('register') ?>" class="btn btn-outline-success btn-sm w-100 rounded-pill fw-bold">
                    <i class="fas fa-user-plus me-1"></i> Daftar Akun Nasabah
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#togglePassword').on('click', function() {
            const passInput = $('#password');
            const icon = $(this).find('i');
            
            if (passInput.attr('type') === 'password') {
                passInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        <?php if(session()->getFlashdata('error')): ?>
            Swal.fire({ 
                icon: 'error', 
                title: 'Login Gagal', 
                text: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#1abc9c'
            });
        <?php endif; ?>
    });
</script>
</body>
</html>