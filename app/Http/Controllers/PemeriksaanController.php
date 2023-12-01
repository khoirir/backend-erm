<?php

namespace App\Http\Controllers;

use App\Http\Requests\PemeriksaanRequest;
use App\Http\Requests\RawatParameterRequest;
use App\Http\Resources\PemeriksaanResource;
use App\Models\PasienModel;
use App\Models\PemeriksaanIrjModel;
use App\Models\RegistrasiModel;
use App\Models\RujukanInternalModel;
use App\Models\UserModel;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PemeriksaanController extends Controller
{
    private function getPasien(string $noRawat, UserModel $userModel): void
    {
        $registrasiPasien = RegistrasiModel::where("no_rawat", $noRawat)
            ->where("kd_dokter", $userModel->kd_dokter)
            ->where("stts", "!=", "Batal")
            ->first();
        if (!$registrasiPasien) {
            $rujukanPasien = RujukanInternalModel::query()
                ->where('no_rawat', $noRawat)
                ->where('kd_dokter', $userModel->kd_dokter)
                ->first();
            if (!$rujukanPasien) {
                throw new HttpResponseException(response([
                    "error" => [
                        "pesan" => "PASIEN DENGAN NO. RAWAT " . $noRawat . " TIDAK DITEMUKAN"
                    ]
                ], 404));
            }
        }

    }

    public function simpan(string $noRawat, PemeriksaanRequest $pemeriksaanRequest): PemeriksaanResource
    {
        $user = Auth::user();
        $data = $pemeriksaanRequest->validated();
        $noRawat = str_replace('-', '/', $noRawat);

        $this->getPasien($noRawat, $user);

        $pemeriksaanPasien = PemeriksaanIrjModel::query()
            ->where('no_rawat', $noRawat)
            ->where('tgl_perawatan', $data['tanggalPerawatan'])
            ->where('jam_rawat', $data['jamPerawatan'])
            ->count();
        if ($pemeriksaanPasien == 1) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                        . $data['tanggalPerawatan'] . " " . $data['jamPerawatan'] . " SUDAH DIINPUTKAN"
                ]
            ], 400));
        }

        $pemeriksaanIrj = new PemeriksaanIrjModel([
            "no_rawat" => $noRawat,
            "tgl_perawatan" => $data['tanggalPerawatan'],
            "jam_rawat" => $data['jamPerawatan'],
            "suhu_tubuh" => $data['suhuTubuh'],
            "tensi" => $data['tensi'],
            "nadi" => $data['nadi'],
            "respirasi" => $data['respirasi'],
            "tinggi" => $data['tinggiBadan'],
            "berat" => $data['beratBadan'],
            "spo2" => $data['spo2'],
            "gcs" => $data['gcs'],
            "kesadaran" => $data['kesadaran'],
            "keluhan" => $data['keluhan'],
            "pemeriksaan" => $data['pemeriksaan'],
            "alergi" => $data['alergi'],
            "lingkar_perut" => $data['lingkarPerut'],
            "rtl" => $data['tindakLanjut'],
            "penilaian" => $data['penilaian'],
            "instruksi" => $data['instruksi'],
            "evaluasi" => $data['evaluasi'],
            "nip" => $user->kd_dokter
        ]);
        $pemeriksaanIrj->save();

        return new PemeriksaanResource($pemeriksaanIrj);
    }

    public function detail(RawatParameterRequest $rawatParameterRequest): PemeriksaanResource
    {
        $user = Auth::user();
        $data = $rawatParameterRequest->validated();

        $noRawat = str_replace('-', '/', $data['noRawat']);
        $tanggalRawat = $data['tanggalRawat'];
        $jamRawat = $data['jamRawat'];

        $this->getPasien($noRawat, $user);

        $pemeriksaanPasien = PemeriksaanIrjModel::query()
            ->where('no_rawat', $noRawat)
            ->where('tgl_perawatan', $tanggalRawat)
            ->where('jam_rawat', $jamRawat)
            ->first();

        if (!$pemeriksaanPasien) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT ".$noRawat." DAN WAKTU PERAWATAN "
                        .$tanggalRawat." ".$jamRawat." TIDAK DITEMUKAN"
                ]
            ], 404));
        }

        return new PemeriksaanResource($pemeriksaanPasien);
    }
}
