<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'databarang';

    public function golonganBarang(): BelongsTo
    {
        return $this->belongsTo(GolonganBarang::class,'kode_golongan', 'kode');
    }

    public function kategoriBarang(): BelongsTo
    {
        return $this->belongsTo(KategoriBarang::class,'kode_kategori', 'kode');
    }

    public function jenisBarang(): BelongsTo
    {
        return $this->belongsTo(JenisBarang::class,'kdjns', 'kdjns');
    }

    public function gudangBarang(): HasMany
    {
        return $this->hasMany(GudangBarang::class, 'kode_brng', 'kode_brng');
    }
}
