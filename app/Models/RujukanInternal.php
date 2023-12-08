<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RujukanInternal extends Model
{
    protected $table = "rujukan_internal_poli";

    public function registrasiPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegistrasiPeriksa::class, "no_rawat", "no_rawat")
            ->where("stts", "!=", "Batal");
    }
}
