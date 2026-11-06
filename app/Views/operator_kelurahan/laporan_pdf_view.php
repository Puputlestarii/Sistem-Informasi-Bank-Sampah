<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Laporan Bulanan RW</title>
    <style>
        body { font-family: 'Segoe UI', Helvetica, Arial, sans-serif; color: #333; line-height: 1.4; padding: 20px; background-color: #fff; }
        
        /* Kop Surat dengan Flexbox untuk logo di kiri */
        .kop-surat { 
        display: flex; 
        align-items: center; /* Menjaga vertikal center */
        justify-content: flex-start; /* Menggeser ke arah kiri */
        border-bottom: 3px solid #000; 
        padding-bottom: 10px; 
        margin-bottom: 20px; 
        }
        .logo-img { 
            width: 80px; 
            height: auto; 
            margin-right: 15px; /* Jarak antara logo dan teks */
        }
        .teks-kop { 
            flex-grow: 1; 
            text-align: center; /* Teks tetap ingin di tengah, tapi posisinya menyesuaikan logo */
            margin-right: 95px; /* Margin ini menyeimbangkan ukuran logo agar teks tetap di tengah */
        }
        .kop-surat h3 { margin: 0; text-transform: uppercase; font-size: 1.2rem; letter-spacing: 0.5px; }
        .kop-surat h2 { margin: 5px 0; text-transform: uppercase; font-size: 1.5rem; color: #2c3e50; }
        .kop-surat p { margin: 0; font-size: 0.85rem; color: #7f8c8d; }
        
        .judul-laporan { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 25px; font-size: 1.1rem; color: #2c3e50; line-height: 1.5; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { background-color: #00e676 !important; color: #ffffff !important; font-weight: bold; padding: 12px; border: 1px solid #e2e8f0; font-size: 0.85rem; text-transform: uppercase; text-align: center; }
        table td { padding: 12px; border: 1px solid #e2e8f0; font-size: 0.9rem; text-align: center; color: #2d3748; }
        
        .bg-row-title { background-color: #e8f5e9 !important; font-weight: 800; color: #1b5e20; text-align: left; padding-left: 20px !important; }
        .total-row { background-color: #616161 !important; color: #ffffff !important; font-weight: 700; }
        .text-start { text-align: left; }
        .text-success { color: #2e7d32; font-weight: bold; }
        
        .ttd-container { float: right; width: 280px; text-align: center; margin-top: 30px; }
        .ttd-space { height: 70px; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <img src="<?= base_url('images/logo_pemkot_yk.png') ?>" alt="Logo MasJOS" class="logo-img">
        
        <div class="teks-kop">
            <h3>Pemerintah Kota Yogyakarta</h3>
            <h2>Laporan Eksekutif Gerakan MASJOS Kemantren</h2>
            <p>Gedung Kompleks Sektor Administrasi Wilayah Kota Yogyakarta, DI Yogyakarta</p>
        </div>
    </div>

    <div class="judul-laporan">
        REKAPITULASI PEMBUKUAN TRANSAKSI TIMBANGAN BANK SAMPAH PER RW<br>
        PERIODE BULAN: <?= esc($bulan_teks) ?> <?= esc($tahun_pilihan) ?>
    </div>

    <table>
        <thead>
            <tr>
                <th width="7%">No</th>
                <th class="text-left">Wilayah Kerja (RW)</th>
                <th width="18%">Total Transaksi</th>
                <th width="18%">Total Berat (Kg)</th>
                <th width="22%">Total Saldo Masuk</th>
                <th width="15%">Nasabah Aktif</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            $sum_tx = 0; $sum_berat = 0; $sum_saldo = 0; $sum_nasabah = 0;
            if (empty($rekap_rw)) : 
            ?>
                <tr><td colspan="6" style="padding: 20px; color: #7f8c8d; font-style: italic;">Belum ada data rekaman transaksi timbangan ter-input pada periode ini.</td></tr>
            <?php 
            else : 
                foreach ($rekap_rw as $r) : 
                    $tx = $r['total_transaksi'] ?: 0;
                    $berat = $r['total_berat'] ?: 0;
                    $saldo = $r['total_saldo'] ?: 0;
                    $nasabah = $r['nasabah_aktif'] ?: 0;

                    $sum_tx += $tx;
                    $sum_berat += $berat;
                    $sum_saldo += $saldo;
                    $sum_nasabah += $nasabah;

                    // Menggunakan nama_rw jika ada, jika tidak gunakan nomor RW
                    $namaTampilRW = !empty($r['nama_rw']) ? esc($r['nama_rw']) : 'RW ' . esc($r['rw']);
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td class="text-left" style="font-weight: bold; padding-left: 15px;"><?= $namaTampilRW ?></td>
                    <td><?= $tx ?> Kali</td>
                    <td><?= number_format($berat, 2, ',', '.') ?> Kg</td>
                    <td class="text-right" style="color: #27ae60; font-weight: bold; padding-right: 15px;">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
                    <td><?= $nasabah ?> Orang</td>
                </tr>
            <?php 
                endforeach; 
            ?>
                <tr class="total-row">
                    <td colspan="2" class="text-right" style="padding-right: 15px;">TOTAL KESELURUHAN:</td>
                    <td><?= $sum_tx ?> Kali</td>
                    <td><?= number_format($sum_berat, 2, ',', '.') ?> Kg</td>
                    <td class="text-right" style="padding-right: 15px;">Rp <?= number_format($sum_saldo, 0, ',', '.') ?></td>
                    <td><?= $sum_nasabah ?> Orang</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd-container">
        <p>Yogyakarta, <?= date('d M Y') ?></p>
        <p style="font-weight: bold; margin-top: -5px;">Operator Kelurahan <?= esc($nama_kelurahan) ?></p>
        <div class="ttd-space"></div>
        <p style="font-weight: bold; text-decoration: underline;"><?= esc($operator['nama']) ?></p>
        <p style="font-size: 0.8rem; color: #7f8c8d; margin-top: -10px;">NIP/ID: PETUGAS-<?= esc($operator['id_pengguna']) ?></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>