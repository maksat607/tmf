<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAirplaneTicket extends Model
{
    use HasFactory;
    protected $table = 'ticket__airplane_tickets';
    protected $guarded = [];
    protected $dates = [
        'start_date_at',
        'end_date_at',
        'return_start_date_at',
        'return_end_date_at',
    ];
    public $timestamps = false;

    public function airline()
    {
        return $this->belongsTo(DictionaryAirline::class, 'airline_id', 'id');
    }
    public function fromAirport()
    {
        return $this->belongsTo(DictionaryAirport::class, 'from_airport_id');
    }

    public function toAirport()
    {
        return $this->belongsTo(DictionaryAirport::class, 'to_airport_id');
    }
    public function returnFromAirport()
    {
        return $this->belongsTo(DictionaryAirport::class, 'return_from_airport_id');
    }

    public function returnToAirport()
    {
        return $this->belongsTo(DictionaryAirport::class, 'return_to_airport_id');
    }
    public function purchases(){
        return $this->belongsTo(TicketBaseTicket::class,'ticket_id','id');
    }

    public function ticketBaseTicket(){
        return $this->belongsTo(TicketBaseTicket::class,'id','id');
    }

}
