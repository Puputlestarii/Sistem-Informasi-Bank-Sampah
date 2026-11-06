<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori_sampah';
    protected $primaryKey       = 'id_katalog';
    protected $allowedFields    = [
        'id_pengguna', 
        'katalog_sampah', 
        'harga_per_kg', 
        'tanggal_berlaku'
    ];

    // Fungsi tambahan untuk mengambil data katalog beserta nama penginputnya
    public function getKatalogWithUser()
    {
        return $this->select('kategori_sampah.*, pengguna.nama_pengguna')
                    ->join('pengguna', 'pengguna.id_pengguna = kategori_sampah.id_pengguna')
                    ->findAll();
    }
}