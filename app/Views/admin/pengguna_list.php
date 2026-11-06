<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px;">
            <div class="me-3 bg-danger text-white p-2 rounded-3 small"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="fw-bold small text-danger"><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px;">
            <div class="me-3 bg-success text-white p-2 rounded-3 small"><i class="fas fa-check-circle"></i></div>
            <div class="fw-bold small text-success"><?= session()->getFlashdata('success') ?></div>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark">Manajemen <span class="text-primary">Petugas</span></h4>
            <p class="text-muted small">Kelola data petugas, akses login, dan wilayah tugas.</p>
        </div>
        <button class="btn btn-success shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Petugas
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 text-center">
                    <thead class="table-light text-uppercase small fw-bold">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Petugas</th>
                            <th>Username</th>
                            <th>Jabatan</th>
                            <th>Wilayah Tugas</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($users as $u): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-start px-3">
                                <div class="fw-bold text-dark"><?= esc($u['nama_pengguna']) ?></div>
                                <small class="text-muted"><i class="fas fa-envelope me-1" style="font-size:0.75rem;"></i><?= (isset($u['email']) && !empty($u['email'])) ? esc($u['email']) : '-'; ?></small>
                            </td>
                            <td><span class="text-primary fw-bold">@<?= esc($u['username']) ?></span></td>
                            <td>
                                <?php 
                                    $badge_class = 'bg-secondary'; $icon = 'fa-user';
                                    if ($u['jabatan'] == 'Admin') { $badge_class = 'badge-admin'; $icon = 'fa-user-shield'; }
                                    else if ($u['jabatan'] == 'Operator Bank Sampah') { $badge_class = 'badge-bank'; $icon = 'fa-recycle'; }
                                    else if ($u['jabatan'] == 'Operator Kelurahan') { $badge_class = 'badge-kelurahan'; $icon = 'fa-building-user'; }
                                    else if ($u['jabatan'] == 'Kepala Kemantren') { $badge_class = 'badge-kemantren'; $icon = 'fa-user-tie'; }
                                ?>
                                <span class="badge-custom <?= $badge_class ?>">
                                    <i class="fas <?= $icon ?> me-1"></i> <?= esc($u['jabatan']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold">
                                    <i class="fas fa-map-location-dot text-primary me-1"></i>
                                    <?php 
                                        if ($u['jabatan'] == 'Admin' || $u['jabatan'] == 'Kepala Kemantren') {
                                            echo 'Seluruh Wilayah';
                                        } else {
                                            $tampil_kel = '-';
                                            // Gunakan id_kelurahan sesuai nama kolom di database Anda
                                            if (!empty($u['id_kelurahan'])) { 
                                                foreach($kelurahan as $k) {
                                                    if ($k['id_kelurahan'] == $u['id_kelurahan']) { 
                                                        $tampil_kel = $k['nama_kelurahan']; 
                                                        break; 
                                                    }
                                                }
                                            }
                                            echo 'Kel. ' . esc($tampil_kel);
                                        }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary btn-edit me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $u['id_pengguna'] ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-hapus" data-id="<?= $u['id_pengguna'] ?>" data-nama="<?= esc($u['nama_pengguna']) ?>" title="Hapus">
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

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i> Tambah Petugas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/pengguna/simpan') ?>" method="post" id="formTambah">
                <?= csrf_field(); ?>
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_pengguna" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Alamat Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Jabatan</label>
                            <select name="jabatan" class="form-select select-jabatan" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="Admin">Admin</option>
                                <option value="Operator Bank Sampah">Operator Bank Sampah</option>
                                <option value="Operator Kelurahan">Operator Kelurahan</option>
                                <option value="Kepala Kemantren">Kepala Kemantren</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 group-kelurahan" style="display:none;">
                        <label class="small fw-bold">Kelurahan Tugas</label>
                        <select name="kelurahan" class="form-select">
                            <option value="">-- Pilih Kelurahan --</option>
                            <?php if(isset($kelurahan)): foreach($kelurahan as $k): ?>
                                <option value="<?= $k['id_kelurahan'] ?>"><?= esc($k['nama_kelurahan']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-success">Password</label>
                            <input type="password" name="password" id="pass_add" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-success">Konfirmasi</label>
                            <input type="password" id="conf_add" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-success w-100 fw-bold">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($users as $u): ?>
<div class="modal fade" id="modalEdit<?= $u['id_pengguna'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i> Edit Petugas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/pengguna/update') ?>" method="post" class="form-edit">
                <?= csrf_field(); ?>
                <input type="hidden" name="id_pengguna" value="<?= $u['id_pengguna'] ?>">
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_pengguna" class="form-control" value="<?= esc($u['nama_pengguna']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= esc($u['username']) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Jabatan</label>
                            <select name="jabatan" class="form-select select-jabatan-edit">
                                <option value="Admin" <?= $u['jabatan'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="Operator Bank Sampah" <?= $u['jabatan'] == 'Operator Bank Sampah' ? 'selected' : '' ?>>Operator Bank Sampah</option>
                                <option value="Operator Kelurahan" <?= $u['jabatan'] == 'Operator Kelurahan' ? 'selected' : '' ?>>Operator Kelurahan</option>
                                <option value="Kepala Kemantren" <?= $u['jabatan'] == 'Kepala Kemantren' ? 'selected' : '' ?>>Kepala Kemantren</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 group-kelurahan-edit">
                            <label class="small fw-bold">Kelurahan</label>
                            <select name="kelurahan" class="form-select">
                                <option value="">-- Pilih --</option>
                                <?php foreach($kelurahan as $k): ?>
                                    <option value="<?= $k['id_kelurahan'] ?>" <?= (isset($u['id_kelurahan']) && $u['id_kelurahan'] == $k['id_kelurahan']) ? 'selected' : '' ?>>
                                        <?= esc($k['nama_kelurahan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">SIMPAN PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Toggle Kelurahan input based on Jabatan
        $('.select-jabatan').on('change', function() {
            const j = $(this).val();
            if (j === 'Operator Bank Sampah' || j === 'Operator Kelurahan') $('.group-kelurahan').fadeIn();
            else $('.group-kelurahan').fadeOut();
        });
        
        // Edit logic for Kelurahan
        $('.select-jabatan-edit').on('change', function() {
            const j = $(this).val();
            const container = $(this).closest('.row').find('.group-kelurahan-edit');
            if (j === 'Operator Bank Sampah' || j === 'Operator Kelurahan') container.show();
            else container.hide();
        }).trigger('change');

        // Delete Handler
        $('.btn-hapus').on('click', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Petugas?',
                text: "Aksi ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = "<?= base_url('admin/pengguna/hapus/') ?>/" + id;
            });
        });
    });
</script>
<?= $this->endSection() ?>