<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DokterModel extends Model
{
    protected $table = "dokter";
    protected $primaryKey = "kd_dokter";

    public $incrementing = false;

    public function user(): HasMany {
        return $this->hasMany(UserModel::class, "kd_dokter","kd_dokter");
    }

    public function pegawai(): BelongsTo {
        return $this->belongsTo(PegawaiModel::class, "kd_dokter", "nik");
    }

    public function registrasi(): HasMany {
        return $this->hasMany(RegistrasiModel::class, 'kd_dokter', 'kd_dokter');
    }

    public function pemeriksaanIrj(): HasMany {
        return $this->hasMany(PemeriksaanIrjModel::class, 'nip', 'kd_dokter');
    }
}
