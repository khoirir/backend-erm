<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kodeObat' => $this->kode_brng,
            'namaObat' =>  $this->nama_brng,
            'satuan' => $this->kode_sat,
            'komposisi' => $this->letak_barang,
            'hargaSatuan' => $this->h_beli,
            'jenisObat' => $this->jenisBarang->nama,
            'kategoriObat' => $this->kategoriBarang->nama,
            'golonganObat' => $this->golonganBarang->nama,
            'jumlahStok' => $this->stok
        ];
    }
}
