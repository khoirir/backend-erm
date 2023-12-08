<?php

namespace App\Http\Controllers;

use App\Http\Requests\InputParameterRequest;
use App\Http\Resources\PasienColletion;
use App\Http\Resources\PasienResource;
use App\Models\RegistrasiPeriksa;
use App\Models\RujukanInternal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PasienController extends Controller
{
    public function detail(string $noRawat): PasienResource
    {
        $user = Auth::user();
        $noRawat = str_replace('-', '/', $noRawat);
        $registrasiPeriksa = RegistrasiPeriksa::query()
            ->where("no_rawat", $noRawat)
            ->where("kd_dokter", $user->kd_dokter)
            ->where("stts", "!=", "Batal")
            ->first();
        if (!$registrasiPeriksa) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PASIEN DENGAN NO. RAWAT " . $noRawat . " TIDAK DITEMUKAN"
                ]
            ], 404));
        }
        return new PasienResource($registrasiPeriksa);
    }

    public function listData(InputParameterRequest $request): PasienColletion
    {
        $user = Auth::user();
        $data = $request->validated();
        $tanggalAwal = $data['tanggalAwal'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $tanggalAkhir = $data['tanggalAkhir'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $halaman = $data['halaman'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $listRegistrasiPeriksa = RegistrasiPeriksa::with(['dokter', 'pasien', 'rujukanInternal', 'poliklinik'])
            ->where('kd_dokter', $user->kd_dokter)
            ->where('tgl_registrasi', '>=', $tanggalAwal)
            ->where('tgl_registrasi', '<=', $tanggalAkhir)
            ->where('stts', '!=', 'Batal');

        $pencarian = $request->input('pencarian');
        if ($pencarian) {
            $listRegistrasiPeriksa = $this->builderPencarian($listRegistrasiPeriksa, $pencarian);
        }

        $listRegistrasiPeriksa = $listRegistrasiPeriksa->orderBy('tgl_registrasi', 'ASC')
            ->orderBy('jam_reg', 'ASC')
            ->paginate(perPage: $limit, page: $halaman);

        return new PasienColletion($listRegistrasiPeriksa);
    }

    public function detailRujukan(string $noRawat): PasienResource
    {
        $user = Auth::user();
        $noRawat = str_replace('-', '/', $noRawat);
        $rujukan = RujukanInternal::query()
            ->where('no_rawat', $noRawat)
            ->where('kd_dokter', $user->kd_dokter)
            ->first();
        if (!$rujukan) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "PASIEN RUJUKAN DENGAN NO. RAWAT " . $noRawat . " TIDAK DITEMUKAN"
                ]
            ], 404));
        }

        return new PasienResource($rujukan->registrasiPeriksa);
    }

    public function listDataRujukan(InputParameterRequest $request): PasienColletion
    {
        $user = Auth::user();
        $data = $request->validated();
        $tanggalAwal = $data['tanggalAwal'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $tanggalAkhir = $data['tanggalAkhir'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $halaman = $data['halaman'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $listDataRujukan = RegistrasiPeriksa::with(['dokter', 'pasien', 'rujukanInternal', 'poliklinik'])
            ->whereRelation('rujukanInternal', 'kd_dokter', $user->kd_dokter)
            ->where('tgl_registrasi', '>=', $tanggalAwal)
            ->where('tgl_registrasi', '<=', $tanggalAkhir)
            ->where('stts', '!=', 'Batal');

        $pencarian = $request->input('pencarian');
        if ($pencarian) {
            $listDataRujukan = $this->builderPencarian($listDataRujukan, $pencarian);
        }

        $listDataRujukan = $listDataRujukan->orderBy('tgl_registrasi', 'ASC')
            ->orderBy('jam_reg', 'ASC')
            ->paginate(perPage: $limit, page: $halaman);

        return new PasienColletion($listDataRujukan);
    }

    private function builderPencarian(Builder $pasien, string $pencarian): Builder
    {
        $pasien->where(function (Builder $builder) use ($pencarian) {
            $builder->orWhere('no_rkm_medis', 'like', '%' . $pencarian . '%');
            $builder->orWhere('no_rawat', 'like', '%' . $pencarian . '%');
            $builder->orWhereHas('pasien', function (Builder $builder) use ($pencarian) {
                $builder->where('nm_pasien', 'like', '%' . $pencarian . '%');
                $builder->orWhere('alamat', 'like', '%' . $pencarian . '%');
            });
        });

        return $pasien;
    }

}
