<?php

namespace App\Http\Controllers;

use App\Http\Resources\PasienResource;
use App\Models\RegistrasiModel;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasienController extends Controller
{
    public function get(string $noRawat): PasienResource{
        $user = Auth::user();
        $pasien = RegistrasiModel::query()
            ->with("pasien")
            ->with("poli")
            ->with("penjab")
            ->where("no_rawat", $noRawat)
            ->where("kd_dokter", $user->kd_dokter)
            ->where("stts", "!=","Batal")
            ->first();
        if(!$pasien){
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "DATA PASIEN TIDAK DITEMUKAN"
                ]
            ], 404));
        }

        $tanggalLahirObj = Carbon::parse($pasien->pasien->tgl_lahir);
        $umur = $tanggalLahirObj->diff(Carbon::now());
        $dataResult = [
            "noRawat" => $pasien->no_rawat,
            "noRegistrasi" => $pasien->no_reg,
            "tanggalRegistrasi" => $pasien->tgl_registrasi,
            "jamRegistrasi" => $pasien->jam_reg,
            "noRM" => $pasien->no_rkm_medis,
            "namaPasien" => $pasien->pasien->nm_pasien,
            "alamat" => $pasien->pasien->alamat,
            "tanggalLahir" => $pasien->pasien->tgl_lahir,
            "umur" => $umur->y.' Tahun '.$umur->m.' Bulan '.$umur->d.' Hari',
            "jenisKelamin" => $pasien->pasien->jk,
            "jenisBayar" => $pasien->penjab->png_jawab,
            "noTelpon" => $pasien->pasien->no_tlp,
            "statusPasien" => $pasien->stts,
            "statusPoli" => $pasien->status_poli
        ];
        return new PasienResource($dataResult);
    }
}
