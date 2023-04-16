<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DictionaryAirport extends Model
{
    use HasFactory;
    protected $table = 'dictionary__airports';
    protected $guarded = [];
    public function country()
    {
        return $this->belongsTo(DictionaryCountry::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(DictionaryCity::class, 'city_id');
    }
}
