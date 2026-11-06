<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* Styling Kloning Sempurna SI-BANTER / MASJOS Premium */
    .card-premium-report { border-radius: 12px; border: none; box-shadow: 0 4px 25px rgba(0,0,0,0.06); overflow: hidden; }
    .table-banter { border: 1px solid #e2e8f0; }
    .table-banter thead th {
        background-color: #00e676 !important; /* Warna Hijau Terang SI-BANTER */
        color: #ffffff !important;
        font-weight: 700;
        text-transform: none;
        font-size: 0.85rem;
        padding: 12px;
        border: 1px solid #e2e8f0 !important;
    }
    .table-banter tbody td { vertical-align: middle !important; padding: 12px; border: 1px solid #e2e8f0 !important; font-size: 0.9rem; color: #2d3748; }
    .bg-row-title { background-color: #e8f5e9 !important; font-weight: 800; color: #1b5e20; text-align: left; padding-left: 20px !important; }
    .total-row { background-color: #616161 !important; color: #ffffff !important; font-weight: 700; }
</style>

<div class="container-fluid px-2">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Laporan Keseluruhan Wilayah</h3>
    </div>

    <div class="card card-premium-report bg-white p-4">
        <form action="<?= base_url('kemantren/laporan') ?>" method="get" class="row g-2 align-items-center mb-4">
            <div class="col-auto">
                <span class="fw-bold text-muted small"><i class="fas fa-filter me-1"></i> Filter:</span>
            </div>
            <div class="col-auto">
                <select name="id_kelurahan" class="form-select form-select-sm fw-bold border-primary" style="width: 180px;">
                    <?php foreach($daftar_kelurahan as $dk): ?>
                        <option value="<?= $dk['id_kelurahan'] ?>" <?= $id_kelurahan_pilihan == $dk['id_kelurahan'] ? 'selected' : '' ?>><?= esc($dk['nama_kelurahan']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <select name="bulan" class="form-select form-select-sm fw-bold" style="width: 130px;">
                    <?php 
                    $months = [
                        '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                        '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                    ];
                    foreach($months as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $bulan_pilihan == $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm fw-bold" style="width: 100px;">
                    <option value="2025" <?= $tahun_pilihan == '2025' ? 'selected' : '' ?>>2025</option>
                    <option value="2026" <?= $tahun_pilihan == '2026' ? 'selected' : '' ?>>2026</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-success px-3 fw-bold" style="background-color: #00c853; border: none; height: 31px;">
                    <i class="fas fa-search me-1"></i> Tampilkan
                </button>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('kemantren/cetak_pdf?id_kelurahan=' . $id_kelurahan_pilihan . '&bulan=' . $bulan_pilihan . '&tahun=' . $tahun_pilihan) ?>" target="_blank" class="btn btn-sm btn-danger fw-bold px-3 shadow-sm" style="height: 31px; line-height: 20px;">
                    <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-banter text-center mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th class="text-start px-4">Wilayah (RW)</th>
                        <th>Total Transaksi</th>
                        <th>Total Berat</th>
                        <th>Total Saldo Masuk</th>
                        <th>Nasabah Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="bg-row-title">KELURAHAN <?= strtoupper(esc($nama_kelurahan_aktif)) ?></td>
                    </tr>

                    <?php 
                    $sum_tx = 0; $sum_berat = 0; $sum_saldo = 0; $sum_nasabah = 0;
                    if (empty($rekap_rw)) : 
                    ?>
                        <tr><td colspan="6" class="py-5 text-muted small italic">Belum ada pembukuan transaksi timbangan pada kelurahan ini di periode terpilih.</td></tr>
                    <?php 
                    else : 
                        $i = 1; 
                        foreach($rekap_rw as $r): 
                            $tx_val      = isset($r['total_transaksi']) ? (int)$r['total_transaksi'] : 0;
                            $berat_val   = isset($r['total_berat']) ? (float)$r['total_berat'] : 0;
                            $saldo_val   = isset($r['total_saldo']) ? (int)$r['total_saldo'] : 0;
                            $nasabah_val = isset($r['nasabah_aktif']) ? (int)$r['nasabah_aktif'] : 0;

                            $sum_tx      += $tx_val;
                            $sum_berat   += $berat_val;
                            $sum_saldo   += $saldo_val;
                            $sum_nasabah += $nasabah_val;
                    ?>
                        <tr>
                            <td class="text-muted fw-bold"><?= $i++ ?></td>
                            
                            <td class="text-start px-4 fw-bold">
                                <?php 
                                    // Pastikan key ada sebelum diakses
                                    if (isset($r['nama_rw']) && !empty($r['nama_rw'])) {
                                        echo esc($r['nama_rw']);
                                    } else {
                                        // Gunakan fallback jika nama_rw kosong
                                        $id_rw_val = isset($r['id_rw']) ? $r['id_rw'] : (isset($r['rw']) ? $r['rw'] : 'Unknown');
                                        echo 'RW ' . esc($id_rw_val);
                                    }
                                ?>
                            </td>
                            
                            <td><?= $tx_val ?> Kali</td>
                            <td><?= number_format($berat_val, 2, '.', ',') ?> Kg</td>
                            <td class="text-success fw-bold">Rp <?= number_format($saldo_val, 0, ',', '.') ?></td>
                            <td><?= $nasabah_val ?> Orang</td>
                        </tr>
                    <?php 
                        endforeach; 
                    endif; 
                    ?>
                    
                    <tr class="total-row">
                        <td colspan="2" class="text-end fw-bold text-uppercase px-4">TOTAL KESELURUHAN:</td>
                        <td><?= $sum_tx ?> Kali</td>
                        <td><?= number_format($sum_berat, 2, '.', ',') ?> Kg</td>
                        <td>Rp <?= number_format($sum_saldo, 0, ',', '.') ?></td>
                        <td><?= $sum_nasabah ?> Orang</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>