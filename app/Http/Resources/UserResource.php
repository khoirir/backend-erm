<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $expiredToken = date("Y-m-d H:i:s", strtotime($this->expired_at));
        return [
            "kdDokter" => $this->kd_dokter,
            "namaDokter" => $this->dokter->nm_dokter,
            "foto" => $this->dokter->pegawai->photo,
            "token" => $this->id,
            "expiredToken" => $expiredToken
        ];
    }
}
