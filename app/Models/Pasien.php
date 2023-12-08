<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    protected $table = "pasien";

    public function registrasiPeriksa(): HasMany {
        return $this->hasMany(RegistrasiPeriksa::class, "no_rkm_medis", "no_rkm_medis");
    }
}
