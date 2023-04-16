<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'personal_access_tokens';
    public function user() {
        return $this->belongsTo(AuthUser::class);
    }
}
