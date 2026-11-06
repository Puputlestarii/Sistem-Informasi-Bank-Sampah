<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; font-size: 12px; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body onload="window.print()"> <div class="header">
        <h3>LAPORAN TRANSAKSI TIMBANGAN</h3>
        <p>Kelurahan: <?= esc($operator['nama_kelurahan']) ?> 
    </div>
    <table>
        <thead>
            <tr>
                <th>ID NOTA</th><th>Tanggal</th><th>Nasabah</th><th>RW</th><th>Layanan</th><th>Kategori</th><th>Berat</th><th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($riwayat_timbang as $rt) : ?>
            <tr>
                <td>#TRX-<?= esc($rt['id_transaksi']) ?></td>
                <td><?= date('d/m/Y', strtotime($rt['tanggal_transaksi'])) ?></td>
                <td><?= esc($rt['nama_nasabah']) ?></td>
                <td><?= isset($rt['nama_rw']) ? esc($rt['nama_rw']) : '-' ?></td>
                <td><?= (!empty($rt['biaya_penjemputan']) && $rt['biaya_penjemputan'] > 0) ? 'Dijemput' : 'Mandiri' ?></td>
                <td><?= esc($rt['katalog_sampah']) ?></td>
                <td><?= number_format($rt['berat_sampah'], 2) ?> Kg</td>
                <td>Rp <?= number_format($rt['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>