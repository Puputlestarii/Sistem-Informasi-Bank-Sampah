<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    // Fungsi untuk mengambil semua permintaan nasabah beserta status jadwalnya
    public function getPermintaanLengkap()
    {
        return $this->db->table('Permintaan_penjemputan_sampah p')
            ->select('p.*, n.nama_nasabah, n.alamat, n.nomor_telepon, j.Jam_jadwal, j.Tanggal_jadwal as tgl_fix, pg.nama_pengguna as nama_petugas')
            ->join('nasabah n', 'n.id_nasabah = p.id_nasabah')
            ->join('Jadwal_penjemputan_sampah j', 'j.id_permintaan = p.Id_permintaan', 'left')
            ->join('pengguna pg', 'pg.id_pengguna = j.id_pengguna', 'left')
            ->orderBy('p.Tanggal_permintaan', 'DESC')
            ->get()->getResultArray();
    }

}