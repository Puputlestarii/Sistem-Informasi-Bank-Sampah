<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* CSS untuk merapikan tabel agar bergaris dan sejajar sempurna */
    .table thead th {
        vertical-align: middle !important;
        padding: 12px 10px !important;
        background-color: #f8f9fa;
        color: #333;
        font-weight: 700;
        border: 1px solid #dee2e6 !important;
    }

    .table tbody td {
        vertical-align: middle !important;
        padding: 12px 10px !important;
        border: 1px solid #dee2e6 !important;
    }

    .biaya-val { 
        color: #e67e22; 
        font-weight: 800; 
        font-size: 1rem; 
    }

    .harga-val {
        color: #27ae60;
        font-weight: 700;
    }

    .item-sampah {
        font-weight: 700;
        color: #2c3e50;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Manajemen <b>Tarif Penjemputan</b></h4>
            <p class="text-muted small mb-0">Kelola standar biaya jasa jemput sampah dari lokasi nasabah.</p>
        </div>
        <button class="btn btn-primary shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTarif">
            <i class="fas fa-plus me-2"></i> Tambah Tarif
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
                            <th width="12%">Minimal Berat</th>
                            <th width="15%">Harga per Kg</th>
                            <th width="15%">Biaya Jemput per Kg</th>
                            <th width="12%">Tanggal Berlaku</th>
                            <th>Keterangan</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($tarif as $t): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $no++ ?></td>
                            <td class="text-start px-4">
                                <div class="item-sampah">
                                    <i class="fas fa-truck-ramp-box text-primary me-2"></i> <?= $t['katalog_sampah'] ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <?= $t['berat_sampah'] ?> Kg
                                </span>
                            </td>
                            <td>
                                <span class="harga-val">Rp <?= number_format($t['harga_per_kg'], 0, ',', '.') ?></span>
                            </td>
                            <td>
                                <span class="biaya-val">Rp <?= number_format($t['biaya_penjemputan'], 0, ',', '.') ?></span>
                            </td>
                            <td>
                                <span class="small fw-bold text-secondary">
                                    <?= $t['tanggal_berlaku'] ? date('d/m/Y', strtotime($t['tanggal_berlaku'])) : '-' ?>
                                </span>
                            </td>
                            <td class="text-start small text-muted px-3">
                                <?= $t['keterangan'] ?: '-' ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary btn-edit-tarif me-1" 
                                            data-id="<?= $t['id_tarif'] ?>" 
                                            data-katalog="<?= $t['id_katalog'] ?>"
                                            data-berat="<?= $t['berat_sampah'] ?>"
                                            data-harga="<?= (int)$t['harga_per_kg'] ?>"
                                            data-biaya="<?= (int)$t['biaya_penjemputan'] ?>"
                                            data-tgl="<?= $t['tanggal_berlaku'] ?>"
                                            data-ket="<?= $t['keterangan'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-hapus-tarif" 
                                            data-id="<?= $t['id_tarif'] ?>" 
                                            data-nama="<?= $t['katalog_sampah'] ?>">
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

<div class="modal fade" id="modalTarif" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-truck me-2"></i> Tambah Tarif Penjemputan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/tarif_penjemputan/simpan') ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Jenis Sampah</label>
                        <select name="id_katalog" class="form-select" required>
                            <option value="">-- Pilih Sampah --</option>
                            <?php if(isset($katalog)): foreach($katalog as $k): ?>
                                <option value="<?= $k['id_katalog'] ?>"><?= $k['katalog_sampah'] ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Minimal Berat (Kg)</label>
                            <input type="number" step="0.01" name="berat_sampah" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Harga per Kg (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_per_kg" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Biaya Jemput (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="biaya_penjemputan" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Tanggal Berlaku</label>
                            <input type="date" name="tanggal_berlaku" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Khusus wilayah RW 05"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-primary px-4 fw-bold w-100 py-2 shadow-sm">SIMPAN TARIF JEMPUT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditTarif" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Tarif Penjemputan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/tarif_penjemputan/update') ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id_tarif" id="edit_id_tarif">
                
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Jenis Sampah</label>
                        <select name="id_katalog" id="edit_id_katalog" class="form-select" required>
                            <option value="">-- Pilih Sampah --</option>
                            <?php if(isset($katalog)): foreach($katalog as $k): ?>
                                <option value="<?= $k['id_katalog'] ?>"><?= $k['katalog_sampah'] ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Minimal Berat (Kg)</label>
                            <input type="number" step="0.01" name="berat_sampah" id="edit_berat_sampah" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Harga per Kg (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_per_kg" id="edit_harga_per_kg" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Biaya Jemput (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="biaya_penjemputan" id="edit_biaya_penjemputan" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Tanggal Berlaku</label>
                            <input type="date" name="tanggal_berlaku" id="edit_tanggal_berlaku" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-info text-white px-4 fw-bold w-100 py-2 shadow-sm">SIMPAN PERUBAHAN TARIF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // TAMBAHAN BARU: Event click untuk mengambil data baris dan menaruhnya ke dalam Modal Edit secara otomatis
        $('.btn-edit-tarif').on('click', function() {
            const id = $(this).data('id');
            const katalog = $(this).data('katalog');
            const berat = $(this).data('berat');
            const harga = $(this).data('harga');
            const biaya = $(this).data('biaya');
            const tgl = $(this).data('tgl');
            const ket = $(this).data('ket');

            // Pengisian value form modal edit harian
            $('#edit_id_tarif').val(id);
            $('#edit_id_katalog').val(katalog);
            $('#edit_berat_sampah').val(berat);
            $('#edit_harga_per_kg').val(harga);
            $('#edit_biaya_penjemputan').val(biaya);
            $('#edit_tanggal_berlaku').val(tgl);
            $('#edit_keterangan').val(ket);

            // Buka Modal Edit secara programmatik
            $('#modalEditTarif').modal('show');
        });

        // Tombol Hapus dengan Konfirmasi SweetAlert
        $('.btn-hapus-tarif').on('click', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Tarif?',
                text: "Tarif jemput untuk '" + nama + "' akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('admin/tarif_penjemputan/hapus') ?>/" + id;
                }
            });
        });

        // Tampilkan Flashdata Success (Bisa menangkap flashdata dari simpan, update, maupun hapus data harian)
        <?php if(session()->getFlashdata('success')): ?>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= session()->getFlashdata('success') ?>', showConfirmButton: false, timer: 1500 });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>