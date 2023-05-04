<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;
    protected $table = 'passengers';
    protected $guarded = [];
    protected $dates = [
        'deleted_at'
    ];
    protected $dateFormat = 'Y-m-d\TH:i:s.u\Z';
    public function purchases() {
        return $this->hasMany(PurchasePassenger::class);
    }
}
