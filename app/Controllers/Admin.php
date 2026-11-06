<?php

namespace App\Controllers;

use App\Models\PenggunaModel;
use App\Models\KategoriModel;
use App\Models\TarifPenjemputanModel;
use App\Models\KelurahanModel;

class Admin extends BaseController 
{
    protected $penggunaModel;
    protected $kategoriModel;
    protected $tarifModel;
    protected $kelurahanModel;

    public function __construct()
    {
        $this->penggunaModel = new PenggunaModel();
        $this->kategoriModel = new KategoriModel();
        $this->tarifModel = new TarifPenjemputanModel();
        $this->kelurahanModel = new KelurahanModel();
    }

    // =========================================================================
    // 1. MANAJEMEN PETUGAS/PENGGUNA
    // =========================================================================
    public function pengguna()
    {
        $data = [
            'title'     => 'Manajemen Pengguna',
            'users'     => $this->penggunaModel->findAll(),
            'kelurahan' => $this->kelurahanModel->findAll()
        ];
        return view('admin/pengguna_list', $data);
    }

    public function simpan_pengguna()
    {
        $rules = [
            'username' => [
                'rules'  => 'required|is_unique[pengguna.username]',
                'errors' => [
                    'required'  => 'Kolom Username wajib diisi.',
                    'is_unique' => 'Username "{value}" sudah digunakan oleh petugas lain! Gunakan username unik.'
                ]
            ],
            'nama_pengguna' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Nama pengguna wajib diisi.']
            ],
            'password' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Kata sandi akun wajib diisi.']
            ]
        ];

        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return redirect()->to('/admin/pengguna')->withInput()->with('error', $validation->getError('username'));
        }

        $this->penggunaModel->save([
            'nama_pengguna' => $this->request->getVar('nama_pengguna'),
            'username'      => $this->request->getVar('username'),
            'password'      => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'email'         => $this->request->getVar('email'),
            'jabatan'       => $this->request->getVar('jabatan'),
            'id_kelurahan' => $this->request->getVar('kelurahan'),
        ]);

        return redirect()->to('/admin/pengguna')->with('success', 'Data Pengguna Berhasil Disimpan');
    }

    public function update_pengguna()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id_pengguna');
        
        // Data dasar
        $data = [
            'nama_pengguna' => $this->request->getPost('nama_pengguna'),
            'username'      => $this->request->getPost('username'), // Pastikan ini ada
            'email'         => $this->request->getPost('email'),
            'jabatan'       => $this->request->getPost('jabatan'),
           'id_kelurahan' => $this->request->getPost('kelurahan')
        ];

        // Cek jika ada password baru
        $pass = $this->request->getPost('password');
        if (!empty($pass)) {
            $data['password'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        $db->table('pengguna')->where('id_pengguna', $id)->update($data);
        return redirect()->to(base_url('admin/pengguna'))->with('success', 'Data petugas berhasil diupdate.');
    }

    public function hapus_pengguna($id)
    {
        $db = \Config\Database::connect();

        $db->table('penarikan_saldo')->where('id_pengguna', $id)->update(['id_pengguna' => null]);
        $db->table('transaksi_timbangan')->where('id_pengguna', $id)->update(['id_pengguna' => null]);
           
        $db->table('jadwal_penjemputan_sampah')->where('id_pengguna', $id)->delete();

        $this->penggunaModel->delete($id);

        return redirect()->to('/admin/pengguna')->with('success', 'Data Petugas Berhasil Dihapus dari Sistem.');
    }

    // =========================================================================
    // 2. MANAJEMEN NASABAH
    // =========================================================================
    public function nasabah()
    {
        $db = \Config\Database::connect();
        
        // Menggunakan Query Builder dengan JOIN agar data kelurahan & rw terisi
        $builder = $db->table('nasabah n');
        $builder->select('n.*, kl.nama_kelurahan, r.nama_rw'); 
        $builder->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left');
        $builder->join('rw r', 'r.id_rw = n.id_rw', 'left');
        $builder->orderBy('n.id_nasabah', 'DESC');
        
        $data['nasabah'] = $builder->get()->getResultArray();
        
        // Pastikan view yang dipanggil sudah sesuai
        return view('admin/nasabah_list', $data);
    }

    public function hapus_nasabah($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Cek apakah data nasabah benar-benar ada
        $nasabah = $db->table('nasabah')->where('id_nasabah', $id)->get()->getRow();

        if ($nasabah) {
            // 2. Matikan pengecekan relasi agar bisa menghapus nasabah yang punya riwayat transaksi
            $db->query('SET FOREIGN_KEY_CHECKS = 0');
            
            // 3. Hapus data
            $db->table('nasabah')->where('id_nasabah', $id)->delete();
            
            // 4. Nyalakan kembali pengecekan relasi
            $db->query('SET FOREIGN_KEY_CHECKS = 1');
            
            return redirect()->to(base_url('admin/nasabah'))->with('success', 'Data nasabah ' . esc($nasabah->nama_nasabah) . ' berhasil dihapus permanen!');
        } else {
            return redirect()->to(base_url('admin/nasabah'))->with('error', 'Gagal: Data tidak ditemukan.');
        }
    }

    // =========================================================================
    // 3. MANAJEMEN KATALOG SAMPAH & HARGA
    // =========================================================================
    public function katalog()
    {
        $data = [
            'title'   => 'Katalog Sampah & Harga',
            'katalog' => $this->kategoriModel->getKatalogWithUser()
        ];
        return view('admin/katalog_list', $data);
    }

    public function simpan_katalog()
    {
        $this->kategoriModel->save([
            'id_pengguna'     => session()->get('id'), 
            'katalog_sampah'  => $this->request->getVar('katalog_sampah'),
            'harga_per_kg'    => $this->request->getVar('harga_per_kg'),
            'tanggal_berlaku' => $this->request->getVar('tanggal_berlaku'),
        ]);
        return redirect()->to('/admin/katalog')->with('success', 'Katalog Berhasil Disimpan');
    }

    public function update_katalog()
    {
        $id = $this->request->getVar('id_katalog');
        $this->kategoriModel->update($id, [
            'katalog_sampah'  => $this->request->getVar('katalog_sampah'),
            'harga_per_kg'    => $this->request->getVar('harga_per_kg'),
            'tanggal_berlaku' => $this->request->getVar('tanggal_berlaku'),
        ]);

        // Perbaiki pesan notifikasi di sini
        return redirect()->to('/admin/katalog')->with('success', 'Data katalog berhasil diperbarui!');
    }
    
    public function hapus_katalog($id)
    {
        // Tambahkan pengecekan agar lebih aman
        if ($this->kategoriModel->find($id)) {
            $this->kategoriModel->delete($id);
            return redirect()->to('/admin/katalog')->with('success', 'Data katalog berhasil dihapus!');
        } else {
            return redirect()->to('/admin/katalog')->with('error', 'Data tidak ditemukan!');
        }
    }

    // =========================================================================
    // 4. MANAJEMEN TARIF PENJEMPUTAN
    // =========================================================================
    public function tarif_penjemputan()
    {
        $data = [
            'title'   => 'Tarif Penjemputan',
            'tarif'   => $this->tarifModel->getTarifLengkap(),
            'katalog' => $this->kategoriModel->findAll() 
        ];
        return view('admin/tarif_penjemputan_list', $data);
    }

    public function simpan_tarif_penjemputan()
    {
        $this->tarifModel->save([
            'id_katalog'        => $this->request->getVar('id_katalog'),
            'berat_sampah'      => $this->request->getVar('berat_sampah'),
            'harga_per_kg'      => $this->request->getVar('harga_per_kg'), 
            'biaya_penjemputan' => $this->request->getVar('biaya_penjemputan'),
            'tanggal_berlaku'   => $this->request->getVar('tanggal_berlaku'), 
            'keterangan'        => $this->request->getVar('keterangan')
        ]);
        return redirect()->to('/admin/tarif_penjemputan')->with('success', 'Tarif Penjemputan Berhasil Ditambahkan');
    }

    public function hapus_tarif($id)
    {
        $this->tarifModel->delete($id);
        return redirect()->to('/admin/tarif_penjemputan')->with('success', 'Tarif Berhasil Dihapus');
    }

    public function update_tarif_penjemputan()
    {
        $db = \Config\Database::connect();
        $id_tarif = $this->request->getPost('id_tarif');

        $db->table('tarif_penjemputan_sampah')->where('id_tarif', $id_tarif)->update([
            'id_katalog'        => $this->request->getPost('id_katalog'),
            'berat_sampah'     => $this->request->getPost('berat_sampah'),
            'harga_per_kg'     => $this->request->getPost('harga_per_kg'),
            'biaya_penjemputan'=> $this->request->getPost('biaya_penjemputan'),
            'tanggal_berlaku'  => $this->request->getPost('tanggal_berlaku'),
            'keterangan'       => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to(base_url('admin/tarif_penjemputan'))->with('success', 'Data ketentuan tarif penjemputan berhasil diperbarui!');
    }

    // =========================================================================
    // 5. MANAJEMEN JADWAL & OPERASIONAL (SINKRONISASI WILAYAH NASABAH)
    // =========================================================================
   public function jadwal_penjemputan()
    {
        $db = \Config\Database::connect();

        // DATA MANUAL: Gantikan query database dengan array manual
        $jam_operasional = [
            ['Id_operasional' => 1, 'Hari' => 'Senin', 'Status_buka' => 'Buka', 'Jam_mulai' => '08:00', 'Jam_selesai' => '16:00'],
            ['Id_operasional' => 2, 'Hari' => 'Selasa', 'Status_buka' => 'Buka', 'Jam_mulai' => '08:00', 'Jam_selesai' => '16:00'],
            ['Id_operasional' => 3, 'Hari' => 'Rabu', 'Status_buka' => 'Buka', 'Jam_mulai' => '08:00', 'Jam_selesai' => '16:00'],
            ['Id_operasional' => 4, 'Hari' => 'Kamis', 'Status_buka' => 'Buka', 'Jam_mulai' => '08:00', 'Jam_selesai' => '16:00'],
            ['Id_operasional' => 5, 'Hari' => 'Jumat', 'Status_buka' => 'Buka', 'Jam_mulai' => '08:00', 'Jam_selesai' => '16:00'],
            ['Id_operasional' => 6, 'Hari' => 'Sabtu', 'Status_buka' => 'Tutup', 'Jam_mulai' => '00:00', 'Jam_selesai' => '00:00'],
            ['Id_operasional' => 7, 'Hari' => 'Minggu', 'Status_buka' => 'Tutup', 'Jam_mulai' => '00:00', 'Jam_selesai' => '00:00'],
        ];
        // 2. Query Permintaan
        $builder = $db->table('permintaan_penjemputan_sampah p');
        $builder->select('p.*, n.nama_nasabah, n.id_kelurahan, j.Id_jadwal, j.Tanggal_jadwal, j.Jam_jadwal, j.Keterangan_admin, pg.id_pengguna as id_petugas, pg.nama_pengguna as nama_petugas');
        $builder->join('nasabah n', 'n.id_nasabah = p.id_nasabah', 'left');
        $builder->join('jadwal_penjemputan_sampah j', 'j.id_permintaan = p.Id_permintaan', 'left');
        $builder->join('pengguna pg', 'pg.id_pengguna = j.id_pengguna', 'left');
        $builder->orderBy('p.Tanggal_permintaan', 'DESC');

        $data = [
            'title'           => 'Jadwal & Operasional Penjemputan',
            'permintaan'      => $builder->get()->getResultArray(),
            // PASTIKAN JABATAN SESUAI DENGAN DATABASE ANDA
            'petugas'         => $db->table('pengguna')->whereIn('jabatan', ['Petugas', 'Operator Bank Sampah'])->get()->getResultArray(),
            'jam_operasional' => $jam_operasional
        ];
        
        return view('admin/jadwal_penjemputan_list', $data);
    }
    
    // =========================================================================
    // PROSES UTAMA: PLOTTING JADWAL OLEH ADMIN PUSAT (TOP-DOWN FLEET SYSTEM)
    // =========================================================================
    public function verifikasi_jadwal()
    {
        $db = \Config\Database::connect();
        
        $id_permintaan = $this->request->getPost('id_permintaan');
        $id_pengguna   = $this->request->getPost('id_petugas'); // ID Operator Bank Sampah yang ditunjuk
        $tanggal_jemput = $this->request->getPost('tanggal_jemput');
        $jam_jemput     = $this->request->getPost('jam_jemput');
        $keterangan     = $this->request->getPost('keterangan');

        if (empty($id_permintaan) || empty($id_pengguna) || empty($tanggal_jemput) || empty($jam_jemput)) {
            return redirect()->back()->with('error', 'Gagal memproses! Seluruh kolom data plotting wajib diisi.');
        }

        if (strlen($jam_jemput) === 5) {
            $jam_jemput = $jam_jemput . ':00';
        }

        $db->transStart();

        $cekJadwal = $db->table('jadwal_penjemputan_sampah')->where('id_permintaan', $id_permintaan)->get()->getRowArray();

        if (!$cekJadwal) {
            $db->table('jadwal_penjemputan_sampah')->insert([
                'id_permintaan'    => $id_permintaan,
                'id_pengguna'      => $id_pengguna,
                'Tanggal_jadwal'   => $tanggal_jemput,
                'Jam_jadwal'       => $jam_jemput,
                'Status_jadwal'    => 'Aktif',
                'Keterangan_admin' => '[Plotting Admin Pusat] ' . ($keterangan ?: '-')
            ]);
        } else {
            $db->table('jadwal_penjemputan_sampah')->where('id_permintaan', $id_permintaan)->update([
                'id_pengguna'      => $id_pengguna,
                'Tanggal_jadwal'   => $tanggal_jemput,
                'Jam_jadwal'       => $jam_jemput,
                'Status_jadwal'    => 'Aktif',
                'Keterangan_admin' => '[Update Admin Pusat] ' . ($keterangan ?: '-')
            ]);
        }

        // Ubah bagian ini di Admin.php
        $db->table('permintaan_penjemputan_sampah')
        ->where('Id_permintaan', $id_permintaan)
        ->update([
            'Status_penjemputan' => 'Diverifikasi' // Gunakan 'Diverifikasi' (sesuai ENUM tabel)
        ]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->to('/admin/jadwal_penjemputan')->with('error', 'Terjadi kesalahan sistem, gagal menyimpan plot armada.');
        }

        return redirect()->to('/admin/jadwal_penjemputan')->with('success', 'Berhasil melakukan plotting! Tugas penjemputan resmi didelegasikan ke Operator Bank Sampah harian.');
    }

    public function update_operasional()
    {
        $db = \Config\Database::connect();
        
        $id = $this->request->getVar('id_operasional');
        $status = $this->request->getVar('status_buka');
        $jam_mulai = $this->request->getVar('jam_mulai');
        $jam_selesai = $this->request->getVar('jam_selesai');

        if ($status == 'Tutup') {
            $jam_mulai = '00:00:00';
            $jam_selesai = '00:00:00';
        }

        
    }

    public function hapus_jadwal_penjemputan($id)
    {
        $db = \Config\Database::connect();
        $db->table('Permintaan_penjemputan_sampah')->where('Id_permintaan', $id)->delete();
        return redirect()->to('/admin/jadwal_penjemputan')->with('success', 'Data Berhasil Dihapus');
    }

    // =========================================================================
    // 6. FITUR LAPORAN
    // =========================================================================
    public function laporan()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pengelolaan_transaksi_bank_sampah');
        $builder->select('pengelolaan_transaksi_bank_sampah.*, nasabah.nama_nasabah, Kategori_sampah.katalog_sampah');
        $builder->join('nasabah', 'nasabah.id_nasabah = pengelolaan_transaksi_bank_sampah.id_nasabah');
        $builder->join('Kategori_sampah', 'Kategori_sampah.id_katalog = pengelolaan_transaksi_bank_sampah.id_katalog');
        $builder->orderBy('tanggal_transaksi', 'DESC');
        
        $data = [
            'title'   => 'Laporan Pengelolaan Bank Sampah',
            'laporan' => $builder->get()->getResultArray()
        ];
        return view('admin/laporan_view', $data);
    }

    // =========================================================================
    // AJAX DETEKSI BENTROK JADWAL PETUGAS UNTUK ADMIN PUSAT
    // =========================================================================
    public function cek_bentrok_petugas()
    {
        $db = \Config\Database::connect();
        
        $tanggal = $this->request->getGet('tanggal');
        $jam     = $this->request->getGet('jam');

        if (empty($tanggal) || empty($jam)) {
            return $this->response->setJSON([]);
        }

        $jam_mulai = date('H:i:s', strtotime($jam . ' -1 hour'));
        $jam_akhir = date('H:i:s', strtotime($jam . ' +1 hour'));

        $terjadwal = $db->table('jadwal_penjemputan_sampah')
            ->select('id_pengguna')
            ->where('Tanggal_jadwal', $tanggal)
            ->where('Jam_jadwal >=', $jam_mulai)
            ->where('Jam_jadwal <=', $jam_akhir)
            ->where('Status_jadwal', 'Aktif')
            ->get()
            ->getResultArray();

        $id_petugas_sibuk = array_column($terjadwal, 'id_pengguna');

        return $this->response->setJSON($id_petugas_sibuk);
    }

    public function wilayah()
    {
        $db = \Config\Database::connect();
        $data['kelurahan'] = $db->table('kelurahan')->get()->getResultArray();
        return view('admin/wilayah/index', $data);
    }

    public function simpan_kelurahan()
    {
        $db = \Config\Database::connect();
        $db->table('kelurahan')->insert([
            'nama_kelurahan' => $this->request->getPost('nama_kelurahan')
        ]);
        return redirect()->back()->with('success', 'Kelurahan berhasil ditambahkan!');
    }

    public function update_kelurahan()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id_kelurahan');
        $db->table('kelurahan')->where('id_kelurahan', $id)->update([
            'nama_kelurahan' => $this->request->getPost('nama_kelurahan')
        ]);
        return redirect()->back()->with('success', 'Kelurahan berhasil diupdate!');
    }

    public function hapus_kelurahan($id)
    {
        $db = \Config\Database::connect();
        $db->table('kelurahan')->where('id_kelurahan', $id)->delete();
        return redirect()->back()->with('success', 'Kelurahan berhasil dihapus!');
    }

    public function detail_rw($id_kelurahan)
    {
        $db = \Config\Database::connect();
        $data['kel'] = $db->table('kelurahan')->where('id_kelurahan', $id_kelurahan)->get()->getRowArray();
        $data['rw']  = $db->table('rw')->where('id_kelurahan', $id_kelurahan)->get()->getResultArray();
        return view('admin/wilayah/detail_rw', $data);
    }

    public function simpan_rw()
    {
        $db = \Config\Database::connect();
        $db->table('rw')->insert([
            'id_kelurahan' => $this->request->getPost('id_kelurahan'),
            'nama_rw'      => $this->request->getPost('nama_rw')
        ]);
        return redirect()->back()->with('success', 'RW berhasil ditambah!');
    }

    public function hapus_rw($id_rw)
    {
        $db = \Config\Database::connect();
        $db->table('rw')->where('id_rw', $id_rw)->delete();
        return redirect()->back()->with('success', 'RW berhasil dihapus!');
    }
        public function update_rw()
    {
        $db = \Config\Database::connect();
        $id_rw = $this->request->getPost('id_rw');
        $db->table('rw')->where('id_rw', $id_rw)->update([
            'nama_rw' => $this->request->getPost('nama_rw')
        ]);
        return redirect()->back()->with('success', 'RW berhasil diupdate!');
    }
}