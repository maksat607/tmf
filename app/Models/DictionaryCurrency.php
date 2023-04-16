<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DictionaryCurrency extends Model
{
    use HasFactory;
    protected $table = 'dictionary__currencies';
    protected $guarded = [];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'name' => 'string',
        'symbol' => 'string',
    ];

    public function purchases() {
        return $this->hasMany(Purchase::class);
    }

    public function tickets() {
        return $this->hasMany(TicketBaseTicket::class);
    }
}
