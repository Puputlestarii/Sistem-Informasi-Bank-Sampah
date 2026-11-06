<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .card-custom { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .table-premium thead th {
        background-color: #f8f9fa;
        color: #4a5568;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        border: 1px solid #e2e8f0 !important;
    }
    .table-premium tbody td { vertical-align: middle !important; border: 1px solid #e2e8f0 !important; font-size: 0.85rem; }
    
    /* Sentuhan penyesuaian gaya agar lencana nama RW panjang tidak turun berantakan */
    .badge-rw-lengkap {
        display: inline-block;
        padding: 6px 12px;
        font-weight: 700;
        border-radius: 6px;
        font-size: 0.75rem;
        background-color: #f3e5f5 !important; 
        color: #4a148c !important; 
        border: 1px solid #e1bee7 !important;
        white-space: nowrap;
    }
</style>

<div class="container-fluid px-2">
    <div class="card card-custom text-white p-4 mb-4" style="background: linear-gradient(135deg, #16a085, #2c3e50);">
        <h2 class="fw-bold mb-1">
            Audit Wilayah Kelurahan: <?= isset($operator['kelurahan']) ? esc($operator['kelurahan']) : 'Wilayah Anda' ?> 👋
        </h2>
        <p class="opacity-90 small mb-0">Memantau seluruh aktivitas log transaksi timbangan dari Operator Bank Sampah lapangan di wilayah Anda.</p>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;"><i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card card-custom p-3 bg-white border-start border-info border-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Total Warga Terdaftar</div>
                        <h2 class="fw-bold my-1 text-dark"><?= $total_nasabah ?> <span class="text-muted" style="font-size: 1rem;">Nasabah</span></h2>
                    </div>
                    <i class="fas fa-users fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card card-custom p-3 bg-white border-start border-success border-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Total Transaksi Wilayah</div>
                        <h2 class="fw-bold my-1 text-success"><?= $total_valid ?> <span class="text-muted" style="font-size: 1rem;">Log Tersimpan</span></h2>
                    </div>
                    <i class="fas fa-check-double fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom bg-white shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-list-check text-success me-2"></i> Riwayat Transaksi Timbangan Sampah Wilayah Kelurahan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover mb-0 align-middle text-center">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th>Tanggal & Waktu</th>
                            <th>Nama Nasabah</th>
                            <th>Wilayah (RW)</th>
                            <th>Katalog</th>
                            <th>Berat Bersih</th>
                            <th>Subtotal Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transaksi_pending)) : ?>
                            <tr><td colspan="7" class="py-5 text-muted small italic">Belum ada riwayat rekaman transaksi timbangan di wilayah Kelurahan ini.</td></tr>
                        <?php else : foreach ($transaksi_pending as $tp) : ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= $tp['id_transaksi'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($tp['tanggal_transaksi'])) ?></td>
                                <td class="fw-bold text-dark text-start px-3"><?= esc($tp['nama_nasabah']) ?></td>
                                
                               <td>
                                <span class="badge-rw-lengkap">
                                    <i class="fas fa-users me-1"></i>
                                    <?php 
                                        // 1. Cek apakah variabel relasi join nama_rw tersedia dan tidak kosong
                                        if (isset($tp['nama_rw']) && !empty($tp['nama_rw'])) {
                                            echo esc($tp['nama_rw']);
                                        } else {
                                            // 2. Fallback aman jika data penampung di baris transaksi berupa teks angka murni (Contoh: "24")
                                            $angka_rw_saja = preg_replace('/[^0-9]/', '', $tp['rw']);
                                            
                                            if (!empty($angka_rw_saja)) {
                                                // Lakukan pencarian manual ke tabel master rw berdasarkan ID angka murni yang dikirim
                                                $db_manual = \Config\Database::connect();
                                                $cek_rw_master = $db_manual->table('rw')->where('id', $angka_rw_saja)->get()->getRowArray();
                                                
                                                if ($cek_rw_master && isset($cek_rw_master['nama_rw'])) {
                                                    echo esc($cek_rw_master['nama_rw']);
                                                } else {
                                                    echo 'RW ' . sprintf("%02d", $angka_rw_saja);
                                                }
                                            } else {
                                                echo 'RW ' . esc($tp['rw']);
                                            }
                                        }
                                    ?>
                                </span>
                            </td>
                                
                                <td><?= esc($tp['katalog_sampah']) ?></td>
                                <td class="fw-bold"><?= number_format($tp['berat_sampah'], 2, ',', '.') ?> Kg</td>
                                <td class="text-success fw-bold">Rp <?= number_format($tp['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->endSection() ?>