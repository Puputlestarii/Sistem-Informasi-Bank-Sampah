<?php

namespace App\Controllers;

use App\Models\PenggunaModel;

class Auth extends BaseController
{
    protected $penggunaModel;

    public function __construct()
    {
        $this->penggunaModel = new PenggunaModel();
    }

    // --- HALAMAN LANDING PAGE (Akses: /) ---
    public function home()
    {
        if (session()->get('logged_in')) {
            return $this->redirectDashboard(session()->get('role'));
        }
        return view('home');
    }

    // --- HALAMAN LOGIN (Akses: /auth) ---
    public function index()
    {
        if (session()->get('logged_in')) {
            return $this->redirectDashboard(session()->get('role'));
        }
        return view('auth/login');
    }

    // --- FUNGSI BANTU PENGALIHAN DASHBOARD ---
    private function redirectDashboard($role)
    {
        switch ($role) {
            case 'nasabah': return redirect()->to('/nasabah/dashboard');
            case 'operator_bank': return redirect()->to('/operator/dashboard');
            case 'operator_kelurahan': return redirect()->to('/kelurahan/dashboard');
            case 'kepala_kemantren': return redirect()->to('/kemantren/dashboard');
            default: return redirect()->to('/admin/dashboard');
        }
    }

    // --- PROSES LOGIN ---
    public function login()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // 1. Cek Admin/Petugas/Operator/Pimpinan
        $user = $this->penggunaModel->where('username', $username)->first();
        if ($user && password_verify($password, $user['password'])) {
            
            $jabatanUser = isset($user['jabatan']) ? strtolower(trim($user['jabatan'])) : '';
            
            $sessionData = [
                'id'          => $user['id_pengguna'],
                'id_pengguna' => $user['id_pengguna'], 
                'username'    => $user['username'],
                'nama'        => $user['nama_pengguna'],
                'jabatan'     => $user['jabatan'],
                'kelurahan'   => isset($user['kelurahan']) ? $user['kelurahan'] : '', 
                'logged_in'   => true
            ];

            if (strpos($jabatanUser, 'kemantren') !== false) {
                $sessionData['role'] = 'kepala_kemantren';
            } elseif (strpos($jabatanUser, 'kelurahan') !== false) {
                $sessionData['role'] = 'operator_kelurahan';
            } elseif (strpos($jabatanUser, 'sampah') !== false || $jabatanUser === 'operator') {
                $sessionData['role'] = 'operator_bank';
            } else {
                $sessionData['role'] = 'admin_petugas';
            }

            session()->set($sessionData);
            return $this->redirectDashboard($sessionData['role'])->with('success', 'Selamat Datang!');
        }

        // 2. Cek Nasabah
        $db = \Config\Database::connect();
        $nasabah = $db->table('nasabah')->where('username', $username)->get()->getRowArray();
        
        if ($nasabah && password_verify($password, $nasabah['password'])) {
            session()->set([
                'id'         => $nasabah['id_nasabah'],
                'id_nasabah' => $nasabah['id_nasabah'], 
                'username'   => $nasabah['username'],
                'nama'       => $nasabah['nama_nasabah'],
                'kelurahan'  => isset($nasabah['kelurahan']) ? $nasabah['kelurahan'] : '',
                'rw'         => isset($nasabah['rw']) ? $nasabah['rw'] : '', 
                'logged_in'  => true, 
                'role'       => 'nasabah'
            ]);
            return redirect()->to('/nasabah/dashboard');
        }

        return redirect()->to('/auth')->with('error', 'Username atau Password salah!');
    } 

    // --- REGISTRASI NASABAH ---
    public function register()
    {
        $db = \Config\Database::connect();
        $data['kelurahan'] = $db->table('kelurahan')->get()->getResultArray();
        return view('auth/register_nasabah', $data);
    }

    public function get_rw_by_kelurahan($id_kelurahan)
    {
        $db = \Config\Database::connect();
        $data = $db->table('rw')->where('id_kelurahan', $id_kelurahan)->get()->getResultArray();
        return $this->response->setJSON($data);
    }

    public function simpan_register()
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil data dengan lebih aman
        $id_kelurahan = $this->request->getPost('id_kelurahan');
        $id_rw        = $this->request->getPost('rw');

        // 2. Validasi: Jika data kosong, jangan dilanjutkan ke database
        if (empty($id_kelurahan) || empty($id_rw)) {
            return redirect()->back()->withInput()->with('error', 'Kelurahan dan RW wajib dipilih!');
        }

        // 3. Proses Simpan
        $data = [
            'nama_nasabah'  => $this->request->getPost('nama_nasabah'),
            'latitude'      => $this->request->getPost('latitude'),
            'longitude'     => $this->request->getPost('longitude'),
            'username'      => $this->request->getPost('username'),
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'alamat'        => $this->request->getPost('alamat'),
            'nomor_telepon' => $this->request->getPost('nomor_telepon'),
            'saldo'         => 0,
            'id_kelurahan'  => $id_kelurahan, 
            'id_rw'         => $id_rw        
        ];

        $db->table('nasabah')->insert($data);
        
        return redirect()->to('/auth')->with('success', 'Registrasi berhasil! Silakan Login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}