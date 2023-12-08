<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GolonganBarang extends Model
{
    protected $table = 'golongan_barang';

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'kode_golongan', 'kode');
    }
}
