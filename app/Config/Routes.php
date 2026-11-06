<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================================================================
// 1. AKSES PUBLIK (Landing Page & Auth)
// =========================================================================
$routes->get('/', 'Auth::home'); // Landing Page baru
$routes->get('auth', 'Auth::index'); // Form Login
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Rute Registrasi Nasabah
$routes->get('register', 'Auth::register'); 
$routes->post('auth/simpan_register', 'Auth::simpan_register');
$routes->get('auth/get_rw_by_kelurahan/(:num)', 'Auth::get_rw_by_kelurahan/$1');

// =========================================================================
// 2. AKSES GRUP ADMIN & KEMANTREN
// =========================================================================
$routes->group('admin', function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('pengguna', 'Admin::pengguna');
    $routes->post('pengguna/simpan', 'Admin::simpan_pengguna');
    $routes->post('pengguna/update', 'Admin::update_pengguna');
    $routes->get('pengguna/hapus/(:num)', 'Admin::hapus_pengguna/$1'); 
    $routes->get('nasabah', 'Admin::nasabah');
    $routes->get('nasabah/hapus/(:num)', 'Admin::hapus_nasabah/$1'); 
    $routes->get('katalog', 'Admin::katalog');
    $routes->post('katalog/simpan', 'Admin::simpan_katalog');
    $routes->post('katalog/update', 'Admin::update_katalog'); 
    $routes->get('katalog/hapus/(:num)', 'Admin::hapus_katalog/$1'); 
    $routes->get('tarif_penjemputan', 'Admin::tarif_penjemputan');
    $routes->post('tarif_penjemputan/simpan', 'Admin::simpan_tarif_penjemputan');
    $routes->post('tarif_penjemputan/update', 'Admin::update_tarif_penjemputan');
    $routes->get('tarif_penjemputan/hapus/(:num)', 'Admin::hapus_tarif/$1');
    $routes->get('jadwal_penjemputan', 'Admin::jadwal_penjemputan'); 
    $routes->post('jadwal_penjemputan/simpan', 'Admin::simpan_jadwal_penjemputan');
    $routes->get('jadwal_penjemputan/hapus/(:num)', 'Admin::hapus_jadwal_penjemputan/$1');
    $routes->post('update_operasional', 'Admin::update_operasional');
    $routes->post('verifikasi_jadwal', 'Admin::verifikasi_jadwal');
    $routes->get('cek_bentrok_petugas', 'Admin::cek_bentrok_petugas');
    $routes->get('laporan', 'Admin::laporan');
    $routes->get('wilayah', 'Admin::wilayah');
    $routes->get('wilayah/detail_rw/(:num)', 'Admin::detail_rw/$1');
    
    $routes->post('wilayah/simpan_kelurahan', 'Admin::simpan_kelurahan');
    $routes->post('wilayah/update_kelurahan', 'Admin::update_kelurahan');
    $routes->get('wilayah/hapus_kelurahan/(:num)', 'Admin::hapus_kelurahan/$1');
    $routes->post('wilayah/simpan_rw', 'Admin::simpan_rw');
    $routes->post('wilayah/update_rw', 'Admin::update_rw'); 
    $routes->get('wilayah/hapus_rw/(:num)', 'Admin::hapus_rw/$1');
});
// =========================================================================
// 3. AKSES GRUP NASABAH
// =========================================================================
$routes->group('nasabah', function($routes) {
    $routes->get('dashboard', 'Nasabah::dashboard');
    $routes->get('minta_jemput', 'Nasabah::minta_jemput');
    $routes->post('minta_jemput/simpan', 'Nasabah::simpan_permintaan');
    $routes->get('riwayat_timbangan', 'Nasabah::riwayat_timbangan');
    $routes->get('penarikan_saldo', 'Nasabah::penarikan_saldo');
    $routes->post('penarikan_saldo/simpan', 'Nasabah::simpan_penarikan');
});

// =========================================================================
// 4. AKSES GRUP OPERATOR BANK SAMPAH
// =========================================================================
$routes->group('operator', function($routes) {
    $routes->get('dashboard', 'Operator::dashboard');
    $routes->get('transaksi_timbangan', 'Operator::transaksi_timbangan');
    $routes->post('transaksi_timbangan/simpan', 'Operator::simpan_timbangan');
    $routes->post('verifikasi_penjemputan', 'Operator::verifikasi_penjemputan');
    $routes->post('verifikasi_penarikan', 'Operator::verifikasi_penarikan');
    
    // Perbaikan: Hapus prefiks 'operator/' karena sudah ada di dalam grup
    $routes->match(['get', 'post'], 'tarik_pendapatan', 'Operator::proses_tarik_pendapatan');
    
    // Rute Laporan
    $routes->get('laporan_transaksi', 'Operator::laporan_transaksi');
    $routes->get('cetak_laporan', 'Operator::cetak_laporan');
    $routes->get('laporan_penjemputan', 'Operator::laporan_penjemputan');
});

// =========================================================================
// 5. AKSES GRUP OPERATOR KELURAHAN
// =========================================================================
$routes->group('kelurahan', function($routes) {
    $routes->get('dashboard', 'OperatorKelurahan::dashboard');
    $routes->post('validasi_transaksi', 'OperatorKelurahan::validasi_transaksi');
    $routes->get('laporan', 'OperatorKelurahan::laporan');
    $routes->get('cetak_pdf', 'OperatorKelurahan::cetak_pdf');
});

// =========================================================================
// 6. AKSES GRUP KEPALA KEMANTREN
// =========================================================================
$routes->group('kemantren', function($routes) {
    $routes->get('dashboard', 'Kemantren::dashboard');
    $routes->get('laporan', 'Kemantren::laporan');
    $routes->get('cetak_pdf', 'Kemantren::cetak_pdf');
});