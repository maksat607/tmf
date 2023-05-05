<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketsMatchAlertRule extends Model
{
    use HasFactory;
    protected $table = 'tickets__match_alert_rules';
    protected $guarded = [];
    public $timestamps = false;
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expired_at' => 'datetime',
        'start_date_at' => 'datetime',
        'end_date_at' => 'datetime',
        'return_start_date_at' => 'datetime',
        'return_end_date_at' => 'datetime',
    ];
    protected $dateFormat = 'c';

    public function user()
    {
        return $this->belongsTo(AuthUser::class,'user_id','id');
    }

    public function fromAirport()
    {
        return $this->belongsTo(DictionaryAirport::class, 'from_airport_id');
    }

    public function toAirport()
    {
        return $this->belongsTo(DictionaryAirport::class, 'to_airport_id');
    }

    public function setFromAirportAttribute($value)
    {
        $this->attributes['from_airport_id'] = $value;
    }


///////// cast CamelCase to Snake/////////
    public function setToAirportAttribute($value)
    {
        $this->attributes['to_airport_id'] = $value;
    }
    public function setAdultsCountAttribute($value)
    {
        $this->attributes['adults_count'] = $value;
    }
    public function setChildrenCountAttribute($value)
    {
        $this->attributes['children_count'] = $value;
    }
    public function setInfantsCountAttribute($value)
    {
        $this->attributes['infants_count'] = $value;
    }
    public function setIsOneWayAttribute($value)
    {
        $this->attributes['is_one_way'] = $value;
    }
    public function setClassTypeAttribute($value)
    {
        $this->attributes['class_type'] = $value;
    }
    /////////////////////////////////////////////////////////////////////
}
