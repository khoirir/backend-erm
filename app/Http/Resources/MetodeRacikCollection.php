<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MetodeRacikCollection extends ResourceCollection
{

    public function toArray(Request $request): array
    {
        return [
            "data" => MetodeRacikResource::collection($this->collection)
        ];
    }
}
