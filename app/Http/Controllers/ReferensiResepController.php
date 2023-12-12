<?php

namespace App\Http\Controllers;

use App\Http\Requests\InputParameterRequest;
use App\Http\Resources\AturanPakaiCollection;
use App\Http\Resources\BarangCollection;
use App\Http\Resources\MetodeRacikCollection;
use App\Models\AturanPakai;
use App\Models\Barang;
use App\Models\GudangBarang;
use App\Models\MetodeRacik;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReferensiResepController extends Controller
{
    public function listDataBarang(string $kodeDepo, InputParameterRequest $request): BarangCollection
    {
        $data = $request->validated();
        $halaman = $data['halaman'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $listObat = Barang::with(['jenisBarang', 'kategoriBarang', 'golonganBarang'])
            ->select(['databarang.*', 'gudangbarang.stok AS stok'])
            ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
            ->where('gudangbarang.kd_bangsal', $kodeDepo)
            ->where('databarang.status', '1');

        $pencarian = $request->input('pencarian');
        if ($pencarian) {
            $listObat = $listObat->where(function (Builder $builder) use ($pencarian) {
                $builder->orWhere('databarang.kode_brng', 'like', '%' . $pencarian . '%');
                $builder->orWhere('nama_brng', 'like', '%' . $pencarian . '%');
                $builder->orWhere('kode_sat', 'like', '%' . $pencarian . '%');
                $builder->orWhere('letak_barang', 'like', '%' . $pencarian . '%');
                $builder->orWhereHas('jenisBarang', function (Builder $builder) use ($pencarian) {
                    $builder->where('nama', 'like', '%' . $pencarian . '%');
                });
                $builder->orWhereHas('kategoriBarang', function (Builder $builder) use ($pencarian) {
                    $builder->where('nama', 'like', '%' . $pencarian . '%');
                });
                $builder->orWhereHas('golonganBarang', function (Builder $builder) use ($pencarian) {
                    $builder->where('nama', 'like', '%' . $pencarian . '%');
                });
            });
        }

        $listObat = $listObat->orderBy('nama_brng', 'ASC')
            ->paginate(perPage: $limit, page: $halaman);

        return new BarangCollection($listObat);
    }

    public function listAturanPakai(InputParameterRequest $request): AturanPakaiCollection
    {
        $request->validated();

        $pencarian = $request->input('pencarian');
        $listAturanPakai = AturanPakai::query()->when($pencarian, function ($query, $pencarian) {
            return $query->where('aturan', 'like', '%' . $pencarian . '%');
        })->get();

        return new AturanPakaiCollection($listAturanPakai);
    }

    public function listMetodeRacik(InputParameterRequest $request): MetodeRacikCollection
    {
        $request->validated();

        $pencarian = $request->input('pencarian');
        $listMetodeRacik = MetodeRacik::query()->when($pencarian, function ($query, $pencarian) {
            return $query->where('nm_racik', 'like', '%' . $pencarian . '%');
        })->get();

        return new MetodeRacikCollection($listMetodeRacik);
    }
}
