<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRatingUpdate extends Model
{
    use HasFactory;
    protected $table = 'auth__user_rating_updates';

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    protected $dateFormat = 'Y-m-d\TH:i:s.u\Z';
    protected $fillable = [
        'destination_id',
        'author_id',
        'created_at',
        'type',
    ];

//    public function destination()
//    {
//        return $this->belongsTo(User::class, 'destination_id');
//    }

    public function author()
    {
        return $this->belongsTo(AuthUser::class, 'author_id','id');
    }
}
