<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Members;
use App\MessageImgs;

class Messages extends Model
{
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
    *
    * @var array
    */
    protected $fillable = [
        'message',
        'user_id',
        'room_id'
    ];

    public function member()
    {
        return $this->belongsTo(Members::class);
    }
	
	public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function photos()
    {
        return $this->hasMany(MessageImgs::class, 'message_id');
    }
}
