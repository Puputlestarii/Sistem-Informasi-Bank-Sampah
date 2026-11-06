<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($title)) ? $title : 'MasJOS - Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        
        /* Sidebar Styling */
        #sidebar { 
            min-height: 100vh; 
            width: 250px; 
            background: #2c3e50; 
            color: white; 
            transition: all 0.3s; 
            position: fixed; 
            z-index: 1000; 
        }
        #sidebar.active { margin-left: -250px; }
        
        /* Content Styling */
        #content { 
            width: calc(100% - 250px); 
            left: 250px; 
            position: relative; 
            transition: all 0.3s; 
            min-height: 100vh;
        }
        #content.active { width: 100%; left: 0; }
        
        /* Navigation Links */
        .nav-link { color: #bdc3c7; transition: all 0.3s; padding: 12px 15px; border-radius: 5px; margin-bottom: 5px; }
        .nav-link:hover { color: white; background: #34495e; }
        .nav-link.active { color: white !important; background: #1abc9c !important; font-weight: bold; }
        .sub-menu .nav-link { font-size: 0.9em; padding-left: 40px; }
        
        /* Custom Elements */
        #sidebarCollapse { background: #1abc9c; border: none; color: white; padding: 8px 15px; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        #sidebarCollapse:hover { background: #16a085; }
        
        .dropdown-toggle::after { display: none; }
        .dropdown-menu { animation: fadeIn 0.3s ease; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .table { background: white; border-radius: 12px; overflow: hidden; }
        .table-bordered th, .table-bordered td { border: 1px solid #dee2e6 !important; }
        
        .user-avatar {
            width: 40px; height: 40px;
            background: #1abc9c;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

    <nav id="sidebar" class="p-3 shadow">
        <div class="d-flex align-items-center justify-content-center mb-4 mt-2 border-bottom pb-3">
            <img src="<?= base_url('images/logo_pemkot_yk.png') ?>" alt="Logo Pemkot YK" class="img-fluid me-3" style="max-height: 40px; width: auto;">
            <h4 class="mb-0 text-white" style="font-weight: 800; letter-spacing: 1px; font-size: 1.4rem;">Mas<b>JOS</b></h4>
        </div>
        <ul class="nav flex-column">

            <!-- ROLE A: ADMIN UTAMA KEMANTREN -->
            <?php if (session()->get('logged_in') && session()->get('role') === 'admin_petugas' && session()->get('jabatan') === 'Admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('admin/dashboard*')) ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                        <i class="fas fa-th-large me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuManajemen">
                        <span><i class="fas fa-users me-2"></i> Manajemen</span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?= (url_is('admin/pengguna*') || url_is('admin/nasabah*')) ? 'show' : '' ?>" id="menuManajemen">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"><a class="nav-link <?= (url_is('admin/pengguna*')) ? 'active' : '' ?>" href="<?= base_url('admin/pengguna') ?>">Petugas</a></li>
                            <li class="nav-item"><a class="nav-link <?= (url_is('admin/nasabah*')) ? 'active' : '' ?>" href="<?= base_url('admin/nasabah') ?>">Nasabah</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('admin/wilayah') ?>">
                        <i class="fas fa-fw fa-map-marker-alt"></i>
                        <span>Manajemen Wilayah</span>
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link <?= (url_is('admin/katalog*')) ? 'active' : '' ?>" href="<?= base_url('admin/katalog') ?>"><i class="fas fa-boxes me-2"></i> Katalog</a></li>
                <li class="nav-item"><a class="nav-link <?= (url_is('admin/tarif_penjemputan*')) ? 'active' : '' ?>" href="<?= base_url('admin/tarif_penjemputan') ?>"><i class="fas fa-truck me-2"></i> Tarif Jemput</a></li>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('admin/jadwal_penjemputan*')) ? 'active' : '' ?>" href="<?= base_url('admin/jadwal_penjemputan') ?>">
                        <i class="fas fa-calendar-alt me-2"></i> Jadwal Jemput
                    </a>
                </li>

            <!-- ROLE B: OPERATOR BANK SAMPAH LAPANGAN -->
            <?php elseif (session()->get('logged_in') && session()->get('role') === 'operator_bank'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('operator/dashboard*')) ? 'active' : '' ?>" href="<?= base_url('operator/dashboard') ?>">
                        <i class="fas fa-desktop me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('operator/transaksi_timbangan*')) ? 'active' : '' ?>" href="<?= base_url('operator/transaksi_timbangan') ?>">
                        <i class="fas fa-balance-scale me-2"></i> Transaksi Timbangan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('operator/laporan_transaksi*')) ? 'active' : '' ?>" href="<?= base_url('operator/laporan_transaksi') ?>">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Laporan Transaksi
                    </a>
                </li>

            <!-- ROLE C: OPERATOR KELURAHAN (AUDITOR WILAYAH) -->
            <?php elseif (session()->get('logged_in') && session()->get('role') === 'operator_kelurahan'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('kelurahan/dashboard*')) ? 'active' : '' ?>" href="<?= base_url('kelurahan/dashboard') ?>">
                        <i class="fas fa-shield-halved me-2"></i> Dashboard 
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('kelurahan/laporan*')) ? 'active' : '' ?>" href="<?= base_url('kelurahan/laporan') ?>">
                        <i class="fas fa-file-signature me-2"></i> Rekap Laporan RW
                    </a>
                </li>

           <?php elseif (session()->get('logged_in') && session()->get('role') === 'kepala_kemantren'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('kemantren/dashboard*')) ? 'active' : '' ?>" href="<?= base_url('kemantren/dashboard') ?>">
                        <i class="fas fa-chart-pie me-2"></i> Dashboard 
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('kemantren/laporan*')) ? 'active' : '' ?>" href="<?= base_url('kemantren/laporan') ?>">
                        <i class="fas fa-chart-line me-2"></i> Monitoring Laporan
                    </a>
                </li>

            <!-- ROLE E: NASABAH / WARGA MASYARAKAT -->
            <?php elseif (session()->get('logged_in') && session()->get('role') === 'nasabah'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('nasabah/dashboard*')) ? 'active' : '' ?>" href="<?= base_url('nasabah/dashboard') ?>">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('nasabah/minta_jemput') ?>"><i class="fas fa-truck-pickup me-2"></i> Minta Jemput</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('nasabah/riwayat_timbangan') ?>"><i class="fas fa-history me-2"></i> Riwayat Timbangan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('nasabah/penarikan_saldo') ?>"><i class="fas fa-wallet me-2"></i> Penarikan Saldo</a>
                </li>
            <?php endif; ?>

            <li class="nav-item mt-4 border-top pt-3 text-center">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Sistem Bank Sampah</small>
            </li>
        </ul>
    </nav>

    <div id="content">
        <nav class="navbar navbar-expand bg-white shadow-sm mb-4 px-4 py-2" style="border-radius: 0 0 15px 15px; margin: 0 15px;">
            <div class="container-fluid p-0">
                <button type="button" id="sidebarCollapse">
                    <i class="fas fa-bars"></i>
                </button>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDrop" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end me-3 d-none d-sm-block">
                                <span class="d-block fw-bold text-dark" style="font-size: 0.9rem; line-height: 1.2;"><?= session()->get('nama') ?: 'Administrator'; ?></span>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= session()->get('jabatan') ?: 'Nasabah'; ?></small>
                            </div>
                            <div class="user-avatar shadow-sm">
                                <?php if(session()->get('role') === 'nasabah'): ?>
                                    <i class="fas fa-user text-white"></i>
                                <?php else: ?>
                                    <i class="fas fa-user-shield text-white"></i>
                                <?php endif; ?>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3 py-2" aria-labelledby="userDrop" style="min-width: 200px;">
                            <li><h6 class="dropdown-header">Akun Saya</h6></li>
                            <li><a class="dropdown-item py-2" href="#"><i class="fas fa-user-circle me-2 text-primary"></i> Profil Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger fw-bold" href="<?= base_url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i> Keluar (Logout)
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid px-4">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Menampilkan pesan sukses secara otomatis jika ada flashdata
    <?php if (session()->getFlashdata('success')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= session()->getFlashdata('success') ?>',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>
</script>

<?= $this->renderSection('scripts') ?>

</body>
</html>