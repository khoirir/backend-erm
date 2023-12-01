<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PemeriksaanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "noRawat" => $this->no_rawat,
            "tanggalPerawatan" => $this->tgl_perawatan,
            "jamPerawatan" => $this->jam_rawat,
            "idPemeriksa" => $this->nip,
            "namaPemeriksa" => $this->dokter->nm_dokter,
            "jabatan" => $this->dokter->pegawai->jbtn,
            "keluhan" => $this->keluhan,
            "pemeriksaan" => $this->pemeriksaan,
            "penilaian" => $this->penilaian,
            "suhuTubuh" => (int)$this->suhu_tubuh,
            "beratBadan" => (int)$this->berat,
            "tinggiBadan" => (int)$this->tinggi,
            "tensi" => $this->tensi,
            "nadi" => (int)$this->nadi,
            "respirasi" => (int)$this->respirasi,
            "instruksi" => $this->instruksi,
            "evaluasi" => $this->evaluasi,
            "kesadaran" => $this->kesadaran,
            "alergi" => $this->alergi,
            "spo2" => (int)$this->spo2,
            "gcs" => $this->gcs,
            "tindakLanjut" => $this->rtl,
            "lingkarPerut" => (int)$this->lingkar_perut
        ];
    }
}
