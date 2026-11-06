<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/wilayah') ?>">Wilayah</a></li>
            <li class="breadcrumb-item active">Kelurahan <?= esc($kel['nama_kelurahan']) ?></li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-users-cog me-2 text-success"></i> Data RW - <?= esc($kel['nama_kelurahan']) ?></h6>
            <button class="btn btn-success btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus-circle me-1"></i> Tambah RW
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">No</th>
                            <th>Nama RW</th>
                            <th width="25%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rw)) : ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted italic">Belum ada data RW di kelurahan ini.</td></tr>
                        <?php else : $i = 1; foreach ($rw as $r) : ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td class="fw-bold"><?= esc($r['nama_rw']) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm text-white px-3 fw-bold me-1" 
                                            onclick="editRW('<?= $r['id_rw'] ?>', '<?= esc($r['nama_rw']) ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm px-3 fw-bold" 
                                            onclick="hapusRW('<?= base_url('admin/wilayah/hapus_rw/' . $r['id_rw']) ?>')">
                                        <i class="fas fa-trash"></i>
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

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/wilayah/simpan_rw') ?>" method="post" class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah RW Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_kelurahan" value="<?= $kel['id_kelurahan'] ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama RW</label>
                    <input type="text" name="nama_rw" class="form-control" placeholder="Contoh: RW 01" required>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/wilayah/update_rw') ?>" method="post" class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Nama RW</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_rw" id="edit_id_rw">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama RW</label>
                    <input type="text" name="nama_rw" id="edit_nama_rw" class="form-control" required>
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
function editRW(id, nama) {
    document.getElementById('edit_id_rw').value = id;
    document.getElementById('edit_nama_rw').value = nama;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

// Fungsi Konfirmasi Hapus SweetAlert2
function hapusRW(url) {
    Swal.fire({
        title: 'Yakin hapus data?',
        text: "Data RW ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// Notifikasi Sukses Flashdata
<?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('success') ?>',
        timer: 2000,
        showConfirmButton: false
    });
<?php endif; ?>

// Loading saat submit form
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });
});
</script>
<?= $this->endSection() ?>