<?php

namespace App\Models;

use CodeIgniter\Model;

class RwModel extends Model
{
    protected $table            = 'rw';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id_kelurahan', 'nama_rw'];
}