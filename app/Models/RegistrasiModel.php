<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegistrasiModel extends Model
{
    protected $table = "reg_periksa";

    public function pasien() : BelongsTo {
        return $this->belongsTo(PasienModel::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function dokter(): BelongsTo {
        return $this->belongsTo(DokterModel::class, 'kd_dokter', 'kd_dokter');
    }

    public function penjab(): BelongsTo {
        return $this->belongsTo(PenjabModel::class, 'kd_pj', 'kd_pj');
    }

    public function poli(): BelongsTo {
        return $this->belongsTo(PoliModel::class, 'kd_poli', 'kd_poli');
    }

    public function rujukanInternal(): HasMany {
        return $this->hasMany(RujukanInternalModel::class, 'no_rawat', 'no_rawat');
    }

    public function pemeriksaanIrj(): HasMany {
        return $this->hasMany(PemeriksaanIrjModel::class, 'no_rawat', 'no_rawat');
    }

}
