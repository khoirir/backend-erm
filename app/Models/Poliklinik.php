<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poliklinik extends Model
{
    protected $table = "poliklinik";

    public function registrasiPeriksa() : HasMany {
        return $this->hasMany(RegistrasiPeriksa::class, "kd_poli", "kd_poli");
    }
}
