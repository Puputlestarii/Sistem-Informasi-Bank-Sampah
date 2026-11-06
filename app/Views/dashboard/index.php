<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%); overflow: hidden; position: relative;">
        <div class="card-body p-4 p-md-5 text-white" style="position: relative; z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">Halo, <?= (session()->get('nama')) ? session()->get('nama') : 'Administrator'; ?>! 👋</h2>
                    <p class="lead opacity-75 mb-4">Mari kelola potensi ekonomi dari sampah anorganik di wilayah Yogyakarta hari ini.</p>
                    <div class="d-flex gap-2">
                        <div class="btn btn-light bg-white bg-opacity-50 text-dark border-0 px-3 py-2" style="border-radius: 50px; font-size: 0.85rem;" disabled>
                            <i class="far fa-calendar-alt me-2"></i> <?= date('l, d F Y'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="fas fa-coins fa-10x opacity-25" style="transform: rotate(-15deg);"></i>
                </div>
            </div>
        </div>
        <div style="position: absolute; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -100px; right: -50px; z-index: 1;"></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3 card-hover" style="border-radius: 20px;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-4 me-3">
                        <i class="fas fa-user-tie fa-2x text-primary"></i>
                    </div>
                    <div class="text-start">
                        <h6 class="text-muted small text-uppercase mb-1 fw-bold">Total Petugas</h6>
                        <h2 class="fw-bold mb-0"><?= (isset($jumlah_user)) ? $jumlah_user : 0; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3 card-hover" style="border-radius: 20px;">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-4 rounded-4 me-3">
                        <i class="fas fa-users-line fa-2x text-warning"></i>
                    </div>
                    <div class="text-start">
                        <h6 class="text-muted small text-uppercase mb-1 fw-bold">Total Nasabah</h6>
                        <h2 class="fw-bold mb-0"><?= (isset($jumlah_nasabah)) ? $jumlah_nasabah : 0; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3 card-hover" style="border-radius: 20px;">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-4 rounded-4 me-3">
                        <i class="fas fa-hand-holding-dollar fa-2x text-success"></i>
                    </div>
                    <div class="text-start">
                        <h6 class="text-muted small text-uppercase mb-1 fw-bold">Jenis Sampah Jual</h6>
                        <h2 class="fw-bold mb-0"><?= (isset($jumlah_katalog)) ? $jumlah_katalog : 0; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; height: 100%;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-recycle text-primary me-2"></i> Potensi Sampah Anorganik</h5>
                    <span class="badge bg-soft-primary text-primary rounded-pill px-3">Bernilai Jual</span>
                </div>
                <p class="text-muted mb-4 small">Persentase jenis sampah anorganik yang paling sering ditabung oleh nasabah.</p>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold text-dark">Plastik (PET/HDPE)</span>
                        <span class="small fw-bold">60%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 10px; background-color: #f0f0f0;">
                        <div class="progress-bar shadow-none" role="progressbar" style="width: 60%; background-color: #3498db;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold text-dark">Kertas & Karton</span>
                        <span class="small fw-bold">30%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 10px; background-color: #f0f0f0;">
                        <div class="progress-bar shadow-none" role="progressbar" style="width: 30%; background-color: #f1c40f;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold text-dark">Logam & Besi</span>
                        <span class="small fw-bold">10%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 10px; background-color: #f0f0f0;">
                        <div class="progress-bar shadow-none" role="progressbar" style="width: 10%; background-color: #e74c3c;"></div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-4 border-dashed mt-auto">
                    <p class="small text-muted mb-0"><i class="fas fa-lightbulb text-warning me-2"></i><b>Info:</b> Sampah anorganik yang bersih memiliki nilai jual lebih tinggi di pasar pengepul.</p>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; height: 100%;">
                <h5 class="fw-bold text-dark mb-4">Aksi Cepat</h5>
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('admin/katalog') ?>" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center py-3">
                        <div class="bg-soft-success rounded-circle p-2 me-3"><i class="fas fa-tags text-success"></i></div>
                        <div>
                            <p class="mb-0 fw-bold">Update Harga Sampah</p>
                            <small class="text-muted">Sesuaikan dengan harga pasar</small>
                        </div>
                        <i class="fas fa-chevron-right ms-auto small text-muted"></i>
                    </a>
                    <a href="<?= base_url('admin/tarif_penjemputan') ?>" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center py-3">
                        <div class="bg-soft-warning rounded-circle p-2 me-3"><i class="fas fa-truck text-warning"></i></div>
                        <div>
                            <p class="mb-0 fw-bold">Tarif Penjemputan</p>
                            <small class="text-muted">Kelola biaya jasa antar-jemput</small>
                        </div>
                        <i class="fas fa-chevron-right ms-auto small text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(52, 152, 219, 0.15); }
    .bg-soft-success { background-color: rgba(46, 204, 113, 0.15); }
    .bg-soft-warning { background-color: rgba(243, 156, 18, 0.15); }
    .border-dashed { border: 1px dashed #dee2e6; }
    .card-hover:hover { 
        transform: translateY(-5px); 
        transition: all 0.3s ease; 
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
</style>
<?= $this->endSection() ?>