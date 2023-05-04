<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $table = 'purchases';
    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'declined_at' => 'datetime'
    ];
    protected $dateFormat = 'Y-m-d\TH:i:s.u\Z';
    public function user() {
        return $this->belongsTo(AuthUser::class);
    }

    public function currency() {
        return $this->belongsTo(DictionaryCurrency::class);
    }

    public function passengers() {
        return $this->hasMany(PurchasePassenger::class);
    }

    public function files() {
        return $this->hasMany(PurchaseFile::class);
    }
}
