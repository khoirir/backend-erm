<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\CollectionFind;

class PasienResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $umur = $this->hitungUmur($this->pasien->tgl_lahir);
        return [
            "noRawat" => $this->no_rawat,
            "noRegistrasi" => $this->no_reg,
            "tanggalRegistrasi" => $this->tgl_registrasi,
            "jamRegistrasi" => $this->jam_reg,
            "poliAsal" => $this->when(count($this->rujukanInternal->where('kd_dokter', Auth::user()->kd_dokter)) > 0, function () {
                return $this->poli->nm_poli;
            }),
            "dokterAsal" => $this->when(count($this->rujukanInternal->where('kd_dokter', Auth::user()->kd_dokter)) > 0, function () {
                return $this->dokter->nm_dokter;
            }),
            "noRM" => $this->no_rkm_medis,
            "namaPasien" => $this->pasien->nm_pasien,
            "alamat" => $this->pasien->alamat,
            "tanggalLahir" => $this->pasien->tgl_lahir,
            "umur" => $umur,
            "jenisKelamin" => $this->pasien->jk,
            "jenisBayar" => $this->penjab->png_jawab,
            "noTelepon" => $this->pasien->no_tlp,
            "statusPasien" => $this->stts,
            "statusPoli" => $this->status_poli
        ];
    }

    private function hitungUmur(string $tglLahir): string
    {
        $tanggalLahir = Carbon::parse($tglLahir);
        $umur = $tanggalLahir->diff(Carbon::now());

        return $umur->y . ' Tahun ' . $umur->m . ' Bulan ' . $umur->d . ' Hari';
    }
}
