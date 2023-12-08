<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pegawai extends Model
{
    protected $table = "pegawai";

    public function dokter(): HasOne {
        return $this->hasOne(Dokter::class, "kd_dokter", "nik");
    }
}
