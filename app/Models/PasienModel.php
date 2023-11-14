<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PasienModel extends Model
{
    protected $table = "pasien";

    public function registrasi(): HasMany {
        return $this->hasMany(RegistrasiModel::class, "no_rkm_medis", "no_rkm_medis");
    }
}
