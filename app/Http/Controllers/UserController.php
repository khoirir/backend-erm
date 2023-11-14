<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\DokterModel;
use App\Models\PegawaiModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function login(UserLoginRequest $request): UserResource {
        $data = $request->validated();
        $dokter = DokterModel::query()
            ->where('kd_dokter', $data['username'])
            ->where('status', '1')
            ->first();
        if(!$dokter){
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "USERNAME ATAU PASSWORD SALAH"
                ]
            ], 401));
        }

        $userKey = env('USER_KEY');
        $passwordKey = env('PASSWORD_KEY');
        $cekUser = DB::table('user')
            ->select(DB::raw('AES_DECRYPT(id_user, ?) as kd_dokter'))
            ->where('id_user', DB::raw('AES_ENCRYPT(?,?)'))
            ->where('password', DB::raw('AES_ENCRYPT(?,?)'))
            ->setBindings([$userKey, $dokter->kd_dokter, $userKey, $data['password'], $passwordKey])
            ->first();
        if(!$cekUser){
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "USERNAME ATAU PASSWORD SALAH"
                ]
            ], 401));
        }

        $pegawai = PegawaiModel::query()
            ->select('nik', 'nama', 'photo')
            ->where('nik', $dokter['kd_dokter'])
            ->where('stts_aktif', 'AKTIF')
            ->first();
        if(!$pegawai){
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "USERNAME ATAU PASSWORD SALAH"
                ]
            ], 401));
        }

        $user = new UserModel([
            "kd_dokter" => $dokter->kd_dokter,
            "expired_at" => Carbon::now()->addMonth()
        ]);
        $user->save();

        $dataResult = [
            "kdDokter" => $pegawai->nik,
            "namaDokter" => $pegawai->nama,
            "foto" => "http://192.168.30.20/webapps/penggajian/".$pegawai->photo,
            "token" => $user->id,
            "expiredToken" => date("Y-m-d H:i:s", strtotime($user->expired_at))
        ];
        return new UserResource($dataResult);
    }

    public function logout(Request $request): JsonResponse{
        $user = Auth::user();

        $user->expired_at = Carbon::now();
        $user->save();

        return response()->json([
            'data' => [
                'pesan' => 'LOGOUT BERHASIL'
            ]
        ])->setStatusCode(200);
    }
}
