<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoliModel extends Model
{
    protected $table = "poliklinik";

    public function registrasi() : HasMany {
        return $this->hasMany(RegistrasiModel::class, "kd_poli", "kd_poli");
    }
}
