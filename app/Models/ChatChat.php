<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatChat extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'chat__chats';
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function users()
    {
        return $this->belongsToMany(AuthUser::class, 'chat_user', 'chat_id', 'user_id')->withPivot('is_admin');
    }

}
