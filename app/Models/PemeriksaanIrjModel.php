<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanIrjModel extends Model
{
    protected $table = "pemeriksaan_ralan";
    protected $primaryKey = ['no_rawat', 'tgl_perawatan', 'jam_rawat'];
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ["no_rawat","tgl_perawatan","jam_rawat","suhu_tubuh","tensi","nadi","respirasi","tinggi","berat","spo2","gcs","kesadaran","keluhan","pemeriksaan","alergi","lingkar_perut","rtl","penilaian","instruksi","evaluasi","nip"];

    public function registrasi(): BelongsTo {
        return $this->belongsTo(RegistrasiModel::class, 'no_rawat', 'no_rawat');
    }

    public function dokter(): BelongsTo {
        return $this->belongsTo(DokterModel::class, 'nip', 'kd_dokter');
    }
}
