<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* Desain Elemen Premium */
    .card-custom { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
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
    
    /* Grid Operasional */
    .op-grid-nasabah { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; }
    .op-card-nasabah { border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; padding: 12px 5px; transition: 0.3s; }
    .op-card-nasabah.libur { background-color: #fff5f5; border-color: #fed7d7; }

    /* Lencana Ragam Status */
    .badge-status { font-weight: 700; font-size: 0.75rem; padding: 5px 12px; border-radius: 50px; display: inline-block; }
</style>

<div class="container-fluid px-2">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="me-3 bg-primary text-white p-3 rounded-3 shadow-sm"><i class="fas fa-handshake fa-2x"></i></div>
            <div>
                <h4 class="fw-bold text-dark mb-0">Halo, <span class="text-primary"><?= esc($nasabah['nama_nasabah']) ?></span>! 👋</h4>
                <p class="text-muted small mb-0">Pantau perkembangan saldo, aturan tarif, dan pelacakan jadwal armada jemput Anda secara berkala.</p>
            </div>
        </div>
        
        <div class="d-flex gap-2 flex-wrap">
            <?php 
                // 1. Konversi teks nama kelurahan jika pangkalan data menyimpan kode angka ID
                $nama_kelurahan_tampil = '-';
                if (isset($nasabah['id_kelurahan']) && !empty($nasabah['kelurahan'])) {
                    if (is_numeric($nasabah['kelurahan'])) {
                        $db_check = \Config\Database::connect();
                        $cek_kel = $db_check->table('kelurahan')->where('id', $nasabah['kelurahan'])->get()->getRowArray();
                        $nama_kelurahan_tampil = $cek_kel ? $cek_kel['nama_kelurahan'] : 'Kelurahan ' . $nasabah['kelurahan'];
                    } else {
                        $nama_kelurahan_tampil = $nasabah['id_kelurahan'];
                    }
                }

                // 2. FIX AMAN: Konversi ID RW menjadi Nama Gerakan RW Lengkap Tanpa Operator Syintax Error
                $nama_rw_tampil = '-';
                $rw_mentah = '';
                if (isset($nasabah['id_rw'])) {
                    $rw_mentah = $nasabah['id_rw'];
                } elseif (isset($nasabah['RW'])) {
                    $rw_mentah = $nasabah['RW'];
                }
                
                if (!empty($rw_mentah)) {
                    if (is_numeric($rw_mentah)) {
                        $db_check = \Config\Database::connect();
                       $cek_rw = $db_check->table('rw')->where('id_rw', $rw_mentah)->get()->getRowArray();
                        if ($cek_rw && isset($cek_rw['nama_rw'])) {
                            $nama_rw_tampil = $cek_rw['nama_rw'];
                        } else {
                            $nama_rw_tampil = 'RW ' . $rw_mentah;
                        }
                    } else {
                        $nama_rw_tampil = $rw_mentah;
                    }
                }
            ?>
            <span class="badge bg-light text-primary border shadow-sm fw-bold p-2 px-3" style="border-radius: 8px; font-size: 0.8rem;">
                <i class="fas fa-building text-success me-1"></i> 
                KELURAHAN: <?= isset($nasabah['nama_kelurahan']) ? strtoupper(esc($nasabah['nama_kelurahan'])) : 'BELUM DIATUR' ?>
            </span>

            <span class="badge bg-light text-primary border shadow-sm fw-bold p-2 px-3" style="border-radius: 8px; font-size: 0.8rem;">
                <i class="fas fa-map-pin text-danger me-1"></i> 
                <?= isset($nasabah['nama_rw']) ? strtoupper(esc($nasabah['nama_rw'])) : 'RW -' ?>
            </span>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card card-custom text-white p-3 h-100" style="background: linear-gradient(135deg, #2ecc71, #27ae60);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-uppercase fw-bold opacity-75">Informasi Saldo Anda</div>
                        <h2 class="fw-bold my-2 text-white">Rp <?= number_format($nasabah['saldo'], 0, ',', '.') ?></h2>
                    </div>
                    <i class="fas fa-wallet fa-3x opacity-25"></i>
                </div>
                <hr class="my-2 opacity-25">
                <div class="small d-flex justify-content-between align-items-center">
                    <span>Syarat Minimal Penarikan Tunai:</span>
                    <span class="badge bg-white text-success fw-bold">Rp 50.000</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card card-custom text-white p-3 h-100" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-uppercase fw-bold opacity-75">Total Berat Setoran</div>
                        <h2 class="fw-bold my-2"><?= number_format($total_berat, 2, ',', '.') ?> <span style="font-size: 1.1rem;">Kg</span></h2>
                    </div>
                    <i class="fas fa-scale-balanced fa-3x opacity-25"></i>
                </div>
                <hr class="my-2 opacity-25">
                <div class="small italic opacity-90"><i class="fas fa-leaf me-1"></i> Luar biasa! Anda berkontribusi mengurangi sampah lingkungan.</div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card card-custom p-3 h-100 justify-content-center bg-white" style="border-left: 5px solid #1abc9c !important;">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="<?= base_url('nasabah/minta_jemput') ?>" class="btn btn-primary w-100 py-3 fw-bold shadow-sm d-flex flex-column align-items-center justify-content-center" style="border-radius: 12px; gap: 5px;">
                            <i class="fas fa-truck-pickup fa-lg"></i> <span style="font-size: 0.8rem;">Minta Jemput</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <?php if($bisa_tarik_saldo): ?>
                            <a href="<?= base_url('nasabah/penarikan_saldo') ?>" class="btn btn-warning text-white w-100 py-3 fw-bold shadow-sm d-flex flex-column align-items-center justify-content-center" style="border-radius: 12px; gap: 5px;">
                                <i class="fas fa-hand-holding-usd fa-lg"></i> <span style="font-size: 0.8rem;">Tarik Saldo</span>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 py-3 fw-bold d-flex flex-column align-items-center justify-content-center opacity-50" style="border-radius: 12px; gap: 5px;" onclick="Swal.fire('Informasi','Maaf, tombol pencairan terkunci karena saldo Anda saat ini belum mencapai batas minimal Rp 50.000.','warning')">
                                <i class="fas fa-lock fa-lg"></i> <span style="font-size: 0.8rem;">Tarik Saldo</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom mb-4 bg-white">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-route text-primary me-2"></i> Pelacakan Status & Jadwal Penjemputan Anda</h6>
            <span class="badge bg-light text-muted border small px-3">Real-time Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover mb-0 align-middle text-center">
                    <thead>
                        <tr>
                            <th width="12%">ID Permintaan</th>
                            <th>Tgl Pengajuan</th>
                            <th>Jadwal Penjemputan Lapangan</th>
                            <th>Nama Driver / Petugas</th>
                            <th>Status Proses</th>
                            <th width="25%">Catatan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($status_jemput)): ?>
                            <tr><td colspan="6" class="py-5 text-muted small italic">Anda belum pernah mengajukan permintaan penjemputan sampah.</td></tr>
                        <?php else: foreach($status_jemput as $sj): ?>
                        <tr>
                            <td class="fw-bold text-muted">#PR-<?= $sj['Id_permintaan'] ?></td>
                            <td><?= date('d/m/Y', strtotime($sj['Tanggal_permintaan'])) ?></td>
                            <td>
                                <?php if($sj['Tanggal_jadwal']): ?>
                                    <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($sj['Tanggal_jadwal'])) ?></div>
                                    <small class="badge bg-dark px-2" style="font-size: 0.65rem;"><i class="far fa-clock me-1"></i> <?= substr($sj['Jam_jadwal'], 0, 5) ?> WIB</small>
                                <?php else: ?>
                                    <span class="text-muted small italic">Belum Diagendakan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($sj['nama_petugas']): ?>
                                    <span class="fw-bold text-primary"><i class="fas fa-user-circle me-1"></i> <?= esc($sj['nama_petugas']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $status_proses = isset($sj['Status_penjemputan']) ? strtolower(trim($sj['Status_penjemputan'])) : '';
                                    if ($status_proses === 'diverifikasi') : 
                                ?>
                                    <span class="badge-status bg-success text-white"><i class="fas fa-check-circle me-1"></i> Terjadwal</span>
                                <?php elseif ($status_proses === 'ditunda') : ?>
                                    <span class="badge-status bg-warning text-dark"><i class="fas fa-clock me-1"></i> Ditunda (Reschedule)</span>
                                <?php elseif ($status_proses === 'menunggu') : ?>
                                    <span class="badge-status bg-info text-white"><i class="fas fa-spinner fa-spin me-1"></i> Menunggu Verifikasi</span>
                                <?php elseif ($status_proses === 'selesai') : ?>
                                    <span class="badge-status bg-primary text-white"><i class="fas fa-box me-1"></i> Selesai Dijemput</span>
                                <?php else : ?>
                                    <span class="badge-status bg-danger text-white"><i class="fas fa-times-circle me-1"></i> Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-start px-3 text-muted small italic">
                                <?= isset($sj['Keterangan_admin']) ? esc($sj['Keterangan_admin']) : '<span class="text-muted opacity-50">-</span>' ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card card-custom h-100 bg-white">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-boxes text-info me-2"></i> Informasi Nilai Tukar Jual Sampah</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium table-hover mb-0 align-middle text-center">
                            <thead>
                                <tr>
                                    <th class="text-start px-3">Jenis Sampah Anorganik</th>
                                    <th>Nilai Konversi / Kg</th>
                                    <th>Pembaruan Aturan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($katalog_sampah as $ks): ?>
                                <tr>
                                    <td class="text-start fw-bold text-dark px-3"><i class="fas fa-recycle text-success me-2"></i> <?= esc($ks['katalog_sampah']) ?></td>
                                    <td class="text-success fw-bold">Rp <?= number_format($ks['harga_per_kg'], 0, ',', '.') ?></td>
                                    <td class="text-muted small"><?= $ks['tanggal_berlaku'] ? date('d/m/Y', strtotime($ks['tanggal_berlaku'])) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card card-custom h-100 bg-white">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-tags text-warning me-2"></i> Kebijakan Batas & Jasa Penjemputan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium table-hover mb-0 align-middle text-center">
                            <thead>
                                <tr>
                                    <th class="text-start px-3">Kategori Sampah</th>
                                    <th>Ambang Berat</th>
                                    <th>Biaya Administrasi</th>
                                    <th>Ruang Lingkup</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($tarif_jemput)): ?>
                                    <tr><td colspan="4" class="py-4 text-muted small italic">Belum ada ketentuan ongkos jemput resmi dari admin.</td></tr>
                                <?php else: foreach($tarif_jemput as $tj): ?>
                                <tr>
                                    <td class="text-start fw-bold px-3"><?= esc($tj['katalog_sampah']) ?></td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1"><?= esc($tj['berat_sampah']) ?> Kg</span></td>
                                    <td class="fw-bold text-orange" style="color: #e67e22;">Rp <?= number_format($tj['biaya_penjemputan'], 0, ',', '.') ?></td>
                                    <td class="text-start text-muted small px-2"><?= esc($tj['keterangan'] ?: '-') ?></td>
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