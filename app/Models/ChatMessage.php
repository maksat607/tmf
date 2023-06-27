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
    protected $casts = [
        'created_at' => 'datetime'
    ];
    protected $dateFormat = 'c';
    public $timestamps = false;
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the sender of this message.
     */
    public function user()
    {
        return $this->belongsTo(AuthUser::class, 'user_id');
    }

    /**
     * Get the files attached to this message.
     */
    public function files()
    {
        return $this->hasMany(ChatMessageFile::class,'message_id');
    }
}
