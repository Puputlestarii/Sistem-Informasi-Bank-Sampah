<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Nasabah - MasJOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <style>
        body { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh; display: flex; align-items: center; color: #333; font-family: 'Segoe UI', sans-serif; }
        .register-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: #ffffff; }
        .register-header { text-align: center; padding: 30px 20px 10px; }
        .btn-register { background: linear-gradient(to right, #2c3e50, #1a252f); border: none; color: white; font-weight: bold; padding: 12px; border-radius: 10px; width: 100%; }
        .btn-register:hover { background: #000; color: white; }
        #map { height: 300px; width: 100%; border-radius: 12px; border: 2px solid #ebedef; margin-bottom: 10px; }
        .password-group { position: relative; }
        .password-toggle { position: absolute; right: 25px; top: 40px; cursor: pointer; color: #999; }
        .section-title { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #1abc9c; margin: 20px 0; display: flex; align-items: center; }
        .section-title::after { content: ""; flex: 1; height: 1px; background: #eee; margin-left: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card register-card my-5">
                <div class="register-header">
                    <h3 class="fw-bold mb-1">Daftar Nasabah</h3>
                    <p class="text-muted small">Bergabunglah dengan gerakan <b>MasJOS</b> hari ini</p>
                </div>

                <div class="card-body p-4 p-md-5 pt-md-2">
                    <form action="<?= base_url('auth/simpan_register') ?>" method="post" id="regForm">
                        <?= csrf_field(); ?>
                        
                        <div class="section-title">Informasi Pribadi</div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_nasabah" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3 password-group">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                                <i class="fas fa-eye password-toggle" id="togglePass"></i>
                            </div>
                            <div class="col-md-6 mb-3 password-group">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                                <i class="fas fa-eye password-toggle" id="togglePassConfirm"></i>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="nomor_telepon" class="form-control" required>
                            </div>
                        </div>

                        <div class="section-title">Lokasi & Alamat</div>
                        <div class="col-md-12 mb-2">
                            <div id="map"></div>
                        </div>
                        <input type="hidden" name="latitude" id="lat">
                        <input type="hidden" name="longitude" id="lng">
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" id="alamat" class="form-control" rows="2" readonly required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelurahan</label>
                                <select name="id_kelurahan" id="id_kelurahan" class="form-select" required onchange="loadRW(this.value)">
                                    <option value="">-- Pilih Kelurahan --</option>
                                    <?php foreach($kelurahan as $k) : ?>
                                        <option value="<?= $k['id_kelurahan'] ?>"><?= $k['nama_kelurahan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">RW</label>
                                <select name="rw" id="rw" class="form-select" required>
                                    <option value="">-- Pilih RW --</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-register mt-3">SELESAIKAN PENDAFTARAN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    // Toggle Password
    $('#togglePass').on('click', function() {
        const input = $('#password');
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    });

    // Fungsi Load RW via AJAX
    function loadRW(id_kelurahan) {
        if (!id_kelurahan) {
            $('#rw').html('<option value="">-- Pilih RW --</option>');
            return;
        }
        $.get("<?= base_url('auth/get_rw_by_kelurahan/') ?>/" + id_kelurahan, function(data) {
            let options = '<option value="">-- Pilih RW --</option>';
            data.forEach(item => {
                options += `<option value="${item.id_rw}">${item.nama_rw}</option>`;
            });
            $('#rw').html(options);
        });
    }

    // Peta Leaflet
    var map = L.map('map').setView([-7.7956, 110.3695], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.Control.geocoder().addTo(map);

    var marker;
    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        $('#lat').val(e.latlng.lat);
        $('#lng').val(e.latlng.lng);
        $.get(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`, function(data){
            $('#alamat').val(data.display_name);
        });
    });
</script>
</body>
</html>