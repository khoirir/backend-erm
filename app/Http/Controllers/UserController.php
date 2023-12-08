<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Pegawai;
use App\Models\UserErm;
use Illuminate\Support\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $userKey = env('USER_KEY');
        $passwordKey = env('PASSWORD_KEY');
        $cekUser = DB::table('user')
            ->select(DB::raw('AES_DECRYPT(id_user, ?) as kd_dokter'))
            ->where('id_user', DB::raw('AES_ENCRYPT(?,?)'))
            ->where('password', DB::raw('AES_ENCRYPT(?,?)'))
            ->setBindings([$userKey, $data['username'], $userKey, $data['password'], $passwordKey])
            ->first();
        if (!$cekUser) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "USERNAME ATAU PASSWORD SALAH"
                ]
            ], 401));
        }

        $pegawai = Pegawai::query()
            ->where('nik', $cekUser->kd_dokter)
            ->where('stts_aktif', '!=', 'KELUAR')
            ->whereRelation('dokter', 'status', '1')
            ->first();
        if (!$pegawai) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "USERNAME ATAU PASSWORD SALAH"
                ]
            ], 401));
        }

        $user = new UserErm([
            "kd_dokter" => $pegawai->dokter->kd_dokter,
            "expired_at" => Carbon::now()->addMonth()
        ]);
        $user->save();
        return (new UserResource($user))->response()->setStatusCode(200);
    }

    public function logout(Request $request): JsonResponse
    {
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
