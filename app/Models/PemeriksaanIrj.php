<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanIrj extends Model
{
    /**
     * @var array|mixed|string|string[]
     */
    protected $table = "pemeriksaan_ralan";
    protected $primaryKey = ['no_rawat', 'tgl_perawatan', 'jam_rawat'];
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ["no_rawat","tgl_perawatan","jam_rawat","suhu_tubuh","tensi","nadi","respirasi","tinggi","berat","spo2","gcs","kesadaran","keluhan","pemeriksaan","alergi","lingkar_perut","rtl","penilaian","instruksi","evaluasi","nip"];

    public function registrasiPeriksa(): BelongsTo {
        return $this->belongsTo(RegistrasiPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function dokter(): BelongsTo {
        return $this->belongsTo(Dokter::class, 'nip', 'kd_dokter');
    }

    protected function getKeyForSaveQuery()
    {

        $primaryKeyForSaveQuery = array(count($this->primaryKey));

        foreach ($this->primaryKey as $i => $pKey) {
            $primaryKeyForSaveQuery[$i] = $this->original[$this->getKeyName()[$i]] ?? $this->getAttribute($this->getKeyName()[$i]);
        }

        return $primaryKeyForSaveQuery;

    }

    protected function setKeysForSaveQuery($query): Builder
    {

        foreach ($this->primaryKey as $i => $pKey) {
            $query->where($this->getKeyName()[$i], '=', $this->getKeyForSaveQuery()[$i]);
        }

        return $query;
    }
}
