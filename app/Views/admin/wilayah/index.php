<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Manajemen Wilayah</h3>
            <p class="text-muted small">Pilih Kelurahan untuk mengelola data RW</p>
        </div>
        <button class="btn btn-primary fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKelurahan">
            <i class="fas fa-plus-circle me-1"></i> Tambah Kelurahan
        </button>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success border-0 shadow-sm mb-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($kelurahan as $k) : ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 5px solid #16a085 !important; border-radius: 12px;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kelurahan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($k['nama_kelurahan']) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-map-marked-alt fa-2x text-gray-300 opacity-25"></i>
                            </div>
                        </div>
                        <hr class="opacity-50">
                        <a href="<?= base_url('admin/wilayah/detail_rw/' . $k['id_kelurahan']) ?>" class="btn btn-sm btn-outline-success w-100 fw-bold mb-2">
                            Kelola RW <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-warning text-white w-50 fw-bold" 
                                    onclick="editKelurahan('<?= $k['id_kelurahan'] ?>', '<?= esc($k['nama_kelurahan']) ?>')">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger w-50 fw-bold" 
                                    onclick="hapusKelurahan('<?= base_url('admin/wilayah/hapus_kelurahan/' . $k['id_kelurahan']) ?>')">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="modalTambahKelurahan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/wilayah/simpan_kelurahan') ?>" method="post" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Kelurahan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Kelurahan</label>
                    <input type="text" name="nama_kelurahan" class="form-control" placeholder="Contoh: Kelurahan Tompeyan" required>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditKelurahan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/wilayah/update_kelurahan') ?>" method="post" class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Kelurahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_kelurahan" id="edit_id_kelurahan">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Kelurahan</label>
                    <input type="text" name="nama_kelurahan" id="edit_nama_kelurahan" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning text-white px-4">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
// Fungsi Edit
function editKelurahan(id, nama) {
    document.getElementById('edit_id_kelurahan').value = id;
    document.getElementById('edit_nama_kelurahan').value = nama;
    new bootstrap.Modal(document.getElementById('modalEditKelurahan')).show();
}

// Fungsi Konfirmasi Hapus
function hapusKelurahan(url) {
    Swal.fire({
        title: 'Yakin hapus kelurahan?',
        text: "Semua data RW di dalamnya akan ikut terhapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) { window.location.href = url; }
    });
}

// Notifikasi Sukses
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
<?= $this->endSection() ?>