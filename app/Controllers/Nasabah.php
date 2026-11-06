<?php

namespace App\Controllers;

class Nasabah extends BaseController
{
    public function dashboard()
    {
        $id_nasabah = session()->get('id_nasabah'); 
        if (!$id_nasabah) return redirect()->to('/auth');

        $db = \Config\Database::connect();

        // 1. Ambil Informasi Nasabah dengan JOIN agar nama wilayah langsung tersedia
        $nasabah = $db->table('nasabah n')
            ->select('n.*, kl.nama_kelurahan as nama_kelurahan, r.nama_rw as nama_rw')
            ->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->where('n.id_nasabah', $id_nasabah)
            ->get()
            ->getRowArray();
        // 2. Hitung Total Berat Sampah
        $total_berat = $db->table('transaksi_timbangan tt')
            ->selectSum('dt.berat_sampah')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi')
            ->where('tt.id_nasabah', $id_nasabah)
            ->get()->getRowArray();

        // 3. Ambil Riwayat Transaksi Timbangan
        $riwayat_timbangan = $db->table('transaksi_timbangan tt')
            ->select('tt.tanggal_transaksi, dt.berat_sampah, dt.subtotal as total_harga')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi')
            ->where('tt.id_nasabah', $id_nasabah)
            ->orderBy('tt.tanggal_transaksi', 'DESC')
            ->limit(3)
            ->get()->getResultArray();

        // 4. Ambil Status Pelacakan Penjemputan
        $status_jemput = $db->table('permintaan_penjemputan_sampah p')
            ->select('p.Id_permintaan, p.Tanggal_permintaan, p.Status_penjemputan, j.Jam_jadwal, j.Tanggal_jadwal, pg.nama_pengguna as nama_petugas, j.Keterangan_admin')
            ->join('jadwal_penjemputan_sampah j', 'j.id_permintaan = p.Id_permintaan', 'left')
            ->join('pengguna pg', 'pg.id_pengguna = j.id_pengguna', 'left')
            ->where('p.id_nasabah', $id_nasabah)
            ->orderBy('p.Id_permintaan', 'DESC')
            ->get()->getResultArray();

        // 5. Data Katalog & Tarif
        $katalog_sampah = $db->table('kategori_sampah')->orderBy('katalog_sampah', 'ASC')->get()->getResultArray();
        
        $tarif_jemput = $db->table('tarif_penjemputan_sampah tps')
            ->select('tps.*, ks.katalog_sampah')
            ->join('kategori_sampah ks', 'ks.id_katalog = tps.id_katalog')
            ->get()->getResultArray();

        $saldo_sekarang = isset($nasabah['saldo']) ? $nasabah['saldo'] : 0;
        
        $data = [
            'title'             => 'Dashboard Nasabah MasJOS',
            'nasabah'           => $nasabah,
            'total_berat' => isset($total_berat['berat_sampah']) ? $total_berat['berat_sampah'] : 0,
            'riwayat_timbangan' => $riwayat_timbangan,
            'status_jemput'     => $status_jemput, 
            'katalog_sampah'    => $katalog_sampah,
            'tarif_jemput'      => $tarif_jemput,
            'bisa_tarik_saldo'  => ($saldo_sekarang >= 50000)
        ];

        return view('nasabah/dashboard', $data);
    }

    public function minta_jemput()
    {
        $db = \Config\Database::connect();
        $id_nasabah = session()->get('id_nasabah');

        // Ambil data nasabah agar latitude/longitude tersedia di form
        $nasabah = $db->table('nasabah')->where('id_nasabah', $id_nasabah)->get()->getRowArray();

        $data = [
            'title'          => 'Form Permintaan Penjemputan Sampah',
            'nasabah'        => $nasabah, // <--- INI YANG KURANG
            'katalog_sampah' => $db->table('kategori_sampah')->get()->getResultArray()
        ];
        
        return view('nasabah/minta_jemput_form', $data);
    }

    public function simpan_permintaan()
    {
        \Config\Database::connect()->table('permintaan_penjemputan_sampah')->insert([
            'id_nasabah'         => session()->get('id_nasabah'),
            'Tanggal_permintaan' => $this->request->getVar('tanggal_permintaan'),
            'Alamat_penjemputan' => $this->request->getVar('alamat_penjemputan'),
            'latitude'           => $this->request->getVar('latitude'),   
            'longitude'          => $this->request->getVar('longitude'), 
            'Status_penjemputan' => 'Menunggu'
        ]);

        return redirect()->to('/nasabah/dashboard')->with('success', 'Permintaan penjemputan berhasil dikirim!');
    }

    public function penarikan_saldo()
    {
        $id = session()->get('id_nasabah');
        $db = \Config\Database::connect();

        // Pastikan JOIN dilakukan di sini agar $nasabah memiliki kunci 'nama_kelurahan' dan 'nama_rw'
        $nasabah = $db->table('nasabah n')
            ->select('n.*, k.nama_kelurahan, r.nama_rw')
            ->join('kelurahan k', 'k.id_kelurahan = n.id_kelurahan', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->where('n.id_nasabah', $id)
            ->get()->getRowArray();

        $riwayat = $db->table('pengajuan_penarikan_saldo')->where('id_nasabah', $id)->orderBy('id_pengajuan', 'DESC')->get()->getResultArray();

        return view('nasabah/penarikan_saldo_view', [
            'title'            => 'Pengajuan Penarikan Saldo',
            'nasabah'          => $nasabah,
            'bisa_tarik_saldo' => ((isset($nasabah['saldo']) ? $nasabah['saldo'] : 0) >= 50000),
            'riwayat_tarik'    => $riwayat
        ]);
    }

    public function simpan_penarikan()
    {
        $db = \Config\Database::connect();
        $id_nasabah = session()->get('id_nasabah');
        $jumlah = (int)$this->request->getPost('jumlah_pengajuan');
        
        // 1. Ambil saldo terbaru dari database
        $nasabah = $db->table('nasabah')->where('id_nasabah', $id_nasabah)->get()->getRowArray();
        
        if (!$nasabah) {
            return redirect()->back()->with('error', 'Data nasabah tidak ditemukan.');
        }
        
        $saldo_tersedia = (int)$nasabah['saldo'];
        
        // 2. VALIDASI PENARIKAN
        // Jika jumlah penarikan lebih besar dari saldo, maka gagal
        if ($jumlah > $saldo_tersedia) {
            return redirect()->back()->with('error', 'Penarikan gagal! Jumlah melebihi saldo Anda.');
        }
        
        // Jika jumlah penarikan kurang dari batas minimal (misal Rp 50.000)
        if ($jumlah < 50000) {
            return redirect()->back()->with('error', 'Penarikan gagal! Minimal penarikan adalah Rp 50.000.');
        }

        // 3. SIMPAN PENGAJUAN
        $db->table('pengajuan_penarikan_saldo')->insert([
            'id_nasabah'        => $id_nasabah, // Diperbaiki dari $id menjadi $id_nasabah
            'tanggal_pengajuan' => date('Y-m-d H:i:s'),
            'jumlah_pengajuan'  => $jumlah,
            'status_pengajuan'  => 'Pending'
        ]);

        return redirect()->to('/nasabah/penarikan_saldo')->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function riwayat_timbangan()
    {
        $db = \Config\Database::connect();
        $id_nasabah = session()->get('id_nasabah');
        
        // Ambil data riwayat transaksi
        $riwayat = $db->table('transaksi_timbangan tt')
            ->select('tt.*, ks.katalog_sampah, dt.berat_sampah, dt.subtotal, dt.harga_per_kg, pg.nama_pengguna as nama_petugas')
            ->join('detil_transaksi_timbangan dt', 'dt.id_transaksi = tt.id_transaksi', 'left')
            ->join('kategori_sampah ks', 'ks.id_katalog = dt.id_katalog', 'left')
            ->join('pengguna pg', 'pg.id_pengguna = tt.id_pengguna', 'left')
            ->where('tt.id_nasabah', $id_nasabah)
            ->orderBy('tt.tanggal_transaksi', 'DESC')
            ->get()
            ->getResultArray();

        // UBAH NAMA KEY MENJADI 'riwayat_lengkap' AGAR SAMA DENGAN VIEW ANDA
        return view('nasabah/riwayat_timbangan_view', [
            'title'           => 'Riwayat Timbangan',
            'riwayat_lengkap' => $riwayat 
        ]);
    }
}