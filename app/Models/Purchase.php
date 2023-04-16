<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $table = 'purchases';
    protected $guarded = [];
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
