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
    
    Route::group(['prefix' => 'produk'], function () {
        Route::get('/', 'ApiProdukController@data');
    });

    Route::group(['prefix' => 'general'], function () {
        Route::get('/', 'ApiGeneralController@data');
    });

    Route::group(['prefix' => 'jasa'], function () {
        Route::get('/', 'ApiJasaController@data');
        Route::get('/harga', 'ApiJasaController@harga');
        Route::get('/hargaperkendaraan', 'ApiJasaController@hargaPerKendaraan');
        Route::get('/durasi', 'ApiJasaController@durasi');
    });

    Route::group(['prefix' => 'merek_kendaraan'], function () {
        Route::get('/', 'ApiMerekKendaraanController@data');
    });

    Route::group(['prefix' => 'kendaraan'], function () {
        Route::get('/', 'ApiKendaraanController@data');
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

});
