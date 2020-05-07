<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Rooms extends Model
{
    protected $fillable = [
        'description',
        'photo',
        'name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "admin_id");
    }
	
	public function members()
	{
		return $this->hasMany(Members::class, "room_id");
	}
}
