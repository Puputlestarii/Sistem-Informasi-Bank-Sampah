<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<style>
    .card-custom { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
    .table-premium thead th { background-color: #f8f9fa; color: #4a5568; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; border: 1px solid #e2e8f0 !important; padding: 12px; }
    .table-premium tbody td { vertical-align: middle !important; border: 1px solid #e2e8f0 !important; font-size: 0.85rem; padding: 12px; }
    .modal-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 15px; }
    .modal-icon { width: 30px; color: #666; display: inline-block; }
    .badge-metode-mandiri { background-color: #e0f2fe; color: #0369a1; font-weight: 700; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; border: 1px solid #bae6fd; }
    .badge-metode-jemput { background-color: #ffedd5; color: #c2410c; font-weight: 700; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; border: 1px solid #fed7aa; }
</style>

<div class="container-fluid px-2">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div><h4 class="fw-bold text-dark mb-0">Laporan <b>Transaksi Timbangan</b></h4></div>
        <a href="<?= base_url('operator/cetak_laporan') ?>" target="_blank" class="btn btn-danger fw-bold shadow-sm px-3">
            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
        </a>
    </div>

    <ul class="nav nav-tabs mb-4 border-0">
    <li class="nav-item">
        <a class="nav-link bg-light text-secondary fw-bold" href="<?= base_url('operator/laporan_penjemputan') ?>">
            <i class="fas fa-truck me-2"></i> Laporan Penjemputan
        </a>
    </li>
</ul>

    <div class="card card-custom bg-white">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-history text-primary me-2"></i> Buku Jurnal Mutasi Tabungan Kelurahan <?= esc($operator['id_kelurahan']) ?>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover mb-0 align-middle text-center">
                    <thead>
                        <tr><th>ID NOTA</th><th>Tanggal</th><th>Nama Nasabah</th><th>RW</th><th>Layanan</th><th>Kategori</th><th>Berat</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayat_timbang as $rt) : ?>
                            <tr>
                                <td class="fw-bold">#TRX-<?= esc($rt['id_transaksi']) ?></td>
                                <td class="text-muted"><?= date('d/m/Y', strtotime($rt['tanggal_transaksi'])) ?></td>
                                <td class="text-start px-3"><?= esc($rt['nama_nasabah']) ?></td>
                                <td class="fw-bold text-primary"><?= isset($rt['nama_rw']) ? esc($rt['nama_rw']) : '-' ?></td>
                                <td>
                                    <?php if (isset($rt['biaya_penjemputan']) && $rt['biaya_penjemputan'] > 0) : ?>
                                        <span class="badge-metode-jemput"><i class="fas fa-truck me-1"></i> Dijemput</span>
                                    <?php else : ?>
                                        <span class="badge-metode-mandiri"><i class="fas fa-store me-1"></i> Mandiri</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= esc(isset($rt['katalog_sampah']) ? $rt['katalog_sampah'] : '-') ?></span></td>
                                <td class="fw-bold"><?= number_format(isset($rt['berat_sampah']) ? $rt['berat_sampah'] : 0, 2, ',', '.') ?> Kg</td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $rt['id_transaksi'] ?>">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php foreach ($riwayat_timbang as $rt) : ?>
<div class="modal fade" id="modalDetail<?= $rt['id_transaksi'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success">Detail Transaksi #TRX-<?= esc($rt['id_transaksi']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="modal-card bg-light border-0">
                    <h6 class="fw-bold mb-3 text-secondary">Informasi Utama</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex mb-2"><i class="fas fa-calendar-alt modal-icon"></i> <span>Tanggal : <b><?= date('d/m/Y', strtotime($rt['tanggal_transaksi'])) ?></b></span></div>
                            <div class="d-flex mb-2"><i class="fas fa-user modal-icon"></i> <span>Nasabah : <b><?= esc($rt['nama_nasabah']) ?></b></span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-2"><i class="fas fa-truck modal-icon"></i> <span>Layanan : <b><?= (isset($rt['biaya_penjemputan']) && $rt['biaya_penjemputan'] > 0) ? 'Dijemput' : 'Mandiri' ?></b></span></div>
                            <div class="d-flex"><i class="fas fa-users modal-icon"></i> <span>RW : <b><?= isset($rt['nama_rw']) ? esc($rt['nama_rw']) : '-' ?></b></span></div>
                        </div>
                    </div>
                </div>

                <div class="modal-card border-0 shadow-sm">
                    <h6 class="fw-bold mb-3 text-secondary">Detail Sampah</h6>
                    <?php 
                        $berat = isset($rt['berat_sampah']) ? $rt['berat_sampah'] : 0;
                        $harga = isset($rt['harga_per_kg']) ? $rt['harga_per_kg'] : 0;
                        $biaya = isset($rt['biaya_penjemputan']) ? $rt['biaya_penjemputan'] : 0;
                        $subtotal = isset($rt['subtotal']) ? $rt['subtotal'] : 0;
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <span>Jenis : <b><?= esc(isset($rt['katalog_sampah']) ? $rt['katalog_sampah'] : '-') ?></b></span>
                            <div class="d-flex mb-2"><i class="fas fa-weight modal-icon"></i> <span>Berat : <b><?= number_format($berat, 2, ',', '.') ?> Kg</b></span></div>
                            <div class="d-flex">
                                <i class="fas fa-tag modal-icon"></i> 
                                <span>Harga/Kg : <b>Rp <?= number_format($harga, 0, ',', '.') ?></b></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-2 justify-content-between"><span>Subtotal Sampah :</span> <b>Rp <?= number_format(($berat * $harga), 0, ',', '.') ?></b></div>
                            <div class="d-flex mb-2 justify-content-between"><span>Biaya Jemput/kg :</span> <b>Rp <?= number_format($biaya, 0, ',', '.') ?></b></div>
                        </div>
                    </div>
                    <div class="text-end mt-3 border-top pt-2">
                        <h5 class="text-success fw-bold">Total Akhir : Rp <?= number_format($subtotal, 0, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?= $this->endSection() ?>