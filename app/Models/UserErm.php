<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserErm extends Model implements Authenticatable
{
    use HasUuids;

    protected $table = "user_erm";
    protected $primaryKey = "id";
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = ['id','kd_dokter','expired_at'];

    public function dokter(): BelongsTo {
        return $this->belongsTo(Dokter::class,"kd_dokter", "kd_dokter");
    }

    public function getAuthIdentifierName()
    {
        return 'kd_dokter';
    }

    public function getAuthIdentifier()
    {
        return $this->kd_dokter;
    }

    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
    }

    public function getRememberToken()
    {
        return $this->id;
    }

    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
    }

    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
    }
}
