<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatChat extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'chat__chats';
    protected $casts = [
        'created_at' => 'datetime',
        'last_update_at' => 'datetime'
    ];
    protected $dateFormat = 'c';
    public $timestamps = false;
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function users()
    {
        return $this->belongsToMany(AuthUser::class, 'chat_user', 'chat_id', 'user_id')->withPivot('is_admin');
    }
    public function replyUser(){
        return $this->belongsTo(AuthUser::class,'reply_user_id');
    }
    public function ticketUser(){
        return $this->belongsTo(AuthUser::class,'ticket_user_id');
    }
    public function ticket(){
        return $this->belongsTo(TicketBaseTicket::class,'ticket_id');
    }

}
