<?php

namespace App\Http\Controllers;

use App\Http\Requests\InputParameterRequest;
use App\Http\Requests\PemeriksaanRequest;
use App\Http\Resources\PemeriksaanCollection;
use App\Http\Resources\PemeriksaanResource;
use App\Models\PemeriksaanIrj;
use App\Models\RegistrasiPeriksa;
use App\Models\RujukanInternal;
use App\Models\UserErm;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PemeriksaanController extends Controller
{
    private function getRegistrasiPasien(string $noRawat, UserErm $userModel): void
    {
        $registrasiPasien = RegistrasiPeriksa::query()
            ->where("no_rawat", $noRawat)
            ->where("kd_dokter", $userModel->kd_dokter)
            ->where("stts", "!=", "Batal")
            ->first();
        if (!$registrasiPasien) {
            $rujukanPasien = RujukanInternal::query()
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

    private function builderPemeriksaanIrjPasien(string $noRawat, string $tanggalRawat, string $jamRawat): Builder
    {
        return PemeriksaanIrj::query()
            ->where('no_rawat', $noRawat)
            ->where('tgl_perawatan', $tanggalRawat)
            ->where('jam_rawat', $jamRawat);
    }

    private function getPemeriksaanIrjPasien(string $noRawat, string $tanggalRawat, string $jamRawat): PemeriksaanIrj
    {
        $pemeriksaanIrjPasien = PemeriksaanIrj::where('no_rawat', $noRawat)
            ->where('tgl_perawatan', $tanggalRawat)
            ->where('jam_rawat', $jamRawat)
            ->first();

        if (!$pemeriksaanIrjPasien) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                        . $tanggalRawat . " " . $jamRawat . " TIDAK DITEMUKAN"
                ]
            ], 404));
        }

        return $pemeriksaanIrjPasien;
    }

    private function transformRequestData(array $data): array
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
            "evaluasi" => $data['evaluasi']
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

        if ($this->builderPemeriksaanIrjPasien($noRawat, $tanggalPerawatan, $jamPerawatan)->count() == 1) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                        . $tanggalPerawatan . " " . $jamPerawatan . " SUDAH DIINPUTKAN"
                ]
            ], 400));
        }

        $pemeriksaanIrjPasien = new PemeriksaanIrj($this->transformRequestData($data));
        $pemeriksaanIrjPasien->no_rawat = $noRawat;
        $pemeriksaanIrjPasien->nip = $user->kd_dokter;
        $pemeriksaanIrjPasien->save();

        return new PemeriksaanResource($pemeriksaanIrjPasien);
    }

    public function detail(string $noRawat, string $tanggalRawat, string $jamRawat): PemeriksaanResource
    {
        $user = Auth::user();
        $noRawat = str_replace('-', '/', $noRawat);

        $this->getRegistrasiPasien($noRawat, $user);

        $pemeriksaanIrjPasien = $this->getPemeriksaanIrjPasien($noRawat, $tanggalRawat, $jamRawat);

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

        $pemeriksaanIrjPasien = $this->getPemeriksaanIrjPasien($noRawat, $tanggalRawat, $jamRawat);

        if ($pemeriksaanIrjPasien['nip'] !== $user->kd_dokter) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PEMERIKSAAN PASIEN HANYA DAPAT DIEDIT OLEH PEMERIKSA"
                ]
            ], 401));
        }

        if ($tanggalPerawatan . $jamPerawatan !== $pemeriksaanIrjPasien['tgl_perawatan'] . $pemeriksaanIrjPasien['jam_rawat']
            && $this->builderPemeriksaanIrjPasien($noRawat, $tanggalPerawatan, $jamPerawatan)->count() == 1) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT " . $noRawat . " DAN WAKTU PERAWATAN "
                        . $tanggalPerawatan . " " . $jamPerawatan . " SUDAH DIINPUTKAN"
                ]
            ], 400));
        }

        $pemeriksaanIrjPasien->fill($this->transformRequestData($data));
        $pemeriksaanIrjPasien->save();

        return new PemeriksaanResource($pemeriksaanIrjPasien);
    }

    public function hapus(string $noRawat, string $tanggalRawat, string $jamRawat): JsonResponse
    {
        $user = Auth::user();
        $noRawat = str_replace('-', '/', $noRawat);

        $this->getRegistrasiPasien($noRawat, $user);

        $pemeriksaanIrjPasien = $this->getPemeriksaanIrjPasien($noRawat, $tanggalRawat, $jamRawat);

        if ($pemeriksaanIrjPasien['nip'] !== $user->kd_dokter) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PEMERIKSAAN PASIEN HANYA DAPAT DIHAPUS OLEH PEMERIKSA"
                ]
            ], 401));
        }

        $pemeriksaanIrjPasien->delete();

        return response()->json([
            "data" => [
                "pesan" => "PEMERIKSAAN DIHAPUS"
            ]
        ])->setStatusCode(200);
    }

    public function listData(string $noRM, InputParameterRequest $request): PemeriksaanCollection
    {
        $data = $request->validated();
        $tanggalAwal = $data['tanggalAwal'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $tanggalAkhir = $data['tanggalAkhir'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $halaman = $data['halaman'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $pemeriksaanIrjPasien = PemeriksaanIrj::with(['registrasiPeriksa', 'dokter', 'dokter.pegawai'])
            ->whereRelation('registrasiPeriksa', 'no_rkm_medis', $noRM)
            ->where('tgl_perawatan', '>=', $tanggalAwal)
            ->where('tgl_perawatan', '<=', $tanggalAkhir);

        $pencarian = $request->input('pencarian');
        if ($pencarian) {
            $pemeriksaanIrjPasien = $pemeriksaanIrjPasien->where(function (Builder $builder) use ($pencarian) {
                $builder->orWhere('no_rawat', 'like', '%' . $pencarian . '%');
                $builder->orWhere('keluhan', 'like', '%' . $pencarian . '%');
                $builder->orWhere('pemeriksaan', 'like', '%' . $pencarian . '%');
                $builder->orWhere('penilaian', 'like', '%' . $pencarian . '%');
                $builder->orWhere('instruksi', 'like', '%' . $pencarian . '%');
                $builder->orWhere('evaluasi', 'like', '%' . $pencarian . '%');
                $builder->orWhere('kesadaran', 'like', '%' . $pencarian . '%');
                $builder->orWhere('alergi', 'like', '%' . $pencarian . '%');
                $builder->orWhere('rtl', 'like', '%' . $pencarian . '%');
                $builder->orWhereHas('dokter', function (Builder $builder) use ($pencarian) {
                    $builder->where('nm_dokter', 'like', '%' . $pencarian . '%');
                });
            });
        }

        $pemeriksaanIrjPasien = $pemeriksaanIrjPasien->orderBy('tgl_perawatan', 'ASC')
            ->orderBy('jam_rawat', 'ASC')
            ->paginate(perPage: $limit, page: $halaman);

        return new PemeriksaanCollection($pemeriksaanIrjPasien);
    }
}
