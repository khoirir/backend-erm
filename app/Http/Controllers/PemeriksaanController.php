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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PemeriksaanController extends Controller
{
    private function getRegistrasiPasien(string $noRawat, UserModel $userModel): void
    {
        $registrasiPasien = RegistrasiModel::query()
            ->where("no_rawat", $noRawat)
            ->where("kd_dokter", $userModel->kd_dokter)
            ->where("stts", "!=", "Batal")
            ->first();
        if (!$registrasiPasien) {
            $rujukanPasien = RujukanInternalModel::query()
                ->where('no_rawat', $noRawat)
                ->where('kd_dokter', $userModel->kd_dokter)
                ->first();
            if (!$rujukanPasien) throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PASIEN DENGAN NO. RAWAT " . $noRawat . " TIDAK DITEMUKAN"
                ]
            ], 404));
        }
    }

    private function builderPemeriksaanIrjPasien(string $noRawat, string $tanggalRawat, string $jamRawat): Builder
    {
        return PemeriksaanIrjModel::query()
            ->where('no_rawat', $noRawat)
            ->where('tgl_perawatan', $tanggalRawat)
            ->where('jam_rawat', $jamRawat);
    }

    private function transformRequestData(array $data, UserModel $userModel): array
    {
        return [
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
            "nip" => $userModel->kd_dokter
        ];
    }

    public function simpan(string $noRawat, PemeriksaanRequest $pemeriksaanRequest): PemeriksaanResource
    {
        $user = Auth::user();
        $data = $pemeriksaanRequest->validated();
        $noRawat = str_replace('-', '/', $noRawat);
        $tanggalPerawatan = $data['tanggalPerawatan'];
        $jamPerawatan = $data['jamPerawatan'];

        $this->getRegistrasiPasien($noRawat, $user);

        if ($this->builderPemeriksaanIrjPasien(
                $noRawat,
                $tanggalPerawatan,
                $jamPerawatan
            )->count() == 1) throw new HttpResponseException(response([
            "error" => [
                "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                    . $tanggalPerawatan . " " . $jamPerawatan . " SUDAH DIINPUTKAN"
            ]
        ], 400));

        $pemeriksaanIrjPasien = new PemeriksaanIrjModel($this->transformRequestData($data, $user));
        $pemeriksaanIrjPasien->no_rawat = $noRawat;
        $pemeriksaanIrjPasien->save();

        return new PemeriksaanResource($pemeriksaanIrjPasien);
    }

    public function detail(string $noRawat, string $tanggalRawat, string $jamRawat): PemeriksaanResource
    {
        $user = Auth::user();
        $noRawat = str_replace('-', '/', $noRawat);

        $this->getRegistrasiPasien($noRawat, $user);

        $pemeriksaanIrjPasien = $this->builderPemeriksaanIrjPasien($noRawat, $tanggalRawat, $jamRawat)->first();
        if (!$pemeriksaanIrjPasien) throw new HttpResponseException(response([
            "error" => [
                "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                    . $tanggalRawat . " " . $jamRawat . " TIDAK DITEMUKAN"
            ]
        ], 404));

        return new PemeriksaanResource($pemeriksaanIrjPasien);
    }

    public function edit(string $noRawat, string $tanggalRawat, string $jamRawat, PemeriksaanRequest $pemeriksaanRequest): PemeriksaanResource
    {
        $user = Auth::user();
        $data = $pemeriksaanRequest->validated();
        $noRawat = str_replace('-', '/', $noRawat);
        $tanggalPerawatan = $data['tanggalPerawatan'];
        $jamPerawatan = $data['jamPerawatan'];

        $this->getRegistrasiPasien($noRawat, $user);

        $pemeriksaanIrjPasien = $this->builderPemeriksaanIrjPasien($noRawat, $tanggalRawat, $jamRawat)->first();
        if (!$pemeriksaanIrjPasien) throw new HttpResponseException(response([
            "error" => [
                "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                    . $tanggalRawat . " " . $jamRawat . " TIDAK DITEMUKAN"
            ]
        ], 404));

        if ($pemeriksaanIrjPasien['nip'] !== $user->kd_dokter) throw new HttpResponseException(response([
            "error" => [
                "pesan" => "PEMERIKSAAN PASIEN HANYA DAPAT DIEDIT OLEH PEMERIKSA"
            ]
        ], 401));

        if ($tanggalPerawatan . $jamPerawatan !== $pemeriksaanIrjPasien['tgl_perawatan'] . $pemeriksaanIrjPasien['jam_rawat']) {
            if ($this->builderPemeriksaanIrjPasien(
                    $noRawat,
                    $tanggalPerawatan,
                    $jamPerawatan
                )->count() == 1) throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                        . $tanggalPerawatan . " " . $jamPerawatan . " SUDAH DIINPUTKAN"
                ]
            ], 400));
        }

        $this->builderPemeriksaanIrjPasien($noRawat, $tanggalRawat, $jamRawat)->update($this->transformRequestData($data, $user));

        $pemeriksaanIrjPasien = $this->builderPemeriksaanIrjPasien($noRawat, $tanggalPerawatan, $jamPerawatan)->first();

        return new PemeriksaanResource($pemeriksaanIrjPasien);
    }

    public function hapus(string $noRawat, string $tanggalRawat, string $jamRawat): JsonResponse
    {
        $user = Auth::user();
        $noRawat = str_replace('-', '/', $noRawat);

        $this->getRegistrasiPasien($noRawat, $user);

        $pemeriksaanIrjPasien = $this->builderPemeriksaanIrjPasien($noRawat, $tanggalRawat, $jamRawat)->first();
        if (!$pemeriksaanIrjPasien) throw new HttpResponseException(response([
            "error" => [
                "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                    . $tanggalRawat . " " . $jamRawat . " TIDAK DITEMUKAN"
            ]
        ], 404));

        if ($pemeriksaanIrjPasien['nip'] !== $user->kd_dokter) throw new HttpResponseException(response([
            "error" => [
                "pesan" => "PEMERIKSAAN PASIEN HANYA DAPAT DIHAPUS OLEH PEMERIKSA"
            ]
        ], 401));

        $this->builderPemeriksaanIrjPasien($noRawat, $tanggalRawat, $jamRawat)->delete();

        return response()->json([
            "data" => [
                "pesan" => "PEMERIKSAAN DIHAPUS"
            ]
        ])->setStatusCode(200);
    }
}
