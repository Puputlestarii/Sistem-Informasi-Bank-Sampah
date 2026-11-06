<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();
        
        $nama    = session()->get('nama');
        $role    = session()->get('role');
        $jabatan = session()->get('jabatan');

        if ($role == 'nasabah') {
            $jabatan = 'Nasabah Mandiri';
        }

        $data = [
            'title'          => 'Dashboard Utama',
            'jumlah_user'    => $db->table('pengguna')->countAll(),
            'jumlah_nasabah' => $db->table('nasabah')->countAll(),
            
            // PERBAIKAN DI SINI:
            // Sesuai gambar ERD kamu, nama tabelnya adalah 'Kategori_sampah'
            'jumlah_katalog' => $db->table('Kategori_sampah')->countAll(), 
            
            'nama'           => $nama,
            'jabatan'        => $jabatan,
            'role'           => $role
        ];

        return view('dashboard/index', $data);
    }
}