<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Tambahkan Geocoder CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<style>
    #map {
        height: 350px;
        width: 100%;
        border-radius: 12px;
        border: 2px solid #ebedef;
        margin-bottom: 10px;
    }
    .card-custom { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .form-label-premium { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #4a5568; margin-bottom: 6px; }
</style>

<div class="container-fluid px-2">
    <div class="d-flex align-items-center mb-4">
        <div class="me-3 bg-primary text-white p-3 rounded-3 shadow-sm">
            <i class="fas fa-truck-pickup fa-2x"></i>
        </div>
        <div>
            <h4 class="fw-bold text-dark mb-0">Form <b>Permintaan Penjemputan</b></h4>
            <p class="text-muted small mb-0">Tentukan titik presisi, jenis sampah, dan perkiraan berat agar mempermudah persiapan armada menjangkau Anda.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card card-custom p-4 bg-white">
                <form action="<?= base_url('nasabah/minta_jemput/simpan') ?>" method="post">
                    <?= csrf_field(); ?>
                    
                    <input type="hidden" name="latitude" id="lat" value="<?= isset($nasabah['latitude']) ? $nasabah['latitude'] : '-7.7956' ?>">
                    <input type="hidden" name="longitude" id="lng" value="<?= isset($nasabah['longitude']) ? $nasabah['longitude'] : '110.3695' ?>">
                    
                    <div class="mb-3">
                        <label class="form-label-premium">Rencana Tanggal Penjemputan</label>
                        <input type="date" name="tanggal_permintaan" id="tanggal_permintaan" class="form-control form-control-lg" required style="border-radius: 10px; font-size: 0.95rem;">
                        <small class="text-muted italic mt-1 d-block">
                            <i class="fas fa-info-circle text-info me-1"></i> Pengajuan malam hari akan diproses oleh petugas besok pagi.
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label-premium">Jenis Sampah Utama</label>
                            <select name="id_katalog" class="form-select form-select-lg" required style="border-radius: 10px; font-size: 0.95rem;">
                                <option value="" disabled selected>-- Pilih Jenis Sampah --</option>
                                <?php if(isset($katalog_sampah)): foreach($katalog_sampah as $ks): ?>
                                    <option value="<?= $ks['id_katalog'] ?>"><?= $ks['katalog_sampah'] ?> (Rp <?= number_format($ks['harga_per_kg'], 0, ',', '.') ?> / Kg)</option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label-premium">Perkiraan Berat (Kg)</label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="0.1" min="0.1" name="perkiraan_berat" class="form-control" placeholder="0.0" required style="font-size: 0.95rem;">
                                <span class="input-group-text bg-light text-muted small fw-bold" style="border-radius: 0 10px 10px 0; font-size: 0.85rem;">Kg</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-premium"><i class="fas fa-map-marker-alt text-danger me-1"></i> Klik atau Geser Pin pada Peta Rumah Anda</label>
                        <div id="map" class="shadow-sm"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Alamat Lengkap Penjemputan</label>
                        <textarea name="alamat_penjemputan" id="alamat_penjemputan" class="form-control" rows="4" readonly required style="border-radius: 10px; font-size: 0.95rem;" placeholder="Alamat terisi otomatis saat peta diklik..."><?= $nasabah['alamat'] ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= base_url('nasabah/dashboard') ?>" class="btn btn-light px-4 fw-bold text-secondary" style="border-radius: 10px;">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold flex-grow-1 shadow-sm" style="border-radius: 10px; background: #1abc9c; border: none;">
                            <i class="fas fa-paper-plane me-2"></i> KIRIM PERMINTAAN JEMPUT
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card card-custom h-100 bg-white" style="border-left: 5px solid #ffc107 !important;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i> PENTING UNTUK DIINGAT:</h6>
                    <ul class="list-unstyled text-secondary small d-flex flex-column" style="gap: 15px; line-height: 1.5;">
                        <li class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <span>Sistem akan mendeteksi dan menulis ulang detail alamat bangunan Anda secara otomatis setiap kali pin peta diklik atau digeser.</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <span>Pastikan penempatan pin berada tepat di atas akses jalan yang bisa dilalui oleh armada kendaraan penjemput sampah bank sampah.</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <span>Kelompokkan sampah anorganik Anda berdasarkan katalog (Plastik, Kertas, Kaca) agar mempermudah penimbangan petugas lapangan.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Tambahkan Geocoder JS -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    $(document).ready(function() {
        // 1. Set Tanggal minimal hari ini
        const today = new Date().toISOString().split('T')[0];
        $('#tanggal_permintaan').attr('min', today);

        // 2. Ambil koordinat awal nasabah
        let defaultLat = parseFloat($('#lat').val());
        let defaultLng = parseFloat($('#lng').val());

        // 3. Inisialisasi Peta
        var map = L.map('map').setView([defaultLat, defaultLng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // 4. Tambahkan fitur pencarian (Geocoder)
        L.Control.geocoder().addTo(map);

        // 5. Buat Marker
        var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        function updateAlamatOtomatis(lat, lng) {
            $.get(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`, function(data) {
                if(data.display_name) {
                    $('#alamat_penjemputan').val(data.display_name);
                }
            });
        }

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            $('#lat').val(e.latlng.lat);
            $('#lng').val(e.latlng.lng);
            updateAlamatOtomatis(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function() {
            var position = marker.getLatLng();
            $('#lat').val(position.lat);
            $('#lng').val(position.lng);
            updateAlamatOtomatis(position.lat, position.lng);
        });
    });
</script>
<?= $this->endSection() ?>