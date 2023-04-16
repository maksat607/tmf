<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketBaseTicket extends Model
{
    use HasFactory;

    protected $table = 'ticket__base_tickets';
    protected $guarded = [];

    public function airline()
    {
        return $this->belongsTo(DictionaryAirline::class, 'airline_code', 'code');
    }

    public function departureAirport()
    {
        return $this->belongsTo(DictionaryAirport::class, 'departure_airport_code', 'code');
    }

    public function arrivalAirport()
    {
        return $this->belongsTo(DictionaryAirport::class, 'arrival_airport_code', 'code');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(AuthUser::class, 'user_id', 'id');
    }

    public function currency(){
        return $this->hasOne(DictionaryCurrency::class,'currency_id','id');
    }
    public function ticketAirplaneTicket()
    {
        return $this->belongsTo(TicketAirplaneTicket::class, 'id', 'id');
    }
}