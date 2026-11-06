<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .card { border-radius: 12px !important; transition: transform 0.2s; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }

    .border-left-primary { border-left: 4px solid #4e73df; }
    .border-left-warning { border-left: 4px solid #f6c23e; }
    .border-left-info    { border-left: 4px solid #36b9cc; }
    .border-left-success { border-left: 4px solid #1cc88a; }
    /* Styling tabel agar bergaris tegas sesuai standar laporan */
    .table thead th {
        vertical-align: middle !important;
        background-color: #f8f9fa;
        color: #4a5568;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        border: 1px solid #e2e8f0 !important;
    }
    .table tbody td { vertical-align: middle !important; border: 1px solid #e2e8f0 !important; font-size: 0.85rem; }
    
    /* Lencana Status Baru Sesuai Alur Dispatch Pusat */
    .status-admin { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 700; font-size: 0.75rem; padding: 4px 12px; border-radius: 50px; }
    .status-aktif { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; font-size: 0.75rem; padding: 4px 12px; border-radius: 50px; }
    .status-ditunda { background-color: #ffedd5; color: #ea580c; border: 1px solid #fed7aa; font-weight: 700; font-size: 0.75rem; padding: 4px 12px; border-radius: 50px; }
</style>

<div class="container-fluid px-2">
    <div class="card card-custom text-white p-4 mb-4" style="background: linear-gradient(135deg, #1abc9c, #16a085); border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        <h2 class="fw-bold mb-1">Halo, <?= esc($operator['nama_pengguna']) ?>! 👋</h2>
        <p class="opacity-90 small mb-4">
            Mari kelola potensi ekonomi dari sampah anorganik di wilayah <span class="fw-bold text-warning">Kelurahan <?= esc($operator['nama_kelurahan']) ?></span> hari ini.
        </p>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-light bg-white bg-opacity-50 text-dark border-0 px-3 py-2" style="border-radius: 50px; font-size: 0.85rem;" disabled>
                <i class="far fa-calendar-alt me-1"></i> <?= date('l, d F Y') ?>
            </button>
        </div>
    </div>

    <!-- NOTIFIKASI SYSTEM -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nasabah Wilayah Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_nasabah ?> Orang</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tugas Jemputan Saya</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $permintaan_masuk ?> Berkas</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sampah Terkumpul</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_berat, 1) ?> Kg</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-box fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalTarikSaldo" style="text-decoration:none;">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan Jasa Jemput</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_pendapatan_jemput, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="modal fade" id="modalTarikSaldo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('operator/tarik_pendapatan') ?>" method="POST">
            <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Tarik Saldo Pendapatan</h5></div>
                    <div class="modal-body">
                        <p>Saldo Tersedia: <strong>Rp <?= number_format($total_pendapatan_jemput, 0, ',', '.') ?></strong></p>
                        <input type="number" name="jumlah_tarik" class="form-control" placeholder="Masukkan jumlah penarikan" max="<?= $total_pendapatan_jemput ?>" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Proses Penarikan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL MANIFEST PENJEMPUTAN (FIXED: MENAMPILKAN SEMUA PENGAJUAN TER-PLOT) -->
    <div class="card card-custom mb-4 bg-white" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-list-alt text-success me-2"></i> Semua Daftar Penugasan Penjemputan Ter-Plot Admin Pusat</h6>
            <span class="badge bg-light text-success border small px-3 fw-bold">Kelurahan <?= esc($operator['nama_kelurahan']) ?> Only</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover mb-0 align-middle text-center">
                    <thead>
                        <tr>
                            <th>Nama Nasabah</th>
                            <th>No. WhatsApp</th>
                            <th>Jadwal Ter-Plot</th>
                            <th>Alamat Rumah Lengkap</th>
                            <th>Status Instruksi</th>
                            <th width="15%">Panduan Navigasi</th>
                            <th width="20%">Konfirmasi Status Lapangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tabel_jemputan)) : ?>
                            <tr><td colspan="7" class="py-5 text-muted small italic">Belum ada tugas penjemputan yang perlu diverifikasi.</td></tr>
                        <?php else : foreach ($tabel_jemputan as $tj) : ?>
                            <tr>
                                <td class="fw-bold text-dark text-start px-3"><?= esc($tj['nama_nasabah']) ?></td>
                                <td><?= esc($tj['nomor_telepon']) ?></td>
                                <td class="fw-bold text-dark">
                                    <?= date('d/m/Y', strtotime($tj['Tanggal_jadwal'])) ?><br>
                                    <small class="text-muted"><?= substr($tj['Jam_jadwal'], 0, 5) ?> WIB</small>
                                </td>
                                <td class="text-start px-3 text-muted small"><?= esc($tj['alamat']) ?></td>
                                <td>
                                    <?php if ($tj['Status_penjemputan'] == 'Aktif') : ?>
                                        <span class="status-aktif"><i class="fas fa-truck fa-spin me-1"></i> Aktif / Penjemputan</span>
                                    <?php elseif ($tj['Status_penjemputan'] == 'Diverifikasi Admin') : ?>
                                        <span class="status-admin"><i class="fas fa-user-shield me-1"></i> Plotted Admin</span>
                                    <?php else : ?>
                                        <span class="status-ditunda"><?= esc($tj['Status_penjemputan']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td width="15%">
                                    <?php if (!empty($tj['latitude']) && !empty($tj['longitude'])) : ?>
                                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $tj['latitude'] ?>,<?= $tj['longitude'] ?>" 
                                        target="_blank" 
                                        class="btn btn-sm btn-outline-danger px-3 fw-bold" 
                                        style="border-radius: 8px; font-size: 0.75rem;">
                                            <i class="fas fa-map-marked-alt me-1"></i> Buka Rute
                                        </a>
                                    <?php else : ?>
                                        <span class="text-muted small italic">Lokasi belum diset</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="<?= base_url('operator/verifikasi_penjemputan') ?>" method="post" class="d-flex gap-1 justify-content-center">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="id_permintaan" value="<?= $tj['Id_permintaan'] ?>">
                                        <select name="status_final" class="form-select form-select-sm" required>
                                            <option value="Diverifikasi">Diverifikasi (Proses)</option>
                                            <option value="Ditunda">Ditunda</option>
                                            <option value="Selesai">Selesai Angkut</option>
                                            <option value="Ditolak">Ditolak</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TABEL KONFIRMASI PENARIKAN SALDO NASABAH -->
    <div class="card card-custom bg-white shadow-sm mb-4" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-handholding-usd text-warning me-2"></i> Konfirmasi Pengajuan Penarikan Saldo Warga</h6>
            <span class="badge bg-light text-warning border small px-3 fw-bold">Membutuhkan Verifikasi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover mb-0 align-middle text-center">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Nasabah</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Saldo Buku Saat Ini</th>
                            <th>Nominal yang Diminta</th>
                            <th width="20%">Aksi Tindakan Operator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($antrean_tarik_saldo)) : ?>
                            <tr>
                                <td colspan="6" class="py-5 text-muted small italic">Tidak ada antrean berkas pengajuan penarikan dana tunai di wilayah Kelurahan ini.</td>
                            </tr>
                        <?php else : $no = 1; foreach ($antrean_tarik_saldo as $ats) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="fw-bold text-dark text-start px-3">
                                    <i class="fas fa-user-circle text-secondary me-2"></i><?= esc($ats['nama_nasabah']) ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($ats['tanggal_pengajuan'])) ?> WIB</td>
                                <td class="text-secondary">Rp <?= number_format($ats['saldo_sekarang'], 0, ',', '.') ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($ats['jumlah_pengajuan'], 0, ',', '.') ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <form action="<?= base_url('operator/verifikasi_penarikan') ?>" method="post" class="form-setuju">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id_pengajuan" value="<?= $ats['id_pengajuan'] ?>">
                                            <input type="hidden" name="status_pengajuan" value="Disetujui">
                                            <button type="button" class="btn btn-sm btn-success fw-bold px-3 btn-aksi-setuju" data-nama="<?= esc($ats['nama_nasabah']) ?>" data-nominal="Rp <?= number_format($ats['jumlah_pengajuan'], 0, ',', '.') ?>" style="border-radius: 8px; font-size: 0.75rem;">
                                                <i class="fas fa-check me-1"></i> Setujui
                                            </button>
                                        </form>

                                        <form action="<?= base_url('operator/verifikasi_penarikan') ?>" method="post" class="form-tolak">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id_pengajuan" value="<?= $ats['id_pengajuan'] ?>">
                                            <input type="hidden" name="status_pengajuan" value="Ditolak">
                                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold px-2 btn-aksi-tolak" data-nama="<?= esc($ats['nama_nasabah']) ?>" style="border-radius: 8px; font-size: 0.75rem;">
                                                <i class="fas fa-times me-1"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // A. Pop-up Konfirmasi untuk Tombol SETUJUI
        $('.btn-aksi-setuju').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('.form-setuju');
            const namaNasabah = $(this).data('nama');
            const nominalTarik = $(this).data('nominal');

            Swal.fire({
                title: 'Setujui Penarikan Saldo?',
                html: `Apakah Anda yakin ingin menyetujui pencairan dana sebesar <b class="text-success">${nominalTarik}</b> untuk nasabah <b>${namaNasabah}</b>?<br><small class="text-muted">Tindakan ini akan memotong saldo rekening nasabah secara otomatis.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Setujui & Cairkan!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    confirmButton: 'fw-bold px-4 py-2 rounded-3',
                    cancelButton: 'px-4 py-2 rounded-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Penarikan...',
                        text: 'Harap tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });

        // B. Pop-up Konfirmasi untuk Tombol TOLAK
        $('.btn-aksi-tolak').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('.form-tolak');
            const namaNasabah = $(this).data('nama');

            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: `Apakah Anda yakin ingin menolak berkas permintaan pencairan saldo milik nasabah ${namaNasabah}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Kembali',
                background: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>