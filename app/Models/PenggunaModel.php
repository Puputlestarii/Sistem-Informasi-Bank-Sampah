<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table            = 'pengguna';
    protected $primaryKey       = 'id_pengguna';
    protected $allowedFields    = [
        'nama_pengguna', 'username', 'password', 
        'email', 'jabatan', 'id_kelurahan', 'id_rw'
    ];
}