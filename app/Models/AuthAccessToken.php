<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthAccessToken extends Model
{
    use HasFactory;
    protected $table = 'auth__access_tokens';
    protected $guarded = [];
    protected $casts = [
        'expired_at' => 'datetime',
    ];
    protected $dateFormat = 'c';

    public function user()
    {
        return $this->belongsTo(AuthUser::class);
    }
}
