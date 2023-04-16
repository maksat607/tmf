<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthAccessToken extends Model
{
    use HasFactory;
    protected $table = 'auth__access_tokens';
    protected $guarded = [];
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(AuthUser::class);
    }
}
