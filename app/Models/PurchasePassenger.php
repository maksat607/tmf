<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePassenger extends Model
{
    use HasFactory;
    protected $table = 'purchase_passengers';
    protected $guarded = [];
    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function passenger() {
        return $this->belongsTo(Passenger::class);
    }
}
