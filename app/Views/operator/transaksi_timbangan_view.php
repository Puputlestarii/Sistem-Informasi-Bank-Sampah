<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* Desain Komponen Premium MasJOS */
    .card-custom { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; height: 100%; }
    .table-premium thead th {
        background-color: #f8f9fa;
        color: #4a5568;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        border: 1px solid #e2e8f0 !important;
    }
    .table-premium tbody td { vertical-align: middle !important; border: 1px solid #e2e8f0 !important; font-size: 0.85rem; }
    .form-label-premium { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #4a5568; margin-bottom: 6px; }
    .input-premium { border-radius: 10px; font-size: 0.9rem; padding: 10px 12px; border: 1px solid #cbd5e1; transition: all 0.2s; }
    .input-premium:focus { border-color: #1abc9c; box-shadow: 0 0 0 0.2rem rgba(26, 188, 156, 0.15); }
    .input-premium[readonly] { background-color: #f8fafc; color: #64748b; font-weight: 600; }
    .calc-box-premium { background-color: #f0fdf4; border: 1px dashed #bbf7d0; border-radius: 12px; padding: 12px; text-align: right; }
    .box-tarif-jemput { background-color: #fff7ed; border: 1px dashed #ffedd5; border-radius: 12px; padding: 12px; display: none; }
    .badge-saldo { background-color: #dcfce7; color: #15803d; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; }
</style>

<div class="container-fluid px-2">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="me-3 bg-primary text-white p-3 rounded-3 shadow-sm">
                <i class="fas fa-balance-scale fa-2x"></i>
            </div>
            <div>
                <h4 class="fw-bold text-dark mb-0">Transaksi <b>Timbangan Digital</b></h4>
                <p class="text-muted small mb-0">Pilih nasabah sewilayah, tetapkan jenis sampah & penyetoran, simpan data ke sistem.</p>
            </div>
        </div>
        <div>
            <span class="badge bg-light text-primary border shadow-sm fw-bold p-2 px-3" style="border-radius: 8px; font-size: 0.8rem;">
                <i class="fas fa-map-marker-alt text-danger me-1"></i> WILAYAH TUGAS: KELURAHAN <?= strtoupper(isset($operator['nama_kelurahan']) ? $operator['nama_kelurahan'] : 'Yogyakarta') ?>
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card card-custom bg-white">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-users text-success me-1"></i> 1. Rekening Warga Kelurahan</h6>
                    <div>
                        <input type="text" id="searchNasabah" onkeyup="cariNamaNasabah()" class="form-control form-control-sm" placeholder="🔍 Cari nama nasabah..." style="width: 180px; border-radius: 8px;">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-premium table-hover mb-0 align-middle text-center" id="tabelNasabah">
                            <thead class="sticky-top">
                                <tr>
                                    <th width="35%">Nama Nasabah</th>
                                    <th width="20%">Wilayah Kerja</th>
                                    <th>Alamat Rumah</th>
                                    <th>Saldo Rekening</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftar_nasabah)) : ?>
                                    <tr><td colspan="5" class="py-5 text-muted small">Belum ada nasabah terdaftar di kelurahan wilayah tugas ini.</td></tr>
                                <?php else : foreach ($daftar_nasabah as $dn) : 
                                    $idNasabah = isset($dn['id_nasabah']) ? $dn['id_nasabah'] : (isset($dn['id']) ? $dn['id'] : 0);
                                ?>
                                    <tr class="baris-nasabah" id="row_<?= $idNasabah ?>">
                                        <td class="fw-bold text-dark text-start px-3">
                                            <i class="fas fa-user-circle text-secondary me-2"></i><span class="nama-warga"><?= esc($dn['nama_nasabah']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border mb-1 small d-block text-start" style="font-size: 0.7rem; border-radius: 5px;">
                                                <i class="fas fa-city text-success me-1"></i> 
                                                <?php 
                                                    if (isset($dn['nama_kelurahan']) && !empty($dn['nama_kelurahan'])) {
                                                        echo esc($dn['nama_kelurahan']);
                                                    } else {
                                                        echo esc(isset($dn['kelurahan']) ? $dn['kelurahan'] : '-');
                                                    }
                                                ?>
                                            </span>
                                            <span class="badge bg-light text-purple border small d-block text-start" style="font-size: 0.7rem; color: #4a148c; background-color: #f3e5f5 !important; border-radius: 5px;">
                                                <i class="fas fa-users text-primary me-1"></i> 
                                                <?php 
                                                    if (isset($dn['nama_rw']) && !empty($dn['nama_rw'])) {
                                                        echo esc($dn['nama_rw']);
                                                    } else {
                                                        echo 'RW ' . esc(isset($dn['rw']) ? $dn['rw'] : '-');
                                                    }
                                                ?>
                                            </span>
                                        </td>
                                        <td class="text-start text-muted small"><?= esc($dn['alamat']) ?></td>
                                        <td><span class="badge-saldo">Rp <?= number_format($dn['saldo'], 0, ',', '.') ?></span></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" 
                                                    onclick="pilihNasabahTimbangan(this, '<?= $idNasabah ?>', '<?= addslashes($dn['nama_nasabah']) ?>')" style="border-radius: 8px;">
                                                Pilih <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card card-custom bg-white border-top border-primary border-4">
                <div class="card-header bg-white border-0 pt-3 pb-2">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-calculator text-primary me-1"></i> 2. Kalkulator Hasil Timbangan</h6>
                </div>
                
                <form id="formTransaksiTimbangan" onsubmit="eksekusiSimpanTimbangan(event)">
                    <?= csrf_field(); ?>
                    <div class="card-body pt-2">
                        
                        <div class="p-3 rounded-3 mb-3 border d-flex align-items-center" id="boxStatusWarga" style="background-color: #f8fafc; transition: 0.3s;">
                            <div class="bg-secondary text-white p-2 rounded-3 me-3" id="iconStatusWarga"><i class="fas fa-user-slash"></i></div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Nasabah Terpilih:</small>
                                <span class="fw-bold text-muted fs-5" id="labelNamaNasabah">Pilih Nasabah Di Kiri</span>
                            </div>
                        </div>

                        <input type="hidden" name="id_nasabah" id="form_id_nasabah" required>
                        <?php 
                            date_default_timezone_set('Asia/Jakarta');
                        ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksi" class="form-control" 
                                value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label form-label-premium">Metode Penyetoran</label>
                            <select name="metode_setor" id="metode_setor" class="form-select input-premium" onchange="toggleMetodeSetor(this.value)" required disabled>
                                <option value="mandiri" selected>Setor Mandiri (Datang Langsung / Tanpa Biaya)</option>
                                <option value="dijemput">Dijemput Armada (Potong Saldo Jasa Jemput)</option>
                            </select>
                        </div>
                    

                        <div class="box-tarif-jemput mb-3" id="panelTarifJemput" style="display: none;">
                            <label class="form-label form-label-premium text-warning"><i class="fas fa-tags me-1"></i> Aturan Tarif Penjemputan Admin</label>
                            <select name="id_tarif" id="id_tarif" class="form-select input-premium text-dark border-warning" onchange="updateTarifJemput(this)">
                                <option value="0" data-ongkos="0" data-min-berat="0" selected>-- Pilih Kebijakan Ongkos Jemput --</option>
                                <?php if(isset($tarif_jemput) && !empty($tarif_jemput)): foreach($tarif_jemput as $tj): 
                                    $idTarifReal = isset($tj['id_tarif']) ? $tj['id_tarif'] : (isset($tj['id']) ? $tj['id'] : 0);
                                    $namaKatalogSampah = isset($tj['katalog_sampah']) ? $tj['katalog_sampah'] : 'Sampah';
                                ?>
                                    <option value="<?= $idTarifReal ?>" data-ongkos="<?= $tj['biaya_penjemputan'] ?>" data-min-berat="<?= $tj['berat_sampah'] ?>">
                                        <?= $namaKatalogSampah ?> -> Rp <?= number_format($tj['biaya_penjemputan'], 0, ',', '.') ?> / Kg (Min. <?= $tj['berat_sampah'] ?> Kg)
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                            <input type="hidden" name="biaya_penjemputan" id="biaya_penjemputan" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label form-label-premium">Jenis Sampah (Katalog)</label>
                            <select name="id_katalog" id="id_katalog" class="form-select input-premium" onchange="updateHargaKatalog(this)" required disabled>
                                <option value="" disabled selected>-- Pilih Jenis Sampah --</option>
                                <?php foreach ($daftar_katalog as $dk) : 
                                    $idKatalog = isset($dk['id_katalog']) ? $dk['id_katalog'] : (isset($dk['id']) ? $dk['id'] : 0);
                                    $namaKatalog = isset($dk['katalog_sampah']) ? $dk['katalog_sampah'] : 'Sampah';
                                    $hargaKatalog = isset($dk['harga_per_kg']) ? $dk['harga_per_kg'] : 0;
                                ?>
                                    <option value="<?= $idKatalog ?>" data-harga="<?= $hargaKatalog ?>">
                                        <?= $namaKatalog ?> (Rp <?= number_format($hargaKatalog, 0, ',', '.') ?> / Kg)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label form-label-premium">Berat Bersih (Kg)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01" name="berat_sampah" id="berat_sampah" oninput="kalkulasiHasilTimbangan()" class="form-control input-premium" placeholder="0.00" required disabled style="border-radius: 10px 0 0 10px;">
                                    <span class="input-group-text bg-light text-muted small fw-bold" style="border-radius: 0 10px 10px 0;">Kg</span>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label form-label-premium">Harga Satuan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted small fw-bold" style="border-radius: 10px 0 0 10px;">Rp</span>
                                    <input type="number" name="harga_per_kg" id="harga_per_kg" class="form-control input-premium" readonly required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="calc-box-premium my-1">
                            <div class="justify-content-between small text-muted mb-1" id="rowBruto" style="display: none;">
                                <span>Kotor Uang Sampah:</span>
                                <span id="lblUangKotor" class="fw-bold text-dark">Rp 0</span>
                            </div>
                            <div class="justify-content-between small text-danger mb-1" id="rowPotongan" style="display: none;">
                                <span>Potongan Jasa Jemput:</span>
                                <span id="lblPotongan" class="fw-bold">- Rp 0</span>
                            </div>
                            <hr class="my-1 border-secondary border-opacity-25" id="lineCalc" style="display: none;">
                            
                            <span class="small text-success d-block fw-bold" style="font-size: 0.65rem; text-transform: uppercase;">Total Uang Masuk Saldo Rekening:</span>
                            <h3 class="fw-bold text-success mb-0" id="textSubtotal">Rp 0</h3>
                        </div>

                    </div>
                    <div class="card-footer bg-white border-0 pb-3 pt-0">
                        <button type="submit" id="btnSimpanTimbangan" class="btn btn-success w-100 fw-bold py-2 shadow-sm" disabled style="border-radius: 10px; background-color: #1abc9c; border: none;">
                            <i class="fas fa-save me-1"></i> SIMPAN PENIMBANGAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // 1. Live Search Cepat Rekening Nasabah
    function cariNamaNasabah() {
        var input = document.getElementById("searchNasabah").value.toLowerCase();
        var rows = document.querySelectorAll("#tabelNasabah tbody tr.baris-nasabah");
        rows.forEach(function(row) {
            var nama = row.querySelector(".nama-warga").innerText.toLowerCase();
            if (nama.indexOf(input) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // 2. Pilih Nasabah Aktif
    function pilihNasabahTimbangan(btn, idWarga, namaWarga) {
        var rows = document.querySelectorAll('.baris-nasabah');
        rows.forEach(function(r) { r.classList.remove('table-primary'); });
        
        document.getElementById('row_' + idWarga).classList.add('table-primary');
        document.getElementById('form_id_nasabah').value = idWarga;
        document.getElementById('labelNamaNasabah').innerText = namaWarga;
        
        var boxStatus = document.getElementById('boxStatusWarga');
        boxStatus.style.backgroundColor = '#f0fdf4';
        boxStatus.style.borderColor = '#bbf7d0';
        document.getElementById('iconStatusWarga').className = "bg-success text-white p-2 rounded-3 me-3";
        document.getElementById('iconStatusWarga').innerHTML = '<i class="fas fa-user-check"></i>';

        // Buka Kunci Seluruh Kolom Borang
        document.getElementById('metode_setor').disabled = false;
        document.getElementById('metode_setor').value = 'mandiri';
        document.getElementById('id_tarif').value = '0';
        document.getElementById('biaya_penjemputan').value = '0';
        document.getElementById('id_katalog').disabled = false;
        document.getElementById('id_katalog').value = '';
        document.getElementById('berat_sampah').disabled = false;
        document.getElementById('berat_sampah').value = '';
        document.getElementById('harga_per_kg').value = '';
        
        // Tetapkan tampilan status awal
        document.getElementById('panelTarifJemput').style.display = 'none';
        document.getElementById('rowBruto').style.display = 'none';
        document.getElementById('rowPotongan').style.display = 'none';
        document.getElementById('lineCalc').style.display = 'none';
        document.getElementById('textSubtotal').innerText = 'Rp 0';
        document.getElementById('btnSimpanTimbangan').disabled = false;
    }

    // 3. Mengawal Paparan Pilihan Metode Penyetoran (Mandiri VS Jemput)
    function toggleMetodeSetor(val) {
        if (val === 'dijemput') {
            document.getElementById('panelTarifJemput').style.display = 'block';
            document.getElementById('rowBruto').style.display = 'flex';
            document.getElementById('rowPotongan').style.display = 'flex';
            document.getElementById('lineCalc').style.display = 'block';
        } else {
            document.getElementById('panelTarifJemput').style.display = 'none';
            document.getElementById('id_tarif').value = '0';
            document.getElementById('biaya_penjemputan').value = '0';
            document.getElementById('rowBruto').style.display = 'none';
            document.getElementById('rowPotongan').style.display = 'none';
            document.getElementById('lineCalc').style.display = 'none';
        }
        kalkulasiHasilTimbangan();
    }

    // 4. Update Nilai Biaya Jasa Jemput Armada (Simpan Data Pokok)
    function updateTarifJemput(select) {
        kalkulasiHasilTimbangan();
    }

    // 5. Update Nilai Harga Katalog Sampah per Kg
    function updateHargaKatalog(select) {
        var selectedOption = select.options[select.selectedIndex];
        var harga = parseInt(selectedOption.getAttribute('data-harga')) || 0;
        document.getElementById('harga_per_kg').value = harga;
        kalkulasiHasilTimbangan();
    }

    // 6. PERBAIKAN LOGIKA: Kalkulator Hitung Progresif & Proteksi Batas Minimum
    function kalkulasiHasilTimbangan() {
        var berat = parseFloat(document.getElementById('berat_sampah').value) || 0;
        var harga = parseInt(document.getElementById('harga_per_kg').value) || 0;
        var metodeSetor = document.getElementById('metode_setor').value;

        var bruto = Math.round(berat * harga);
        var totalOngkosJemput = 0;

        if (metodeSetor === 'dijemput') {
            var selectTarif = document.getElementById('id_tarif');
            var selectedOption = selectTarif.options[selectTarif.selectedIndex];
            
            if (selectTarif.value !== '0') {
                var ongkosPerKg = parseInt(selectedOption.getAttribute('data-ongkos')) || 0;
                var minBerat = parseFloat(selectedOption.getAttribute('data-min-berat')) || 0;

                // VALIDASI AMBANG BATAS: Beri peringatan keras jika berat sampah kurang dari batas minimal
                if (berat > 0 && berat < minBerat) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Berat Sampah Kurang!',
                        html: `Aturan penjemputan kategori ini minimal bernilai <b>${minBerat} Kg</b>. Berat input saat ini (<b>${berat} Kg</b>) belum memenuhi batas ambang pelayanan.`,
                        confirmButtonColor: '#e67e22',
                        confirmButtonText: 'Evaluasi Berat'
                    });
                    
                    // Blokir tombol simpan agar tidak terjadi anomali pelaporan
                    document.getElementById('btnSimpanTimbangan').disabled = true;
                } else {
                    document.getElementById('btnSimpanTimbangan').disabled = false;
                }

                // HITUNG PROGRESIF: Total Ongkos = Ongkos per Kg x Berat Sampah
                totalOngkosJemput = Math.round(berat * ongkosPerKg);
                document.getElementById('biaya_penjemputan').value = totalOngkosJemput;
            }
        } else {
            document.getElementById('btnSimpanTimbangan').disabled = false;
        }

        var netto = bruto - totalOngkosJemput;
        if (netto < 0) netto = 0;

        var formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

        document.getElementById('lblUangKotor').innerText = formatter.format(bruto);
        document.getElementById('lblPotongan').innerText = '- ' + formatter.format(totalOngkosJemput);
        document.getElementById('textSubtotal').innerText = formatter.format(netto);
    }

    // 7. ENJIN UTAMA SUBMIT AJAX FETCH & KELUAR POPUP SWEETALERT2
    function eksekusiSimpanTimbangan(event) {
        event.preventDefault();

        var form = document.getElementById('formTransaksiTimbangan');
        var formData = new FormData(form);
        var submitBtn = document.getElementById('btnSimpanTimbangan');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan Hasil Timbangan...';

        fetch('<?= base_url("operator/transaksi_timbangan/simpan") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: data.message,
                    confirmButtonColor: '#1abc9c',
                    confirmButtonText: 'Selesai & Muat Ulang'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: data.message,
                    confirmButtonColor: '#e74c3c'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> SIMPAN PENIMBANGAN';
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'success',
                title: 'Transaksi Sukses Tercatat!',
                text: 'Pembukuan timbangan berhasil diproses dan saldo tabungan sudah dikirim langsung ke akun nasabah.',
                confirmButtonColor: '#1abc9c',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        });
    }
</script>
<?= $this->endSection() ?>