<?php

namespace App\Controllers;

class OperatorKelurahan extends BaseController
{
    protected function getOperatorData()
    {
        $id_pengguna = session()->get('id_pengguna');
        if (!$id_pengguna) return [];

        $db = \Config\Database::connect();
        
        // Perbaikan: Pastikan join menggunakan kolom yang benar (biasanya id_kelurahan)
        // Dan kita ambil data dengan join yang lebih aman
        $operator = $db->table('pengguna p')
                       ->select('p.*, p.nama_pengguna as nama, k.nama_kelurahan')
                       ->join('kelurahan k', 'k.id_kelurahan = p.id_kelurahan', 'left')
                       ->where('p.id_pengguna', $id_pengguna)
                       ->get()
                       ->getRowArray();

        // Perbaikan: Hapus logika yang menyebabkan error sintaks pada baris 25
        // GANTI BARIS 25-27 DENGAN INI:
        if (isset($operator['nama_kelurahan']) && !empty($operator['nama_kelurahan'])) {
            $operator['kelurahan'] = $operator['nama_kelurahan'];
        } elseif (isset($operator['kelurahan']) && !empty($operator['kelurahan'])) {
            $operator['kelurahan'] = $operator['kelurahan'];
        } else {
            $operator['kelurahan'] = 'Tompeyan';
        }
        
        return $operator;
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();
        $operator = $this->getOperatorData();
        $nama_kelurahan = isset($operator['kelurahan']) ? trim($operator['kelurahan']) : 'Tompeyan';

        // 1. Ambil Log Transaksi Timbangan
        $builder_pending = $db->table('detil_transaksi_timbangan dt')
            ->select('tt.id_transaksi, tt.tanggal_transaksi, n.nama_nasabah, n.id_rw, r.nama_rw, k.katalog_sampah, dt.berat_sampah, dt.subtotal')
            ->join('transaksi_timbangan tt', 'tt.id_transaksi = dt.id_transaksi')
            ->join('nasabah n', 'n.id_nasabah = tt.id_nasabah')
            ->join('kategori_sampah k', 'k.id_katalog = dt.id_katalog')
            ->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->where('kl.nama_kelurahan', $nama_kelurahan);

        $transaksi_pending = $builder_pending->orderBy('tt.tanggal_transaksi', 'DESC')
                                              ->get()
                                              ->getResultArray();

        // 2. Hitung Total Nasabah
        $total_nasabah = $db->table('nasabah n')
                            ->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left')
                            ->where('kl.nama_kelurahan', $nama_kelurahan)
                            ->countAllResults();
        
        // 3. Hitung Total Log Transaksi
        $total_log_timbangan = $db->table('transaksi_timbangan tt')
                                  ->join('nasabah n', 'n.id_nasabah = tt.id_nasabah')
                                  ->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left')
                                  ->where('kl.nama_kelurahan', $nama_kelurahan)
                                  ->countAllResults();
        
        return view('operator_kelurahan/dashboard_view', [
            'title'             => 'Dashboard Operator Kelurahan ' . $nama_kelurahan,
            'operator'          => $operator,
            'transaksi_pending' => $transaksi_pending,
            'total_nasabah'     => $total_nasabah,
            'total_valid'       => $total_log_timbangan 
        ]);
    }

    public function laporan()
    {
        $db = \Config\Database::connect();
        $operator = $this->getOperatorData();
        $nama_kelurahan = isset($operator['kelurahan']) ? trim($operator['kelurahan']) : 'Tompeyan';

        $bulan = ($this->request->getGet('bulan') !== null) ? $this->request->getGet('bulan') : date('m');
        $tahun = ($this->request->getGet('tahun') !== null) ? $this->request->getGet('tahun') : date('Y');

        $builder = $db->table('nasabah n')
            ->select('n.id_rw, r.nama_rw, COUNT(DISTINCT tt.id_transaksi) as total_transaksi, SUM(dt.berat_sampah) as total_berat, SUM(dt.subtotal) as total_saldo, COUNT(DISTINCT n.id_nasabah) as nasabah_aktif')
            ->join('transaksi_timbangan tt', 'tt.id_nasabah = n.id_nasabah', 'left')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            ->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->where('kl.nama_kelurahan', $nama_kelurahan)
            ->where('MONTH(tt.tanggal_transaksi)', $bulan)
            ->where('YEAR(tt.tanggal_transaksi)', $tahun)
            ->groupBy('n.id_rw, r.nama_rw')
            ->orderBy('n.id_rw', 'ASC');

        $rekap_rw = $builder->get()->getResultArray();

        return view('operator_kelurahan/laporan_view', [
            'title' => 'Laporan Bank Sampah Kelurahan ' . $nama_kelurahan,
            'operator' => $operator, 'rekap_rw' => $rekap_rw, 'bulan_pilihan' => $bulan, 'tahun_pilihan' => $tahun, 'nama_kelurahan' => $nama_kelurahan
        ]);
    }

    public function cetak_pdf()
    {
        $db = \Config\Database::connect();
        $operator = $this->getOperatorData();
        $nama_kelurahan = isset($operator['kelurahan']) ? trim($operator['kelurahan']) : 'Tompeyan';

        $bulan = ($this->request->getGet('bulan') !== null) ? $this->request->getGet('bulan') : date('m');
        $tahun = ($this->request->getGet('tahun') !== null) ? $this->request->getGet('tahun') : date('Y');

        // Tambahkan logika konversi angka bulan ke nama bulan
        $nama_bulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $bulan_teks = isset($nama_bulan[$bulan]) ? $nama_bulan[$bulan] : $bulan;

        // Query Builder dipisahkan dari pengiriman data ke View
        $builder = $db->table('nasabah n')
            ->select('n.id_rw, r.nama_rw, COUNT(DISTINCT tt.id_transaksi) as total_transaksi, SUM(dt.berat_sampah) as total_berat, SUM(dt.subtotal) as total_saldo, COUNT(DISTINCT n.id_nasabah) as nasabah_aktif')
            ->join('transaksi_timbangan tt', 'tt.id_nasabah = n.id_nasabah', 'left')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            ->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->where('kl.nama_kelurahan', $nama_kelurahan)
            ->where('MONTH(tt.tanggal_transaksi)', $bulan)
            ->where('YEAR(tt.tanggal_transaksi)', $tahun)
            ->groupBy('n.id_rw, r.nama_rw')
            ->orderBy('n.id_rw', 'ASC');

        $rekap_rw = $builder->get()->getResultArray();

        // Kirim semua variabel yang dibutuhkan ke dalam array View
        return view('operator_kelurahan/laporan_pdf_view', [
            'rekap_rw'       => $rekap_rw, 
            'nama_kelurahan' => $nama_kelurahan, 
            'operator'       => $operator,
            'bulan_teks'     => $bulan_teks,    // Sekarang variabel ini tersedia di View
            'tahun_pilihan'  => $tahun          // Sekarang variabel ini tersedia di View
        ]);
    }
}