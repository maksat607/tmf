<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketsFavoriteTicket extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'tickets__favorite_tickets';
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(TicketBaseTicket::class);
    }
}
