<?php

namespace App\Models;

use CodeIgniter\Model;

class TarifPenjemputanModel extends Model
{
    protected $table            = 'tarif_penjemputan_sampah';
    protected $primaryKey       = 'id_tarif';
    protected $allowedFields    = [
        'id_katalog', 
        'berat_sampah', 
        'harga_per_kg', // Kolom baru ditambahkan
        'biaya_penjemputan', 
        'tanggal_berlaku', // Kolom baru ditambahkan
        'keterangan'
    ];

    // Mengambil tarif lengkap dengan nama jenis sampahnya
    public function getTarifLengkap()
    {
        return $this->select('tarif_penjemputan_sampah.*, kategori_sampah.katalog_sampah')
                    ->join('kategori_sampah', 'kategori_sampah.id_katalog = tarif_penjemputan_sampah.id_katalog')
                    ->findAll();
    }
}