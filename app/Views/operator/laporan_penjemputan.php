<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">Laporan <b>Penjemputan Sampah</b></h4>
    </div>

    <div class="card card-custom bg-white shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-truck text-danger me-2"></i> Rekap Potongan Biaya Penjemputan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover mb-0 text-center">
                    <thead>
                        <tr>
                            <th>TGL</th>
                            <th>NASABAH</th>
                            <th>BERAT (KG)</th>
                            <th>BIAYA/KG</th>
                            <th>TOTAL POTONGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_seluruh_potongan = 0;
                        if(empty($laporan)) : ?>
                            <tr><td colspan="5" class="py-4 text-muted">Belum ada data penjemputan.</td></tr>
                        <?php else : foreach($laporan as $l): 
                            $potongan = $l['berat_sampah'] * $l['biaya_penjemputan'];
                            $total_seluruh_potongan += $potongan;
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($l['tanggal_transaksi'])) ?></td>
                            <td class="text-start"><?= esc($l['nama_nasabah']) ?></td>
                            <td><?= number_format($l['berat_sampah'], 2, ',', '.') ?></td>
                            <td>Rp <?= number_format($l['biaya_penjemputan'], 0, ',', '.') ?></td>
                            <td class="text-danger fw-bold">Rp <?= number_format($potongan, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="4" class="text-end">TOTAL KESELURUHAN POTONGAN:</th>
                            <th class="text-danger">Rp <?= number_format($total_seluruh_potongan, 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>