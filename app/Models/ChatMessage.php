<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'chat__messages';
    /**
     * Get the chat that this message belongs to.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the sender of this message.
     */
    public function sender()
    {
        return $this->belongsTo(AuthUser::class, 'sender_id');
    }

    /**
     * Get the files attached to this message.
     */
    public function files()
    {
        return $this->hasMany(ChatMessageFile::class);
    }
}
