<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .table thead th { vertical-align: middle !important; padding: 12px 10px !important; background-color: #f8f9fa; color: #333; font-weight: 700; border: 1px solid #dee2e6 !important; }
    .table tbody td { vertical-align: middle !important; padding: 10px 10px !important; border: 1px solid #dee2e6 !important; }
    .harga-text { color: #27ae60; font-weight: 800; font-size: 1.05rem; }
    .jenis-sampah { font-weight: 700; color: #2c3e50; }
    .admin-pencatat { font-size: 0.85rem; color: #666; font-weight: 500; }
    .badge-date { background-color: #f0f7ff; color: #0d47a1; border: 1px solid #bbdefb; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Katalog <span class="text-success">Sampah & Harga</span></h4>
            <p class="text-muted small mb-0">Kelola daftar harga beli sampah dari masyarakat berbasis MasJOS.</p>
        </div>
        <button class="btn btn-success shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKatalog">
            <i class="fas fa-plus me-2"></i> Tambah Katalog
        </button>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-center">
                    <thead class="text-uppercase small fw-bold">
                        <tr>
                            <th width="5%">No</th>
                            <th class="text-start px-4">Jenis Sampah</th>
                            <th width="18%">Harga / Kg</th>
                            <th width="20%">Tanggal Berlaku</th>
                            <th width="15%">Pencatat</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($katalog as $k): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $no++ ?></td>
                            <td class="text-start px-4">
                                <div class="jenis-sampah"><i class="fas fa-recycle text-success me-2"></i> <?= esc($k['katalog_sampah']) ?></div>
                            </td>
                            <td><span class="harga-text">Rp <?= number_format($k['harga_per_kg'], 0, ',', '.') ?></span></td>
                            <td>
                                <div class="badge-date"><i class="far fa-calendar-alt me-1 text-primary"></i> <?= date('d/m/Y', strtotime($k['tanggal_berlaku'])) ?></div>
                            </td>
                            <td>
                                <div class="admin-pencatat">
                                    <i class="fas fa-user-circle me-1 text-secondary"></i> 
                                    <?= esc(isset($k['nama_pengguna']) ? $k['nama_pengguna'] : 'Admin') ?>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-warning btn-edit-katalog me-1" 
                                        data-id="<?= $k['id_katalog'] ?>" data-nama="<?= esc($k['katalog_sampah']) ?>" 
                                        data-harga="<?= $k['harga_per_kg'] ?>" data-mulai="<?= $k['tanggal_berlaku'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-hapus-katalog" 
                                        data-id="<?= $k['id_katalog'] ?>" data-nama="<?= esc($k['katalog_sampah']) ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahKatalog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Tambah Jenis Sampah</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/katalog/simpan') ?>" method="post">
                <div class="modal-body p-4 text-start">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small">Nama Jenis Sampah</label>
                            <input type="text" name="katalog_sampah" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Harga per Kilogram</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_per_kg" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Mulai Berlaku</label>
                            <input type="date" name="tanggal_berlaku" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-success px-4 fw-bold w-100 py-2">SIMPAN KATALOG</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Perbarui Katalog Sampah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/katalog/update') ?>" method="post">
                <input type="hidden" name="id_katalog" id="edit_id">
                <div class="modal-body p-4 text-start">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small">Nama Jenis Sampah</label>
                            <input type="text" name="katalog_sampah" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Harga per Kilogram</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_per_kg" id="edit_harga" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Tanggal Berlaku</label>
                            <input type="date" name="tanggal_berlaku" id="edit_mulai" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-warning px-4 fw-bold w-100 py-2">UPDATE PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.btn-edit-katalog').on('click', function() {
            $('#edit_id').val($(this).data('id'));
            $('#edit_nama').val($(this).data('nama'));
            $('#edit_harga').val($(this).data('harga'));
            $('#edit_mulai').val($(this).data('mulai'));
            $('#modalEdit').modal('show');
        });

        $('.btn-hapus-katalog').on('click', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Katalog?',
                text: "Jenis sampah '" + nama + "' akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('admin/katalog/hapus') ?>/" + id;
                }
            });
        });

        // Notifikasi otomatis hilang setelah 3 detik
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 3000);
    });
</script>
<?= $this->endSection() ?>