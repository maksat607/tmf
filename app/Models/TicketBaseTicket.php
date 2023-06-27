<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketBaseTicket extends Model
{
    use HasFactory;

    protected $table = 'ticket__base_tickets';
    protected $guarded = [];
    public $timestamps = false;
    protected $casts = [
        'created_at' => 'datetime',
        'expired_at' => 'datetime',
        'top_position_expired_at' => 'datetime',
    ];
    protected $dateFormat = 'c';


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
    public function chats()
    {
        return $this->hasMany(ChatChat::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(AuthUser::class, 'user_id', 'id');
    }

    public function currency(){
        return $this->hasOne(DictionaryCurrency::class,'id','currency_id');
    }
    public function ticketAirplaneTicket()
    {
        return $this->hasOne(TicketAirplaneTicket::class, 'id', 'id');
    }
    public function favorite(){
        return $this->hasOne(TicketsFavoriteTicket::class,'ticket_id','id');
    }


}
