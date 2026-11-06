<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .card-executive { border-radius: 14px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .chart-container { position: relative; margin: auto; height: 260px; width: 260px; }
</style>

<div class="container-fluid px-2">
    <div class="card card-executive text-white p-4 mb-4" style="background: linear-gradient(135deg, #2c3e50, #1a252f);">
        <div class="row align-items-center">
            <div class="col-md-9">
                <h3 class="fw-bold mb-1">E-Monitoring Eksekutif Gerakan MASJOS</h3>
                <p class="opacity-75 small mb-0">Selamat datang, <b><?= esc($pimpinan['nama_pengguna']) ?></b>. Memantau visualisasi grafik kontribusi keaktifan olah sampah wilayah.</p>
            </div>
            <div class="col-md-3 text-end d-none d-md-block">
                <span class="badge bg-warning text-dark fw-bold px-3 py-2" style="font-size: 0.8rem; border-radius: 8px;">LEVEL KEMANTREN</span>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card card-executive p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-primary" style="background-color: #e3f2fd;"><i class="fas fa-scale-balanced"></i></div>
                    <div class="ms-3">
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Massa Terkumpul</small>
                        <h4 class="fw-bold text-dark mb-0"><?= number_format($total_kg, 1, ',', '.') ?> <span class="text-muted" style="font-size: 0.8rem;">Kg</span></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card card-executive p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-success" style="background-color: #e8f5e9;"><i class="fas fa-wallet"></i></div>
                    <div class="ms-3">
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Sirkulasi Saldo</small>
                        <h4 class="fw-bold text-success mb-0">Rp <?= number_format($total_rp, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card card-executive p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-info" style="background-color: #e0f7fa;"><i class="fas fa-users"></i></div>
                    <div class="ms-3">
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Total Anggota</small>
                        <h4 class="fw-bold text-dark mb-0"><?= $total_warga ?> <span class="text-muted" style="font-size: 0.8rem;">Warga</span></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card card-executive p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-warning" style="background-color: #fff3e0;"><i class="fas fa-user-shield"></i></div>
                    <div class="ms-3">
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Aparatur Aktif</small>
                        <h4 class="fw-bold text-dark mb-0"><?= $total_petugas ?> <span class="text-muted" style="font-size: 0.8rem;">Petugas</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card card-executive bg-white p-4 h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list-ol text-success me-2"></i> Matriks Perbandingan Capaian Wilayah Kelurahan</h6>
                <div class="table-responsive">
                    <table class="table table-hover border align-middle text-center mb-0" style="border-radius: 8px; overflow: hidden;">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th class="text-start px-3">Nama Kelurahan</th>
                                <th>Nasabah</th>
                                <th>Transaksi</th>
                                <th>Total Sampah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (empty($laporan_kelurahan)) : 
                            ?>
                                <tr><td colspan="5" class="py-4 text-muted small italic">Belum ada pergerakan data dari wilayah manapun.</td></tr>
                            <?php 
                            else: 
                                $i = 1; 
                                foreach($laporan_kelurahan as $lk): 
                                    // Lewati baris kelurahan jika belum ada nasabah dan belum ada transaksi sama sekali
                                    if ($lk['warga'] == 0 && $lk['transaksi'] == 0) {
                                        continue;
                                    }
                                    
                                    $nama_kel = isset($lk['nama_kelurahan']) ? $lk['nama_kelurahan'] : 'Belum Diatur';
                                    $berat = isset($lk['berat']) ? (float)$lk['berat'] : 0;
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td class="text-start px-3 fw-bold text-secondary"><?= strtoupper(esc($nama_kel)) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= $lk['warga'] ?> Orang</span></td>
                                    <td><?= $lk['transaksi'] ?> Kali</td>
                                    <td class="fw-bold text-success"><?= number_format($berat, 1, ',', '.') ?> Kg</td>
                                </tr>
                            <?php 
                                endforeach; 
                                
                                // Jika setelah difilter ternyata semua baris kelurahan kosong
                                if ($i == 1) {
                                    echo '<tr><td colspan="5" class="py-4 text-muted small italic">Belum ada data setoran aktif dari kelurahan manapun.</td></tr>';
                                }
                            endif; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card card-executive bg-white p-4 h-100 text-center">
                <h6 class="fw-bold text-dark text-start mb-2"><i class="fas fa-chart-pie text-primary me-2"></i> Persentase Kontribusi Olah Sampah</h6>
                <small class="text-muted text-start d-block mb-4">Rasio keaktifan dihitung berdasarkan volume sampah (Kg) terkumpul.</small>
                
                <div class="chart-container d-flex align-items-center justify-content-center">
                    <canvas id="chartKeaktifanKelurahan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Dekode data json kiriman variabel Controller secara aman
        const labelKelurahan = <?= $chart_labels ?>;
        const dataKontribusi = <?= $chart_data ?>;

        // Cek jika data kosong untuk menghindari error render kanvas kosong
        const totalSampah = dataKontribusi.reduce((a, b) => a + b, 0);

        if (totalSampah === 0) {
            // Tampilan alternatif jika data di pangkalan data masih kosong
            $('#chartKeaktifanKelurahan').replaceWith('<p class="text-muted small italic my-5">Belum ada data setoran sampah untuk dikonversi menjadi grafik.</p>');
            return;
        }

        // Inisialisasi Rendering Chart.js Doughnut Type
        const ctx = document.getElementById('chartKeaktifanKelurahan').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labelKelurahan,
                datasets: [{
                    data: dataKontribusi,
                    backgroundColor: [
                        '#1abc9c', // Hijau Tosca (Kricak)
                        '#3498db', // Biru (Tegalrejo)
                        '#e67e22', // Orange 
                        '#9b59b6', // Ungu
                        '#f1c40f'  // Kuning
                    ],
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, weight: '600' },
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let percentage = ((value / totalSampah) * 100).toFixed(1);
                                return `${label}: ${value.toLocaleString('id-ID')} Kg (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%' // Membuat lingkaran tengah donat lebih elegan
            }
        });
    });
</script>
<?= $this->endSection() ?>