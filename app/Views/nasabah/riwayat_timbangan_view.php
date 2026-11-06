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
        letter-spacing: 0.5px;
        border: 1px solid #e2e8f0 !important;
    }
    .table-premium tbody td { vertical-align: middle !important; border: 1px solid #e2e8f0 !important; font-size: 0.85rem; }
    
    /* Style Badge Premium Metode */
    .badge-metode-mandiri { background-color: #e0f2fe; color: #0369a1; font-weight: 700; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; border: 1px solid #bae6fd; }
    .badge-metode-jemput { background-color: #ffedd5; color: #c2410c; font-weight: 700; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; border: 1px solid #fed7aa; }
</style>

<div class="container-fluid px-2">
    <div class="d-flex align-items-center mb-4">
        <div class="me-3 bg-primary text-white p-3 rounded-3 shadow-sm">
            <i class="fas fa-history fa-2x"></i>
        </div>
        <div>
            <h4 class="fw-bold text-dark mb-0">Riwayat <b>Timbangan Sampah</b></h4>
            <p class="text-muted small mb-0">Daftar transparansi catatan hasil penimbangan sampah anorganik Anda oleh petugas MasJOS.</p>
        </div>
    </div>

    <div class="card card-custom bg-white">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-list text-primary me-2"></i> Semua Log Catatan Timbangan</h6>
            <span class="badge bg-light text-muted border small px-3">Data Akurat Akuntansi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover mb-0 align-middle text-center">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal & Waktu</th>
                            <th>Jenis Sampah</th>
                            <th>Metode Layanan</th>
                            <th>Berat (Kg)</th>
                            <th>Harga / Kg</th>
                            <th>Subtotal Saldo Masuk</th>
                            <th>Petugas Penimbang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat_lengkap)) : ?>
                            <tr>
                                <td colspan="8" class="py-5 text-muted small italic">
                                    <i class="fas fa-folder-open d-block mb-2 fa-2x text-light"></i>
                                    Belum ada catatan penimbangan sampah terdata untuk akun Anda.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1; foreach ($riwayat_lengkap as $rl) : 
                                // KALKULASI PENGAMAN: Jika database lama menyimpan NULL, hitung dinamis dari subtotal / berat
                                $harga_satuan = floatval($rl['harga_per_kg']);
                                if ($harga_satuan <= 0 && floatval($rl['berat_sampah']) > 0) {
                                    $harga_satuan = round(floatval($rl['subtotal']) / floatval($rl['berat_sampah']));
                                }
                            ?>
                                <tr>
                                    <td class="text-muted fw-bold"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($rl['tanggal_transaksi'])) ?></div>
                                        <small class="text-muted" style="font-size: 0.7rem;"><i class="far fa-clock me-1"></i> <?= date('H:i', strtotime($rl['tanggal_transaksi'])) ?> WIB</small>
                                    </td>
                                    <td class="fw-bold text-dark text-start px-3">
                                        <i class="fas fa-recycle text-success me-2"></i> <?= $rl['katalog_sampah'] ?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($rl['id_tarif'] != null && $rl['id_tarif'] > 0) : ?>
                                            <span class="badge-metode-jemput">
                                                <i class="fas fa-truck me-1"></i> Dijemput
                                            </span>
                                        <?php else : ?>
                                            <span class="badge-metode-mandiri">
                                                <i class="fas fa-store me-1"></i> Mandiri
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold"><?= number_format($rl['berat_sampah'], 2, ',', '.') ?> Kg</span>
                                    </td>
                                    <td class="text-secondary">Rp <?= number_format($harga_satuan, 0, ',', '.') ?></td>
                                    <td class="fw-bold text-success text-end px-4">
                                        + Rp <?= number_format($rl['subtotal'], 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <span class="small fw-bold text-primary"><i class="fas fa-user-check me-1"></i> <?= !empty($rl['nama_petugas']) ? $rl['nama_petugas'] : 'Lestari Puput' ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>