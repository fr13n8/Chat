<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Messages;
use App\Rooms;
use App\Members;

class User extends Authenticatable implements JWTSubject
{

    use Notifiable;

    protected $table = "users";
    protected $fillable = [
        "avatar_id", 
        "name"
    ]; 

    protected $hidden = [
        'password', 
        'email_verified_at', 
        'remember_token', 
        'created_at', 
        'updated_at', 
        'verified'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
    */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
    *
    * @return array
    */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function messages()
    {
        /* return $this->hasManyThrough(Messages::class ,Members::class, 
            'user_id', // Foreign key on cars table...
            'member_id', // Foreign key on owners table...
            'id', // Local key on mechanics table...
            'id' // Local key on cars table...
        ); */
        
        return $this->hasMany(Messages::class);
    }

    public function rooms()
    {
        return $this->hasMany(Rooms::class, "admin_id");
    }
	
	public function members()
	{
		return $this->hasMany(Members::class);
	}
}
