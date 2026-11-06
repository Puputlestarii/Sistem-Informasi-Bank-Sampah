<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .table thead th { vertical-align: middle !important; background-color: #f8f9fa; border: 1px solid #dee2e6 !important; text-transform: uppercase; font-size: 0.75rem; color: #495057; }
    .table tbody td { vertical-align: middle !important; border: 1px solid #dee2e6 !important; font-size: 0.9rem; }
    .badge-status { font-weight: 600; letter-spacing: 0.5px; }
    .badge-jam { background-color: #212529 !important; font-weight: 500; }
    .op-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; }
    .op-card { border: 1px solid #e3e6f0; border-radius: 12px; background: #fff; padding: 15px 5px; }
</style>

<div class="container-fluid">
    <div class="mb-3">
        <h4 class="fw-bold text-primary"><i class="fas fa-tasks me-2"></i>Manajemen Jadwal</h4>
        <p class="text-muted">Kelola operasional dan penjemputan sampah.</p>
    </div>

<div class="container-fluid">
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body">
            <div class="op-grid">
                <?php if(isset($jam_operasional)) foreach($jam_operasional as $jo): ?>
                <div class="op-card text-center shadow-sm">
                    <div class="fw-bold text-uppercase small"><?= isset($jo['Hari']) ? $jo['Hari'] : '-' ?></div>
                    <div class="<?= (isset($jo['Status_buka']) && $jo['Status_buka'] == 'Tutup') ? 'text-danger' : 'text-success' ?> fw-bold">
                        <?= (isset($jo['Status_buka']) && $jo['Status_buka'] == 'Buka') ? substr($jo['Jam_mulai'], 0, 5) . ' - ' . substr($jo['Jam_selesai'], 0, 5) : 'LIBUR' ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white border-0 py-3">
            <h5 class="m-0 fw-bold"><i class="fas fa-calendar-alt me-2"></i> Tabel Jadwal Penjemputan Sampah</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-center">
                    <thead>
                        <tr>
                            <th>NASABAH</th>
                            <th>PETUGAS (NAMA & ID)</th>
                            <th>TANGGAL JADWAL</th>
                            <th>JAM JADWAL</th>
                            <th>STATUS JADWAL</th>
                            <th>KETERANGAN</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($permintaan)) : ?>
                            <tr><td colspan="8" class="py-4 text-muted">Belum ada data jadwal.</td></tr>
                        <?php else : foreach ($permintaan as $p) : ?>
                            <tr>
                                <td class="text-start ps-3">
                                    <div class="fw-bold text-dark"><?= isset($p['nama_nasabah']) ? esc($p['nama_nasabah']) : '-' ?></div>
                                </td>
                                <td class="text-start ps-3">
                                    <div class="fw-bold text-primary"><?= isset($p['nama_petugas']) ? esc($p['nama_petugas']) : 'Belum Dipilih' ?></div>
                                    <small class="text-muted">ID: USR-<?= isset($p['id_petugas']) ? $p['id_petugas'] : '-' ?></small>
                                </td>
                                <td><?= isset($p['Tanggal_jadwal']) ? date('d/m/Y', strtotime($p['Tanggal_jadwal'])) : '-' ?></td>
                                <td>
                                    <span class="badge badge-jam rounded-pill px-3">
                                        <i class="fas fa-clock me-1"></i> 
                                        <?= (!empty($p['Jam_jadwal'])) ? substr($p['Jam_jadwal'], 0, 5) : '00:00' ?> WIB
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        $status = isset($p['Status_penjemputan']) ? $p['Status_penjemputan'] : 'Menunggu';
                                        $color = ($status == 'Selesai') ? 'success' : (($status == 'Proses') ? 'primary' : 'warning text-dark');
                                    ?>
                                    <span class="badge bg-<?= $color ?> rounded-pill px-3 text-uppercase badge-status">
                                        <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i> <?= $status ?>
                                    </span>
                                </td>
                                <td class="text-start">
                                    <small class="text-muted">
                                        <?= !empty($p['Keterangan_admin']) ? esc($p['Keterangan_admin']) : '-' ?>
                                    </small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary px-3 btn-Aksi-Jadwal-Admin" 
                                            data-bs-toggle="modal" data-bs-target="#modalTambahJadwalAdmin" 
                                            data-id-permintaan="<?= isset($p['Id_permintaan']) ? $p['Id_permintaan'] : '' ?>"
                                            data-id-kelurahan="<?= isset($p['id_kelurahan']) ? $p['id_kelurahan'] : '' ?>"
                                            data-nama-nasabah="<?= isset($p['nama_nasabah']) ? esc($p['nama_nasabah']) : 'Tanpa Nama' ?>">
                                        <i class="fas fa-calendar-check me-1"></i> Plot
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahJadwalAdmin" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Plotting Penjadwalan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/verifikasi_jadwal') ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id_permintaan" id="admin_id_permintaan">
                <div class="modal-body p-4">
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="small text-muted fw-bold d-block">Nama Nasabah:</label>
                        <span id="display_nama_nasabah" class="fw-bold text-primary fs-5"></span>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Pilih Petugas (Sesuai Kelurahan Wilayah)</label>
                        <select name="id_petugas" id="admin_select_petugas" class="form-select border-primary" required>
                            <option value="" disabled selected>-- Pilih Petugas --</option>
                            <?php if(isset($petugas)) foreach($petugas as $p): ?>
                                <option value="<?= esc($p['id_pengguna']) ?>" 
                                        data-kelurahan-op="<?= isset($p['id_kelurahan']) ? esc($p['id_kelurahan']) : '' ?>">
                                    <?= esc($p['nama_pengguna']) ?> 
                                    <?= isset($p['nama_kelurahan']) ? '(Kel. ' . esc($p['nama_kelurahan']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="small fw-bold">Tanggal</label>
                            <input type="date" name="tanggal_jemput" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="small fw-bold">Jam Penjemputan</label>
                            <input type="time" name="jam_jemput" class="form-control" required value="09:00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary w-100 fw-bold">SIMPAN PLOTTING</button></div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).on('click', '.btn-Aksi-Jadwal-Admin', function() {
        const idPermintaan = $(this).data('id-permintaan');
        const kelNasabah = $(this).data('id-kelurahan');
        const namaNasabah = $(this).data('nama-nasabah');
        
        // Mengisi nilai ke form
        $('#admin_id_permintaan').val(idPermintaan);
        $('#display_nama_nasabah').text(namaNasabah);
        
        // Logika filter petugas per kelurahan
        $('#admin_select_petugas option').each(function() {
            const kelOp = $(this).data('kelurahan-op');
            if ($(this).val() === "") {
                $(this).show();
            } else if (kelOp == kelNasabah && kelNasabah != "") {
                $(this).show().prop('disabled', false);
            } else {
                $(this).hide().prop('disabled', true);
            }
        });
        $('#admin_select_petugas').val('');
    });
</script>
<?= $this->endSection() ?>