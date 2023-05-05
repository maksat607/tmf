<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthUser extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasFactory;
    protected $table = 'auth__users';

    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime',
        'payment_intent_created_at' => 'datetime',
        'email_verification_requested_at' => 'datetime'
    ];
    protected $dateFormat = 'c';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function personalAccessTokens()
    {
        return $this->hasMany(PersonalAccessToken::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function tickets()
    {
        return $this->hasMany(TicketBaseTicket::class,'user_id','id');
    }
    public function accessTokens()
    {
        return $this->hasMany(AuthAccessToken::class, 'user_id', 'id');
    }

    public function photo()
    {
        return $this->belongsTo(File::class, 'photo_id', 'id');
    }

    public function favoriteTickets()
    {
        return $this->hasMany(TicketsFavoriteTicket::class, 'user_id', 'id');
    }

    public function ticketsMatchAlertRule()
    {
        return $this->hasMany(TicketsMatchAlertRule::class, 'user_id', 'id');
    }

}
