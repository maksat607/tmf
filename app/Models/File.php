<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $table = 'files';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(AuthUser::class);
    }

    public function purchaseFiles()
    {
        return $this->hasMany(PurchaseFile::class);
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_files');
    }

    public function messageFiles()
    {
        return $this->hasMany(ChatMessageFile::class);
    }

    public function chatMessages()
    {
        return $this->belongsToMany(ChatMessage::class, 'chat__message_files');
    }
}
