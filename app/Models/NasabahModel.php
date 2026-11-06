<?php

namespace App\Models;

use CodeIgniter\Model;

class NasabahModel extends Model
{
    protected $table            = 'nasabah';
    protected $primaryKey       = 'id_nasabah';
    protected $allowedFields    = [
        'nama_nasabah', 'username', 'password', 
        'alamat', 'nomor_telepon', 'saldo', 
        'id_kelurahan', 'id_rw'
    ];

    /**
     * Mengambil data nasabah lengkap dengan nama kelurahan & RW
     * Menggunakan join berdasarkan kolom ID (Foreign Key)
     */
    public function getNasabahLengkap()
    {
        return $this->db->table('nasabah n')
            ->select('n.*, kl.nama_kelurahan, r.nama_rw')
            ->join('kelurahan kl', 'kl.id_kelurahan = n.id_kelurahan', 'left')
            ->join('rw r', 'r.id_rw = n.id_rw', 'left')
            ->get()
            ->getResultArray();
    }
}