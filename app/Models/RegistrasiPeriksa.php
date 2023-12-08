<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegistrasiPeriksa extends Model
{
    protected $table = "reg_periksa";

    public function pasien() : BelongsTo {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function dokter(): BelongsTo {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function penjab(): BelongsTo {
        return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    }

    public function poliklinik(): BelongsTo {
        return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    }

    public function rujukanInternal(): HasMany {
        return $this->hasMany(RujukanInternal::class, 'no_rawat', 'no_rawat');
    }

    public function pemeriksaanIrj(): HasMany {
        return $this->hasMany(PemeriksaanIrj::class, 'no_rawat', 'no_rawat');
    }

}
