<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBarang extends Model
{
    protected $table = 'jenis';

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'kdjns', 'kdjns');
    }
}
