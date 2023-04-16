<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DictionaryAirline extends Model
{
    use HasFactory;
    protected $table = 'dictionary__airlines';
    protected $guarded = [];
    public function airport()
    {
        return $this->hasMany(DictionaryAirport::class, 'airline_id');
    }
}
