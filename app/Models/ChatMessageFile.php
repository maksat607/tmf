<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageFile extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'chat__message_files';
    public function chatMessage()
    {
        return $this->belongsTo(ChatMessage::class);
    }
}
