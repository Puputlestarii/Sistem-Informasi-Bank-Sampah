<?php

namespace App\Controllers;

use Dompdf\Dompdf; // Pastikan ini ada

class Kemantren extends BaseController
{
    // Fungsi pembantu profil pimpinan
    protected function getPimpinanData()
    {
        $id_pengguna = session()->get('id_pengguna');
        $db = \Config\Database::connect();
        return $db->table('pengguna')->select('*, nama_pengguna as nama')->where('id_pengguna', $id_pengguna)->get()->getRowArray();
    }

    // =========================================================================
    // 1. DASHBOARD UTAMA PIMPINAN (FIX CAPAIAN MATRIKS KELURAHAN & CHART)
    // =========================================================================
    public function dashboard()
    {
        $db = \Config\Database::connect();
        $pimpinan = $this->getPimpinanData();

        // 1. STATISTIK MAKRO KESELURUHAN SISTEM
        $total_warga = $db->table('nasabah')->countAllResults();
        $total_petugas = $db->table('pengguna')->where('jabatan !=', 'Kepala Kemantren')->countAllResults();
        
        $omset = $db->table('detil_transaksi_timbangan')->select('SUM(berat_sampah) as kg, SUM(subtotal) as rupiah')->get()->getRowArray();
        $total_kg = isset($omset['kg']) ? $omset['kg'] : 0;
        $total_rp = isset($omset['rupiah']) ? $omset['rupiah'] : 0;

        // 2. DATA AGREGAT PER KELURAHAN (Penyelarasan ID angka & Fallback Teks Nama)
        $laporan_kelurahan = $db->table('kelurahan k')
        ->select('
            k.nama_kelurahan, 
            COUNT(DISTINCT n.id_nasabah) as warga,
            COUNT(DISTINCT tt.id_transaksi) as transaksi, 
            SUM(dt.berat_sampah) as berat, 
            SUM(dt.subtotal) as omset
        ')
        // Perbaiki join: gunakan id_kelurahan
        ->join('nasabah n', 'n.id_kelurahan = k.id_kelurahan', 'left') 
        ->join('transaksi_timbangan tt', 'tt.id_nasabah = n.id_nasabah', 'left') 
        ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
        ->groupBy('k.id_kelurahan, k.nama_kelurahan') // Sesuaikan grup by
        ->orderBy('berat', 'DESC')
        ->get()->getResultArray();

        // 3. PARSING DATA UNTUK GRAFIK CHART.JS DOUGHNUT
        $chart_labels = [];
        $chart_data = [];
        foreach ($laporan_kelurahan as $lk) {
            // Hanya masukkan ke grafik chart jika kelurahan memiliki data timbangan aktif
            if ((float)$lk['berat'] > 0) {
                $chart_labels[] = $lk['nama_kelurahan'] ? $lk['nama_kelurahan'] : 'Tanpa Nama';
                $chart_data[]   = (float)$lk['berat']; 
            }
        }

        // Jika setelah difilter ternyata grafik kosong, beri label default
        if (empty($chart_labels)) {
            $chart_labels = ['Belum Ada Data'];
            $chart_data = [0];
        }

        $data = [
            'title'             => 'Dashboard Eksekutif Kemantren',
            'pimpinan'          => $pimpinan,
            'total_warga'       => $total_warga,
            'total_petugas'     => $total_petugas,
            'total_kg'          => $total_kg,
            'total_rp'          => $total_rp,
            'laporan_kelurahan' => $laporan_kelurahan,
            'chart_labels'      => json_encode($chart_labels),
            'chart_data'        => json_encode($chart_data)
        ];

        return view('kemantren/dashboard_view', $data);
    }

    public function laporan()
    {
        $db = \Config\Database::connect();
        $pimpinan = $this->getPimpinanData();

        $daftar_kelurahan = $db->table('kelurahan')->get()->getResultArray();
        $id_kelurahan_pilihan = $this->request->getGet('id_kelurahan') ?: ($daftar_kelurahan ? $daftar_kelurahan[0]['id_kelurahan'] : '');
        $bulan = $this->request->getGet('bulan') ?: date('m');
        $tahun = $this->request->getGet('tahun') ?: date('Y');

        $nama_kelurahan_aktif = 'Semua';
        foreach ($daftar_kelurahan as $dk) {
            if ($dk['id_kelurahan'] == $id_kelurahan_pilihan) {
                $nama_kelurahan_aktif = $dk['nama_kelurahan'];
                break;
            }
        }

        // Ambil data rekap
        // Ambil data rekap
        $builder = $db->table('nasabah n')
            ->select('n.id_rw, r.nama_rw, COUNT(DISTINCT tt.id_transaksi) as total_transaksi, SUM(dt.berat_sampah) as total_berat, SUM(dt.subtotal) as total_saldo, COUNT(DISTINCT n.id_nasabah) as nasabah_aktif')
            ->join('transaksi_timbangan tt', 'tt.id_nasabah = n.id_nasabah', 'left')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            // Perbaiki join rw: gunakan id_rw
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            // Perbaiki filter: gunakan id_kelurahan
            ->where('n.id_kelurahan', $id_kelurahan_pilihan)
            ->where('MONTH(tt.tanggal_transaksi)', $bulan)
            ->where('YEAR(tt.tanggal_transaksi)', $tahun)
            ->groupBy('n.id_rw, r.nama_rw')
            ->orderBy('n.id_rw', 'ASC');

        $data = [
            'title'                => 'Monitoring Laporan Wilayah Kelurahan',
            'pimpinan'             => $pimpinan,
            'daftar_kelurahan'     => $daftar_kelurahan,
            'id_kelurahan_pilihan' => $id_kelurahan_pilihan,
            'nama_kelurahan_aktif' => $nama_kelurahan_aktif,
            'bulan_pilihan'        => $bulan,
            'tahun_pilihan'        => $tahun,
            'rekap_rw'             => $builder->get()->getResultArray()
        ];

        return view('kemantren/laporan_view', $data);
    }

    public function cetak_pdf()
    {
        $db = \Config\Database::connect();
        
        $id_kelurahan_pilihan = $this->request->getGet('id_kelurahan');
        $bulan = $this->request->getGet('bulan') ?: date('m');
        $tahun = $this->request->getGet('tahun') ?: date('Y');

        // PERBAIKAN: Gunakan id_kelurahan sebagai primary key
        $kelurahan_row = $db->table('kelurahan')->where('id_kelurahan', $id_kelurahan_pilihan)->get()->getRowArray();
        $nama_kelurahan = $kelurahan_row ? $kelurahan_row['nama_kelurahan'] : 'Yogyakarta';

        $nama_bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // PERBAIKAN: Gunakan id_rw dan id_kelurahan untuk join/where
        $builder = $db->table('nasabah n')
            ->select('n.id_rw, r.nama_rw, COUNT(DISTINCT tt.id_transaksi) as total_transaksi, SUM(dt.berat_sampah) as total_berat, SUM(dt.subtotal) as total_saldo, COUNT(DISTINCT n.id_nasabah) as nasabah_aktif')
            ->join('transaksi_timbangan tt', 'tt.id_nasabah = n.id_nasabah', 'left')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->where('n.id_kelurahan', $id_kelurahan_pilihan)
            ->where('MONTH(tt.tanggal_transaksi)', $bulan)
            ->where('YEAR(tt.tanggal_transaksi)', $tahun)
            ->groupBy('n.id_rw, r.nama_rw')
            ->orderBy('n.id_rw', 'ASC');

        $data = [
            'title'                => 'Laporan Eksekutif',
            'nama_kelurahan_aktif' => $nama_kelurahan,
            'bulan_teks'           => $nama_bulan[(int)$bulan],
            'tahun_pilihan'        => $tahun,
            'rekap_rw'             => $builder->get()->getResultArray(),
            'pimpinan'             => $this->getPimpinanData()
        ];

        return view('kemantren/laporan_pdf_view', $data);
    }
}
