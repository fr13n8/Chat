<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\User;
use App\Rooms;
use App\Messages;

class Members extends Model
{
	protected $fillable = [
        'room_id',
        'user_id',
    ];
	
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Rooms::class);
    }

    public function messages()
    {
        return $this->hasMany(Messages::class, "member_id");
    }
}