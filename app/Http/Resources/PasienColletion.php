<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PasienColletion extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
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
            "data" => PasienResource::collection($this->collection),
            "meta" => $this->meta
        ];
    }
}
