<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GudangBarang extends Model
{
    protected $table = 'gudangbarang';

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_brng', 'kode_brng')
            ->where('status', '1');
    }
}
