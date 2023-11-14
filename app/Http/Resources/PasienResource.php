<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasienResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "noRawat" => $this->resource['noRawat'],
            "noRegistrasi" => $this->resource['noRegistrasi'],
            "tanggalRegistrasi" => $this->resource['tanggalRegistrasi'],
            "jamRegistrasi" => $this->resource['jamRegistrasi'],
            "noRM" => $this->resource['noRM'],
            "namaPasien" => $this->resource['namaPasien'],
            "alamat" => $this->resource['alamat'],
            "tanggalLahir" => $this->resource['tanggalLahir'],
            "umur" => $this->resource['umur'],
            "jenisKelamin" => $this->resource['jenisKelamin'],
            "jenisBayar" => $this->resource['jenisBayar'],
            "noTelpon" => $this->resource['noTelpon'],
            "statusPasien" => $this->resource['statusPasien'],
            "statusPoli" => $this->resource['statusPoli']
        ];
    }
}
