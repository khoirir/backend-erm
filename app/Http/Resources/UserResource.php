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
        return [
            "kdDokter" => $this->resource['kdDokter'],
            "namaDokter" => $this->resource['namaDokter'],
            "foto" => $this->resource['foto'],
            "token" => $this->resource['token'],
            "expiredToken" => $this->resource['expiredToken']
        ];
    }
}
