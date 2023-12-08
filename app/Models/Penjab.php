<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjab extends Model
{
    protected $table = "penjab";

    public function registrasiPeriksa() : HasMany {
        return $this->hasMany(RegistrasiPeriksa::class, "kd_pj", "kd_pj");
    }
}
