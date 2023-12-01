<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/user/login', [\App\Http\Controllers\UserController::class, 'login']);
Route::middleware(\App\Http\Middleware\ApiAuthMiddleware::class)->group(function () {
    Route::delete('/user/logout', [\App\Http\Controllers\UserController::class, 'logout']);

    Route::controller(\App\Http\Controllers\PasienController::class)->group(function () {
        Route::get('/irj/pasien/{noRawat}', 'detail');
        Route::get('/irj/pasien', 'listData');
        Route::get('/irj/pasien-rujukan/{noRawat}', 'detailRujukan');
        Route::get('/irj/pasien-rujukan', 'listDataRujukan')->where('rujukan', 'rujukan');
    });

    Route::controller(\App\Http\Controllers\PemeriksaanController::class)->group(function () {
        Route::post('/irj/pasien/{noRawat}/pemeriksaan', 'simpan');
//        Route::post('/irj/pasien-rujukan/{noRawat}/pemeriksaan', 'simpan');
        Route::get('/irj/pasien/{noRawat}/pemeriksaan/{tanggalRawat}/{jamRawat}', 'detail');
    });
});
