<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseFile extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'purchase_files';
    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function file() {
        return $this->belongsTo(File::class);
    }
}
