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

   Route::get('/irj/pasien/{noRawat}', [\App\Http\Controllers\PasienController::class, 'get'])->where('noRawat', '.*');;
});
