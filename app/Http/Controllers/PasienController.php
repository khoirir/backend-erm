<?php

namespace App\Http\Controllers;

use App\Http\Requests\InputParameterRequest;
use App\Http\Resources\PasienColletion;
use App\Http\Resources\PasienResource;
use App\Models\RegistrasiModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PasienController extends Controller
{
    public function get(string $noRawat): PasienResource
    {
        $user = Auth::user();
        $pasien = RegistrasiModel::query()
            ->with(['pasien', 'penjab'])
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

        $dataResult = $this->toArrayData($pasien);
        return new PasienResource($dataResult);
    }

    public function dataPasien(InputParameterRequest $request): PasienColletion
    {
        $user = Auth::user();
        $request->validated();
        $tanggalAwal = $request->input('tanggalAwal', date('Y-m-d', strtotime(Carbon::now())));
        $tanggalAkhir = $request->input('tanggalAkhir', date('Y-m-d', strtotime(Carbon::now())));
        $halaman = $request->input('halaman', 1);
        $limit = $request->input('limit', 10);

        $pasien = RegistrasiModel::query()
            ->with(['pasien', 'penjab'])
            ->where("kd_dokter", $user->kd_dokter)
            ->where("tgl_registrasi", ">=", $tanggalAwal)
            ->where("tgl_registrasi", "<=", $tanggalAkhir)
            ->where("stts", "!=", "Batal");

        $pasien = $pasien->where(function (Builder $builder) use ($request) {
            $pencarian = $request->input('pencarian');
            if ($pencarian) {
                $builder->where(function (Builder $builder) use ($pencarian) {
                    $builder->orWhere('no_rkm_medis', 'like', '%' . $pencarian . '%');
                    $builder->orWhere('no_rawat', 'like', '%' . $pencarian . '%');
                    $builder->orWhereHas('pasien', function (Builder $builder) use ($pencarian) {
                        $builder->where('nm_pasien', 'like', '%' . $pencarian . '%');
                        $builder->orWhere('alamat', 'like', '%' . $pencarian . '%');
                    });
                });
            }
        })->orderBy('tgl_registrasi')
        ->orderBy('jam_reg');

        $pasien = $pasien->paginate(perPage: $limit, page: $halaman);

        $pasien->getCollection()->transform(function ($data) {
            return $this->toArrayData($data);
        });

        return new PasienColletion($pasien);
    }

    public function toArrayData($data): array
    {
        $tanggalLahir = Carbon::parse($data->pasien->tgl_lahir);
        $umur = $tanggalLahir->diff(Carbon::now());
        return [
            "noRawat" => $data->no_rawat,
            "noRegistrasi" => $data->no_reg,
            "tanggalRegistrasi" => $data->tgl_registrasi,
            "jamRegistrasi" => $data->jam_reg,
            "noRM" => $data->no_rkm_medis,
            "namaPasien" => $data->pasien->nm_pasien,
            "alamat" => $data->pasien->alamat,
            "tanggalLahir" => $data->pasien->tgl_lahir,
            "umur" => $umur->y . ' Tahun ' . $umur->m . ' Bulan ' . $umur->d . ' Hari',
            "jenisKelamin" => $data->pasien->jk,
            "jenisBayar" => $data->penjab->png_jawab,
            "noTelepon" => $data->pasien->no_tlp,
            "statusPasien" => $data->stts,
            "statusPoli" => $data->status_poli
        ];
    }
}
