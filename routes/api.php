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
        Route::get('/irj/pasien-rujukan', 'listDataRujukan');
    });

    Route::controller(\App\Http\Controllers\PemeriksaanController::class)->group(function () {
        Route::post('/irj/pasien/{noRawat}/pemeriksaan', 'simpan');
        Route::get('/irj/pasien/{noRM}/pemeriksaan', 'listData')->where('noRM', '[0-9]{6,8}+');
        Route::get('/irj/pasien/{noRawat}/pemeriksaan/{tanggalRawat}/{jamRawat}', 'detail')->where([
            'tanggalRawat' => '((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))+',
            'jamRawat' => '(([0-1][0-9])|([2][0-3])):([0-5][0-9]):([0-5][0-9])+'
        ]);
        Route::put('/irj/pasien/{noRawat}/pemeriksaan/{tanggalRawat}/{jamRawat}', 'edit')->where([
            'tanggalRawat' => '((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))+',
            'jamRawat' => '(([0-1][0-9])|([2][0-3])):([0-5][0-9]):([0-5][0-9])+'
        ]);
        Route::delete('/irj/pasien/{noRawat}/pemeriksaan/{tanggalRawat}/{jamRawat}', 'hapus')->where([
            'tanggalRawat' => '((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))+',
            'jamRawat' => '(([0-1][0-9])|([2][0-3])):([0-5][0-9]):([0-5][0-9])+'
        ]);
    });
});
