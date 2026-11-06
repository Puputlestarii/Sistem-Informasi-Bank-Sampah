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
    
    /* Badge Status Pengajuan */
    .badge-pending { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; padding: 4px 10px; border-radius: 50px; font-weight: 600; }
    .badge-disetujui { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 50px; font-weight: 600; }
    .badge-ditolak { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 50px; font-weight: 600; }
</style>

<div class="container-fluid px-2">
    <div class="d-flex align-items-center mb-4">
        <div class="me-3 bg-primary text-white p-3 rounded-3 shadow-sm">
            <i class="fas fa-wallet fa-2x"></i>
        </div>
        <div>
            <h4 class="fw-bold text-dark mb-0">Kelola <b>Penarikan Saldo</b></h4>
            <p class="text-muted small mb-0">Cairkan saldo hasil penjualan sampah anorganik Anda ke bentuk tunai atau dompet digital.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-5">
            <?php if ($bisa_tarik_saldo) : ?>
                <div class="card card-custom text-white p-3 mb-4 shadow-sm" style="background: linear-gradient(135deg, #2ecc71, #27ae60);">
                    <div class="small text-uppercase fw-bold opacity-75">Total Saldo Yang Dapat Ditarik</div>
                    <h3 class="fw-bold my-1">Rp <?= number_format($nasabah['saldo'], 0, ',', '.') ?></h3>
                    <small class="opacity-75 italic"><i class="fas fa-check-circle"></i> Batas ambang minimal Rp 50.000 terpenuhi.</small>
                </div>

                <div class="card card-custom p-4 bg-white shadow-sm mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-paper-plane text-primary me-2"></i> Formulir Pencairan</h6>
                    <form action="<?= base_url('nasabah/penarikan_saldo/simpan') ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold text-dark small">Tanggal</label>
                                <input type="text" class="form-control bg-light" value="<?= date('d/m/Y') ?>" readonly>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold text-dark small">Wilayah</label>
                                <input type="text" class="form-control bg-light" readonly value="Kel. <?= isset($nasabah['nama_kelurahan']) ? esc($nasabah['nama_kelurahan']) : '-' ?> / RW <?= isset($nasabah['nama_rw']) ? esc($nasabah['nama_rw']) : '-' ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">Nominal Dicaikan (Rp)</label>
                        <input type="number" name="jumlah_pengajuan" class="form-control form-control-lg" 
                            min="50000" 
                            max="<?= (int)$nasabah['saldo'] ?>" 
                            placeholder="Min. 50.000" required>
                        <small class="text-muted mt-1 d-block">
                            *Maksimal penarikan: Rp <?= number_format((int)$nasabah['saldo'], 0, ',', '.') ?>
                        </small>
                    </div>
                        <button type="submit" class="btn btn-warning text-white w-100 fw-bold py-2 shadow-sm">AJUKAN PENARIKAN</button>
                    </form>
                </div>
            <?php else : ?>
                <div class="card card-custom p-4 bg-white shadow-sm text-center">
                    <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold">Saldo Belum Mencukupi</h6>
                    <p class="small text-muted">Minimal saldo Rp 50.000 untuk dapat mengajukan penarikan.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-7">
            <div class="card card-custom bg-white shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-warning me-2"></i> Riwayat Status Pengajuan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium table-hover mb-0 align-middle text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3">Tgl Pengajuan</th>
                                    <th class="py-3">Nominal</th>
                                    <th class="py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($riwayat_tarik)) : ?>
                                    <tr><td colspan="3" class="py-5 text-muted small">Belum ada riwayat penarikan.</td></tr>
                                <?php else : foreach ($riwayat_tarik as $rt) : ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($rt['tanggal_pengajuan'])) ?></td>
                                        <td class="fw-bold text-dark">Rp <?= number_format($rt['jumlah_pengajuan'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php if ($rt['status_pengajuan'] == 'Pending') echo '<span class="badge-pending">Pending</span>';
                                            elseif ($rt['status_pengajuan'] == 'Disetujui') echo '<span class="badge-disetujui">Disetujui</span>';
                                            else echo '<span class="badge-ditolak">Ditolak</span>'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>