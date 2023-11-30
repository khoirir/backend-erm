<?php

namespace App\Http\Controllers;

use App\Http\Requests\InputParameterRequest;
use App\Http\Resources\PasienColletion;
use App\Http\Resources\PasienResource;
use App\Models\RegistrasiModel;
use App\Models\RujukanInternalModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PasienController extends Controller
{
    public function detail(string $noRawat): PasienResource
    {
        $user = Auth::user();
        $noRawat = str_replace('-', '/', $noRawat);
        $pasien = RegistrasiModel::query()
            ->where("no_rawat", $noRawat)
            ->where("kd_dokter", $user->kd_dokter)
            ->where("stts", "!=", "Batal")
            ->first();
        if (!$pasien) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "DATA PASIEN TIDAK DITEMUKAN"
                ]
            ], 404));
        }
        return new PasienResource($pasien);
    }

    public function listData(InputParameterRequest $request): PasienColletion
    {
        $user = Auth::user();
        $data = $request->validated();
        $tanggalAwal = $data['tanggalAwal'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $tanggalAkhir = $data['tanggalAkhir'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $halaman = $data['halaman'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $pasien = RegistrasiModel::with(['dokter','pasien','rujukanInternal','poli'])
            ->where('kd_dokter', $user->kd_dokter)
            ->where('tgl_registrasi', '>=', $tanggalAwal)
            ->where('tgl_registrasi', '<=', $tanggalAkhir)
            ->where('stts', '!=', 'Batal');

        $pencarian = $request->input('pencarian');
        if ($pencarian) {
            $pasien = $this->builderPencarian($pasien, $pencarian);
        }

        $pasien = $pasien->paginate(perPage: $limit, page: $halaman);

        return new PasienColletion($pasien);
    }

    public function detailRujukan(string $noRawat): PasienResource
    {
        $user = Auth::user();
        $noRawat = str_replace('-', '/', $noRawat);
        $rujukan = RujukanInternalModel::query()
            ->where('no_rawat', $noRawat)
            ->where('kd_dokter', $user->kd_dokter)
            ->first();

        if (!$rujukan) {
            throw new HttpResponseException(response([
                "error" => [
                    "pesan" => "DATA PASIEN RUJUKAN TIDAK DITEMUKAN"
                ]
            ], 404));
        }

        return new PasienResource($rujukan->registrasi);
    }

    public function listDataRujukan(InputParameterRequest $request): PasienColletion
    {
        $user = Auth::user();
        $data = $request->validated();
        $tanggalAwal = $data['tanggalAwal'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $tanggalAkhir = $data['tanggalAkhir'] ?? date('Y-m-d', strtotime(Carbon::now()));
        $halaman = $data['halaman'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $pasien = RegistrasiModel::with(['dokter','pasien','rujukanInternal','poli'])
            ->whereHas('rujukanInternal', function (Builder $builder) use ($user) {
                $builder->where('kd_dokter', $user->kd_dokter);
            })
            ->where('tgl_registrasi', '>=', $tanggalAwal)
            ->where('tgl_registrasi', '<=', $tanggalAkhir)
            ->where('stts', '!=', 'Batal');

        $pencarian = $request->input('pencarian');
        if ($pencarian) {
            $pasien = $this->builderPencarian($pasien, $pencarian);
        }

        $pasien = $pasien->paginate(perPage: $limit, page: $halaman);

        return new PasienColletion($pasien);
    }

    private function builderPencarian(Builder $pasien, string $pencarian):Builder {
        $pasien->where(function (Builder $builder) use ($pencarian) {
            $builder->orWhere('no_rkm_medis', 'like', '%' . $pencarian . '%');
            $builder->orWhere('no_rawat', 'like', '%' . $pencarian . '%');
            $builder->orWhereHas('pasien', function (Builder $builder) use ($pencarian) {
                $builder->where('nm_pasien', 'like', '%' . $pencarian . '%');
                $builder->orWhere('alamat', 'like', '%' . $pencarian . '%');
            });
        })->orderBy('tgl_registrasi')->orderBy('jam_reg');

        return $pasien;
    }

}
