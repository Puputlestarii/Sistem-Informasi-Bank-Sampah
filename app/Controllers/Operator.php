<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Operator extends BaseController
{
    protected function getOperatorData($id_pengguna, $db)
    {
        return $db->table('pengguna')->where('id_pengguna', $id_pengguna)->get()->getRowArray();
    }

    public function dashboard()
    {
        $id_pengguna = session()->get('id_pengguna');
        $db = \Config\Database::connect();
        
        $operator = $this->getOperatorData($id_pengguna, $db);

        // Inisialisasi default
        $nama_pengguna = 'Operator Bank Sampah';
        $nama_kelurahan_tugas = 'Yogyakarta';
        $id_kelurahan_tugas = null;

        if ($operator) {
            $nama_pengguna = isset($operator['nama_pengguna']) ? $operator['nama_pengguna'] : (isset($operator['username']) ? $operator['username'] : 'Operator');
            $id_kelurahan_tugas = isset($operator['id_kelurahan']) ? $operator['id_kelurahan'] : null;
            
            if ($id_kelurahan_tugas) {
                $cekKel = $db->table('kelurahan')->where('id_kelurahan', $id_kelurahan_tugas)->get()->getRowArray();
                if ($cekKel) { $nama_kelurahan_tugas = $cekKel['nama_kelurahan']; }
            }
        }

        $data_operator = [
            'id_pengguna'    => $id_pengguna,
            'nama_pengguna'  => $nama_pengguna,
            'nama_kelurahan' => $nama_kelurahan_tugas
        ];

        // 1. Statistik Nasabah per Kelurahan
        $total_nasabah = $db->table('nasabah')->where('id_kelurahan', $id_kelurahan_tugas)->countAllResults();

        // 2. Statistik Permintaan Masuk (Penjemputan yang ditugaskan ke operator ini)
        $permintaan_masuk = $db->table('permintaan_penjemputan_sampah pjs')
            ->join('jadwal_penjemputan_sampah jjs', 'jjs.id_permintaan = pjs.Id_permintaan', 'left')
            ->whereIn('pjs.Status_penjemputan', ['Diverifikasi', 'Aktif', 'Ditunda'])
            ->where('jjs.id_pengguna', $id_pengguna)
            ->countAllResults();

        // 3. Total Berat Sampah per Kelurahan
        $total_sampah = $db->table('detil_transaksi_timbangan dt')
            ->join('transaksi_timbangan tt', 'tt.id_transaksi = dt.id_transaksi')
            ->join('nasabah n', 'n.id_nasabah = tt.id_nasabah')
            ->where('n.id_kelurahan', $id_kelurahan_tugas)
            ->selectSum('dt.berat_sampah')
            ->get()->getRowArray();
        $total_berat = isset($total_sampah['berat_sampah']) ? $total_sampah['berat_sampah'] : 0;

        // 4. Perhitungan Pendapatan Jasa Jemput (Khusus Operator ini)
        $data_pendapatan = $db->table('transaksi_timbangan tt')
        ->select('SUM(dt.berat_sampah * tj.biaya_penjemputan) as total_pendapatan')
        ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
        ->join('tarif_penjemputan_sampah tj', 'tj.id_tarif = tt.id_tarif', 'left')
        ->where('tt.id_pengguna', $id_pengguna)
        ->where('tt.id_tarif IS NOT NULL')
        ->get()
        ->getRowArray();

        $total_pendapatan_kotor = isset($data_pendapatan['total_pendapatan']) ? $data_pendapatan['total_pendapatan'] : 0;

        // Ambil total yang sudah ditarik
        $operator_data = $db->table('pengguna')->where('id_pengguna', $id_pengguna)->get()->getRowArray();
        $total_ditarik = isset($operator_data['saldo_operasional']) ? $operator_data['saldo_operasional'] : 0;

        // --- TAMBAHKAN BARIS INI ---
        $total_pendapatan_jemput = $total_pendapatan_kotor - $total_ditarik;
        // ---------------------------
        // 5. Data Tabel Jemputan
        $tabel_jemputan = $db->table('permintaan_penjemputan_sampah pjs')
            ->select('pjs.*, n.nama_nasabah, n.nomor_telepon, n.alamat, jjs.Tanggal_jadwal, jjs.Jam_jadwal')
            ->join('nasabah n', 'n.id_nasabah = pjs.id_nasabah', 'left')
            ->join('jadwal_penjemputan_sampah jjs', 'jjs.id_permintaan = pjs.Id_permintaan', 'inner')
            ->whereIn('pjs.Status_penjemputan', ['Diverifikasi', 'Aktif', 'Ditunda'])
            ->where('jjs.id_pengguna', $id_pengguna)
            ->orderBy('pjs.Id_permintaan', 'DESC') 
            ->get()
            ->getResultArray();
        // 6. Data Antrean Tarik Saldo
        $antrean_tarik_saldo = $db->table('pengajuan_penarikan_saldo pps')
            ->select('pps.*, n.nama_nasabah, n.saldo as saldo_sekarang')
            ->join('nasabah n', 'n.id_nasabah = pps.id_nasabah', 'left')
            ->where('pps.status_pengajuan', 'Pending')
            ->where('n.id_kelurahan', $id_kelurahan_tugas)
            ->orderBy('pps.id_pengajuan', 'ASC')
            ->get()->getResultArray();

        return view('operator/dashboard_view', [
            'title'                => 'Dashboard Operator ' . $nama_kelurahan_tugas,
            'operator'             => $data_operator,
            'total_nasabah'        => $total_nasabah,
            'permintaan_masuk'     => $permintaan_masuk,
            'total_berat'          => $total_berat,
            'total_pendapatan_jemput' => $total_pendapatan_jemput,
            'tabel_jemputan'       => $tabel_jemputan,
            'antrean_tarik_saldo'  => $antrean_tarik_saldo
        ]);
    }

    public function transaksi_timbangan()
    {
        $db = \Config\Database::connect();
        $id_pengguna = session()->get('id_pengguna');
        $operator = $this->getOperatorData($id_pengguna, $db);
        $id_kel_tugas = isset($operator['id_kelurahan']) ? $operator['id_kelurahan'] : null;

        $builder = $db->table('nasabah n')
            ->select('n.*, kl.nama_kelurahan, r.nama_rw')
            ->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->where('n.id_kelurahan', $id_kel_tugas)
            ->orderBy('n.nama_nasabah', 'ASC')
            ->get()->getResultArray();

        $tarif_jemput = $db->table('tarif_penjemputan_sampah tj')
            ->select('tj.*, ks.katalog_sampah')
            ->join('kategori_sampah ks', 'ks.id_katalog = tj.id_katalog', 'left')
            ->get()->getResultArray();

        return view('operator/transaksi_timbangan_view', [
            'title'          => 'Transaksi Timbangan Digital',
            'daftar_nasabah' => $builder,
            'daftar_katalog' => $db->table('kategori_sampah')->get()->getResultArray(),
            'tarif_jemput'   => $tarif_jemput
        ]);
    }
    // Pastikan kode ini ada di dalam class Operator
   public function laporan_transaksi()
    {
        $db = \Config\Database::connect();
        $id_pengguna = session()->get('id_pengguna');
        $operator = $this->getOperatorData($id_pengguna, $db);
        $id_kel_tugas = isset($operator['id_kelurahan']) ? $operator['id_kelurahan'] : null;

        $riwayat = $db->table('transaksi_timbangan tt')
            ->select('tt.*, n.nama_nasabah, r.nama_rw, ks.katalog_sampah, dt.berat_sampah, dt.subtotal, dt.harga_per_kg, tj.biaya_penjemputan')
            ->join('nasabah n', 'n.id_nasabah = tt.id_nasabah', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left') // Join ke RW
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            ->join('kategori_sampah ks', 'ks.id_katalog = dt.id_katalog', 'left')
            ->join('tarif_penjemputan_sampah tj', 'tj.id_tarif = tt.id_tarif', 'left') // Join ke Tarif
            ->where('n.id_kelurahan', $id_kel_tugas)
            ->orderBy('tt.tanggal_transaksi', 'DESC')
            ->get()->getResultArray();

        return view('operator/laporan_transaksi_view', [
            'riwayat_timbang' => $riwayat,
            'operator'        => $operator
        ]);
    }
    public function cetak_laporan()
    {
        $db = \Config\Database::connect();
        $id_pengguna = session()->get('id_pengguna');
        
        $operator = $this->getOperatorData($id_pengguna, $db);
        $id_kelurahan_tugas = isset($operator['id_kelurahan']) ? $operator['id_kelurahan'] : null;

        // 1. PERBAIKAN: Gunakan nama tabel 'kelurahan', bukan nama kolom
        $nama_kel = 'Yogyakarta';
        if ($id_kelurahan_tugas) {
            $cekKel = $db->table('kelurahan')->where('id_kelurahan', $id_kelurahan_tugas)->get()->getRowArray();
            $nama_kel = $cekKel ? $cekKel['nama_kelurahan'] : 'Yogyakarta';
        }

        // 2. Query data
        $riwayat = $db->table('transaksi_timbangan tt')
            ->select('tt.*, n.nama_nasabah, r.nama_rw, ks.katalog_sampah, dt.berat_sampah, dt.subtotal, dt.harga_per_kg, tj.biaya_penjemputan')
            ->join('nasabah n', 'n.id_nasabah = tt.id_nasabah', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            ->join('kategori_sampah ks', 'ks.id_katalog = dt.id_katalog', 'left')
            ->join('tarif_penjemputan_sampah tj', 'tj.id_tarif = tt.id_tarif', 'left')
            // 3. Pastikan menggunakan variabel $id_kelurahan_tugas yang sudah didefinisikan di atas
            ->where('n.id_kelurahan', $id_kelurahan_tugas) 
            ->orderBy('tt.tanggal_transaksi', 'DESC')
            ->get()->getResultArray();

        $data = [
            'riwayat_timbang' => $riwayat,
            'operator'        => ['nama_kelurahan' => $nama_kel]
        ];

        return view('operator/laporan_cetak_view', $data);
    }

    public function verifikasi_penjemputan()
    {
        $id_permintaan = $this->request->getPost('id_permintaan');
        $status_final  = $this->request->getPost('status_final');

        $status_valid = ['Diverifikasi', 'Ditunda', 'Selesai', 'Ditolak'];
        
        if ($id_permintaan && in_array($status_final, $status_valid)) {
            $db = \Config\Database::connect();
            $db->transStart();

            $db->table('permintaan_penjemputan_sampah')
                ->where('Id_permintaan', $id_permintaan)
                ->update(['Status_penjemputan' => $status_final]);

            $db->table('jadwal_penjemputan_sampah')
                ->where('id_permintaan', $id_permintaan)
                ->update(['Status_jadwal' => $status_final]);

            $db->transComplete();

            return redirect()->to(base_url('operator/dashboard'))->with('success', 'Status berhasil diubah menjadi ' . $status_final);
        }

        return redirect()->to(base_url('operator/dashboard'))->with('error', 'Status tidak valid.');
    }

    public function simpan_timbangan()
    {
        // 1. Set zona waktu ke Jakarta agar sinkron dengan waktu lokal Anda
        date_default_timezone_set('Asia/Jakarta');
        
        $db = \Config\Database::connect();
        
        // 2. Ambil data dari form
        $id_nasabah = $this->request->getPost('id_nasabah');
        $id_katalog = $this->request->getPost('id_katalog');
        $berat_sampah = floatval($this->request->getPost('berat_sampah'));
        $harga_per_kg = intval($this->request->getPost('harga_per_kg'));
        
        // 3. Ambil input tanggal_transaksi dari form
        // Jika input tidak ada, gunakan tanggal hari ini
        $tanggal_input = $this->request->getPost('tanggal_transaksi') ?: date('Y-m-d');
        
        $metode_setor = $this->request->getPost('metode_setor');
        $id_tarif = $this->request->getPost('id_tarif');
        
        $id_tarif_final = ($metode_setor === 'dijemput' && $id_tarif > 0) ? $id_tarif : null;
        
        $ongkos_dasar = 0;
        if ($id_tarif_final) {
            $cekTarif = $db->table('tarif_penjemputan_sampah')->where('id_tarif', $id_tarif_final)->get()->getRowArray();
            if ($cekTarif) {
                $ongkos_dasar = intval($cekTarif['biaya_penjemputan']); 
                $min_berat_db = floatval($cekTarif['berat_sampah']); 
                
                if ($berat_sampah < $min_berat_db) {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan! Berat sampah tidak memenuhi batas minimal aturan jemput.']);
                    exit;
                }
            }
        }

        $bruto_uang_sampah = round($berat_sampah * $harga_per_kg);
        $total_potongan_jemput = round($berat_sampah * $ongkos_dasar);
        
        $netto_masuk_saldo = $bruto_uang_sampah - $total_potongan_jemput;
        if ($netto_masuk_saldo < 0) { $netto_masuk_saldo = 0; }

        if ($id_nasabah && $id_katalog && $berat_sampah > 0) {
            $dataInsertMaster = [
                'id_pengguna'       => session()->get('id_pengguna') ?: null,
                'id_nasabah'        => $id_nasabah,
                'id_tarif'          => $id_tarif_final,
                // 4. Gunakan $tanggal_input yang diambil dari form
                'tanggal_transaksi' => $tanggal_input . ' ' . date('H:i:s'), 
            ];
            
            $db->table('transaksi_timbangan')->insert($dataInsertMaster);
            $id_transaksi = $db->connID->insert_id;

            $dataInsertDetail = [
                'id_transaksi' => $id_transaksi,
                'id_katalog'   => $id_katalog,
                'berat_sampah' => $berat_sampah,
                'harga_per_kg' => $harga_per_kg,
                'subtotal'     => $netto_masuk_saldo
            ];
            $db->table('detil_transaksi_timbangan')->insert($dataInsertDetail);

            $nasabah = $db->table('nasabah')->where('id_nasabah', $id_nasabah)->get()->getRowArray();
            $saldo_lama = isset($nasabah['saldo']) ? intval($nasabah['saldo']) : 0;
            $saldo_baru = $saldo_lama + $netto_masuk_saldo;

            $db->table('nasabah')->where('id_nasabah', $id_nasabah)->update(['saldo' => $saldo_baru]);

            echo json_encode(['status' => 'success', 'message' => 'Data penimbangan harian berhasil disimpan!']);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Gagal memproses data timbangan.']);
        exit;
    }

    public function verifikasi_penarikan()
    {
        $db = \Config\Database::connect();
        
        $id_pengajuan = $this->request->getPost('id_pengajuan');
        $status_baru = $this->request->getPost('status_pengajuan'); 
        $id_pengguna = session()->get('id_pengguna');

        if ($id_pengajuan && $id_pengguna) {
            $pengajuan = $db->table('pengajuan_penarikan_saldo')->where('id_pengajuan', $id_pengajuan)->get()->getRowArray();
            
            if ($pengajuan && $pengajuan['status_pengajuan'] === 'Pending') {
                $id_nasabah = $pengajuan['id_nasabah'];
                $jumlah_tarik = floatval($pengajuan['jumlah_pengajuan']);

                $nasabah = $db->table('nasabah')->where('id_nasabah', $id_nasabah)->get()->getRowArray();
                $saldo_sekarang = floatval($nasabah['saldo']);

                if ($status_baru === 'Disetujui') {
                    if ($jumlah_tarik > $saldo_sekarang) {
                        return redirect()->to(base_url('operator/dashboard'))->with('error', 'Gagal memverifikasi! Jumlah klaim penarikan melebihi saldo.');
                    }
                    $saldo_baru = $saldo_sekarang - $jumlah_tarik;
                    $db->table('nasabah')->where('id_nasabah', $id_nasabah)->update(['saldo' => $saldo_baru]);
                }

                $db->table('pengajuan_penarikan_saldo')
                    ->where('id_pengajuan', $id_pengajuan)
                    ->update(['status_pengajuan' => $status_baru]);

                return redirect()->to(base_url('operator/dashboard'))->with('success', 'Status penarikan berhasil diperbarui.');
            }
        }
        return redirect()->to(base_url('operator/dashboard'))->with('error', 'Gagal memproses.');
    }
    
   public function laporan_penjemputan()
    {
        $db = \Config\Database::connect();
        $id_pengguna = session()->get('id_pengguna');
        $operator = $this->getOperatorData($id_pengguna, $db);
        $id_kel_tugas = isset($operator['id_kelurahan']) ? $operator['id_kelurahan'] : null;

        $riwayat = $db->table('transaksi_timbangan tt')
            ->select('tt.*, n.nama_nasabah, r.nama_rw, ks.katalog_sampah, dt.berat_sampah, dt.subtotal, dt.harga_per_kg, tj.biaya_penjemputan')
            ->join('nasabah n', 'n.id_nasabah = tt.id_nasabah', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left') 
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            ->join('kategori_sampah ks', 'ks.id_katalog = dt.id_katalog', 'left')
            ->join('tarif_penjemputan_sampah tj', 'tj.id_tarif = tt.id_tarif', 'left') 
            // FILTER KELURAHAN (Sudah ada di kode Anda)
            ->where('n.id_kelurahan', $id_kel_tugas)
            // FILTER TAMBAHAN: Pastikan hanya data yang memiliki tarif penjemputan (Dijemput)
            ->where('tt.id_tarif IS NOT NULL') 
            ->orderBy('tt.tanggal_transaksi', 'DESC')
            ->get()->getResultArray();

        return view('operator/laporan_penjemputan', [
            'laporan' => $riwayat, // Pastikan variabel ini sesuai dengan yang dipakai di file view
            'operator' => $operator
        ]);
    }

    public function proses_tarik_pendapatan()
    {
        $db = \Config\Database::connect();
        $id_pengguna = session()->get('id_pengguna');
        $jumlah_tarik = $this->request->getPost('jumlah_tarik');

        // 1. Ambil data operator dengan perhitungan pendapatan yang sama seperti di dashboard
        $data_pendapatan = $db->table('transaksi_timbangan tt')
            ->select('SUM(dt.berat_sampah * tj.biaya_penjemputan) as total_pendapatan')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            ->join('tarif_penjemputan_sampah tj', 'tj.id_tarif = tt.id_tarif', 'left')
            ->where('tt.id_pengguna', $id_pengguna)
            ->where('tt.id_tarif IS NOT NULL')
            ->get()->getRowArray();
        
        $total_pendapatan_kotor = isset($data_pendapatan['total_pendapatan']) ? $data_pendapatan['total_pendapatan'] : 0;

        // Ambil total yang sudah ditarik sebelumnya
        $operator_data = $db->table('pengguna')->where('id_pengguna', $id_pengguna)->get()->getRowArray();
        $total_ditarik = isset($operator_data['saldo_operasional']) ? $operator_data['saldo_operasional'] : 0;

        // Saldo yang benar-benar bisa ditarik
        $saldo_tersedia = $total_pendapatan_kotor - $total_ditarik;

        // 2. Validasi dengan saldo yang baru dihitung
        if ($jumlah_tarik > 0 && $jumlah_tarik <= $saldo_tersedia) {
            $db->query("UPDATE pengguna SET saldo_operasional = saldo_operasional + ? WHERE id_pengguna = ?", 
                [$jumlah_tarik, $id_pengguna]);
                
            return redirect()->to(base_url('operator/dashboard'))->with('success', 'Penarikan berhasil!');
        } else {
            // Debugging: Kita bisa tahu kenapa gagal di sini
            return redirect()->back()->with('error', 'Gagal: Input ' . $jumlah_tarik . ' > Saldo Tersedia ' . $saldo_tersedia);
        }
    }
}