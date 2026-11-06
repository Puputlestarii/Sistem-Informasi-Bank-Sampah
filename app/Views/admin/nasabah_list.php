<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .name-column { white-space: nowrap; font-weight: bold; color: #2c3e50; }
    .addr-text { font-size: 0.85rem; line-height: 1.4; color: #555; display: block; word-wrap: break-word; }
    .badge-wilayah { display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; margin-bottom: 4px; width: fit-content; text-align: left; }
    .badge-wilayah i { width: 15px; text-align: center; }
    .bg-kel { background-color: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb; }
    .bg-rw { background-color: #f3e5f5; color: #4a148c; border: 1px solid #e1bee7; }
    .saldo-val { color: #27ae60; font-weight: 800; font-size: 1rem; white-space: nowrap; }
</style>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark">Data Nasabah Terdaftar</h4>
            <p class="text-muted small">Daftar lengkap warga yang berpartisipasi dalam program MasJOS.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center text-uppercase small fw-bold">
                        <tr>
                            <th width="3%">No</th>
                            <th width="20%">Nasabah</th>
                            <th width="40%">Alamat & Kontak</th>
                            <th width="20%">Wilayah (Kel/RW)</th>
                            <th width="10%">Saldo</th>
                            <th width="7%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($nasabah as $n): ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                            <td class="px-3">
                                <div class="name-column"><?= esc($n['nama_nasabah']) ?></div>
                                <small class="text-muted">User: <span class="text-danger"><?= esc($n['username']) ?></span></small>
                            </td>
                            <td class="px-3">
                                <span class="addr-text mb-2"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc($n['alamat']) ?></span>
                                <div class="text-success small fw-bold"><i class="fas fa-phone-alt me-1"></i> <?= esc($n['nomor_telepon']) ?></div>
                            </td>
                            <td class="text-center">
                                <div class="badge-wilayah bg-kel">
                                    <i class="fas fa-city me-1"></i> 
                                    <?= esc(isset($n['nama_kelurahan']) && $n['nama_kelurahan'] ? $n['nama_kelurahan'] : 'Belum Diatur') ?>
                                </div>
                                <br>
                                <div class="badge-wilayah bg-rw">
                                    <i class="fas fa-users me-1"></i> 
                                    <?= esc(isset($n['nama_rw']) && $n['nama_rw'] ? $n['nama_rw'] : 'Belum Diatur') ?>
                                </div>
                            </td>
                            <td class="text-end px-3">
                                <span class="saldo-val">Rp <?= number_format(isset($n['saldo']) ? $n['saldo'] : 0, 0, ',', '.') ?></span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger btn-hapus-nasabah" 
                                        data-id="<?= $n['id_nasabah'] ?>" 
                                        data-nama="<?= esc($n['nama_nasabah']) ?>">
                                    <i class="fas fa-trash-alt"></i>
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

<div class="modal fade" id="modalTambahNasabah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Tambah Nasabah Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/nasabah/simpan') ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body p-4">
                    </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.btn-hapus-nasabah').on('click', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Nasabah?',
                text: "Data " + nama + " akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('admin/nasabah/hapus') ?>/" + id;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>