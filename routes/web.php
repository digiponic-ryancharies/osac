<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function () {

    Route::group(['prefix' => 'general'], function () {
        Route::get('/', 'ApiGeneralController@data');
    });

    Route::group(['prefix' => 'jasa'], function () {
        Route::get('/', 'ApiJasaController@data');
        Route::get('/harga', 'ApiJasaController@harga');
        Route::get('/durasi', 'ApiJasaController@durasi');
    });

    Route::group(['prefix' => 'pelanggan'], function () {
        Route::post('/', 'ApiPelangganController@store');
        Route::post('/kendaraan', 'ApiPelangganController@storeKendaraan');
        Route::post('/alamat', 'ApiPelangganController@storeAlamat');

        Route::get('/', 'ApiPelangganController@data');
        Route::get('/kendaraan', 'ApiPelangganController@kendaraan');
        Route::get('/alamat', 'ApiPelangganController@alamat');
    });

    Route::group(['prefix' => 'pos'], function () {
        Route::post('/', 'ApiPOSController@store');
        Route::post('/kendaraan', 'ApiPOSController@storeKendaraan');
        Route::post('/alamat', 'ApiPOSController@storeAlamat');

        Route::get('/', 'ApiPOSController@data');
        Route::get('/kendaraan', 'ApiPOSController@kendaraan');
        Route::get('/alamat', 'ApiPOSController@alamat');
    });

    Route::group(['prefix' => 'posjasa'], function () {
        Route::post('/', 'ApiPOSJasaController@store');
        Route::post('/kendaraan', 'ApiPOSJasaController@storeKendaraan');
        Route::post('/alamat', 'ApiPOSJasaController@storeAlamat');

        Route::get('/', 'ApiPOSJasaController@data');
        Route::get('/kendaraan', 'ApiPOSJasaController@kendaraan');
        Route::get('/alamat', 'ApiPOSJasaController@alamat');
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'API\UserController@login');
        Route::post('daftar', 'API\UserController@register');
        Route::post('edit', 'API\UserController@edit');
        Route::get('detail/{id}', 'API\UserController@detail');
        Route::get('logout', 'API\UserController@logout');
    });

    Route::group(['prefix' => 'kendaraan'], function () {
        Route::get('daftar/{merk}', 'API\KendaraanController@allByMerk');
        Route::get('merk', 'API\KendaraanController@merkList');
        Route::get('jenis', 'API\KendaraanController@vehicleType');
        Route::get('pelanggan/{idPelanggan}', 'API\KendaraanController@kendaraanPelanggan');
        Route::get('detail/{idKendaraan}', 'API\KendaraanController@detail');
        Route::post('tambah', 'API\KendaraanController@tambah');
        Route::post('edit', 'API\KendaraanController@edit');
        Route::delete('hapus/{id}', 'API\KendaraanController@hapus');
    });

    Route::group(['prefix' => 'jasa'], function () {
        Route::get('jenis', 'API\JasaController@jenisJasaList');
        Route::get('daftar/{id_jenis}', 'API\JasaController@jasaList');
        Route::get('harga', 'API\JasaController@hargaJasaList');
        Route::get('durasi', 'API\JasaController@durasiJasaList');
    });

    Route::group(['prefix' => 'reservasi'], function () {
        Route::get('cabang', 'API\ReservasiController@cabangList');
        Route::get('slots/{cabang}/{tgl}', 'API\ReservasiController@slotList');
        Route::post('tambah', 'API\ReservasiController@daftar');
        Route::get('pelanggan/{id}', 'API\ReservasiController@reservasiPelanggan');
        Route::get('detail/{kodebooking}', 'API\ReservasiController@detail');
    });

    Route::group(['prefix' => 'produk'], function () {
        Route::get('/', 'API\ProdukController@all');
        Route::get('/kategori', 'API\ProdukController@category');
        Route::get('/by-kategori/{kategori}', 'API\ProdukController@byCategory');
    });
});
