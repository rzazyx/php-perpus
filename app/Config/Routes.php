<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Admin');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->add('/', 'Root::index');
$routes->add('/proseslogin', 'Root::login');
$routes->add('/proseslogout', 'Root::logout');
$routes->add('/kunjungan', 'Root::tampilkunjungan');
$routes->add('/berkunjung', 'Root::simpankunjungan');
$routes->add('/pencarian', 'Root::caripustaka');
$routes->add('/cetakpustaka', 'Root::cetakpustaka');
$routes->add('/cetakpeminjaman', 'Root::prosescetakpeminjaman');
$routes->add('/cetakmutasi', 'Root::prosescetakmutasi');
$routes->add('/cetakkunjungan/(:any)', 'Root::prosescetakkunjungan/$1');
$routes->add('/cetakanggota', 'Root::prosescetakanggota');
$routes->add('/cetaklabel', 'Root::prosescetaklabel');
$routes->add('/cetakstbp/(:any)', 'Root::prosescetakstbp/$1');

// ADMINISTRATOR =======================================
$routes->add('/a', 'Admin::index');
$routes->add('/a/profil', 'Admin::tampilprofil');
$routes->add('/a/profilubah', 'Admin::ubahprofil');
$routes->add('/a/dasar', 'Admin::tampildasar');
$routes->add('/a/dasarubah', 'Admin::ubahdasar');
$routes->add('/a/akses', 'Admin::tampilakses');
$routes->add('/a/aksesubah', 'Admin::ubahakses');

$routes->add('/a/pustakawan', 'Admin_pustakawan::index');
$routes->add('/a/pustakawansimpan', 'Admin_pustakawan::simpan');
$routes->add('/a/pustakawanehapus/(:any)', 'Admin_pustakawan::hapus/$1');

$routes->add('/a/rak', 'Admin_rak::index');
$routes->add('/a/raksimpan', 'Admin_rak::simpan');
$routes->add('/a/rakhapus/(:any)', 'Admin_rak::hapus/$1');

$routes->add('/a/klasifikasi', 'Admin_klasifikasi::index');
$routes->add('/a/klasifikasisimpan', 'Admin_klasifikasi::simpan');
$routes->add('/a/klasifikasihapus/(:any)', 'Admin_klasifikasi::hapus/$1');

$routes->add('/a/pustaka', 'Admin_pustaka::index');
$routes->add('/a/pustakadetail/(:any)', 'Admin_pustaka::detail/$1');

$routes->add('/a/anggota', 'Admin_anggota::index');
$routes->add('/a/anggotadetail/(:any)', 'Admin_anggota::detail/$1');

$routes->add('/a/pinjam', 'Admin_siklus::pinjam');
$routes->add('/a/pinjamtampil', 'Admin_siklus::pinjamtampil');
$routes->add('/a/mutasi', 'Admin_siklus::mutasi');
$routes->add('/a/mutasitampil', 'Admin_siklus::mutasitampil');
$routes->add('/a/kunjungan', 'Admin_siklus::kunjungan');
$routes->add('/a/kunjungantampil', 'Admin_siklus::kunjungantampil');


// PUSTAKAWAN =======================================
$routes->add('/p', 'Pustakawan::index');
$routes->add('/p/profil', 'Pustakawan::tampilprofil');
$routes->add('/p/profilubah', 'Pustakawan::ubahprofil');
$routes->add('/p/akses', 'Pustakawan::tampilakses');
$routes->add('/p/aksesubah', 'Pustakawan::ubahakses');

$routes->add('/p/pustaka', 'Pustakawan_pustaka::index');
$routes->add('/p/pustakabaru', 'Pustakawan_pustaka::baru');
$routes->add('/p/pustakatambahpengarang', 'Pustakawan_pustaka::tambahpengarang');
$routes->add('/p/pustakahapuspengarang/(:any)/(:any)', 'Pustakawan_pustaka::hapuspengarang/$1/$2');
$routes->add('/p/pustakasimpan', 'Pustakawan_pustaka::simpan');
$routes->add('/p/pustakadetail/(:any)', 'Pustakawan_pustaka::detail/$1');
$routes->add('/p/pustakahapus/(:any)', 'Pustakawan_pustaka::hapus/$1');
$routes->add('/p/pustakaubah', 'Pustakawan_pustaka::ubah');

$routes->add('/p/anggota', 'Pustakawan_anggota::index');
$routes->add('/p/anggotasimpan', 'Pustakawan_anggota::simpan');
$routes->add('/p/anggotadetail/(:any)', 'Pustakawan_anggota::detail/$1');
$routes->add('/p/anggotahapus/(:any)', 'Pustakawan_anggota::hapus/$1');

$routes->add('/p/mutasi', 'Pustakawan_mutasi::index');
$routes->add('/p/mutasimasukbaru', 'Pustakawan_mutasi::masukbaru');
$routes->add('/p/mutasikeluarbaru', 'Pustakawan_mutasi::keluarbaru');
$routes->add('/p/mutasitambahdetail', 'Pustakawan_mutasi::tambahdetail');
$routes->add('/p/mutasihapusdetail/(:any)/(:any)', 'Pustakawan_mutasi::hapusdetail/$1/$2');
$routes->add('/p/mutasisimpan', 'Pustakawan_mutasi::simpan');

$routes->add('/p/pinjam', 'Pustakawan_pinjam::index');
$routes->add('/p/pinjambaru', 'Pustakawan_pinjam::baru');
$routes->add('/p/pinjamtambahdetail', 'Pustakawan_pinjam::tambahdetail');
$routes->add('/p/pinjamhapusdetail/(:any)/(:any)', 'Pustakawan_pinjam::hapusdetail/$1/$2');
$routes->add('/p/pinjamsimpan', 'Pustakawan_pinjam::simpan');
$routes->add('/p/pinjamkembali', 'Pustakawan_pinjam::kembali');

$routes->add('/p/kembali', 'Pustakawan_kembali::index');
$routes->add('/p/kembalisimpan', 'Pustakawan_kembali::simpan');

$routes->add('/p/kunjungan', 'Pustakawan_siklus::kunjungan');
$routes->add('/p/kunjungantampil', 'Pustakawan_siklus::kunjungantampil');

// PUSTAKAWAN =======================================
$routes->add('/ag', 'Anggota::index');
$routes->add('/ag/profil', 'Anggota::tampilprofil');
$routes->add('/ag/profilubah', 'Anggota::ubahprofil');
$routes->add('/ag/akses', 'Anggota::tampilakses');
$routes->add('/ag/aksesubah', 'Anggota::ubahakses');
$routes->add('/ag/riwayat', 'Anggota::tampilriwayat');
$routes->add('/ag/riwayatdetail/(:any)', 'Anggota::detailriwayat/$1');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
