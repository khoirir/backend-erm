<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PemeriksaanCollection extends ResourceCollection
{
    private array $meta;

    public function __construct($resource)
    {
        $this->meta = [
            'limit' => $resource->perPage(),
            'halaman' => $resource->currentPage(),
            'total' => $resource->total()
        ];

        $resource = $resource->getCollection();

        parent::__construct($resource);
    }
    public function toArray(Request $request): array
    {
        return [
            "data" => PemeriksaanResource::collection($this->collection),
            "meta" => $this->meta
        ];
    }
}
