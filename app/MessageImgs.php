<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Messages;

class MessageImgs extends Model
{
    protected $fillable = [
        'message_id',
        'path',
    ];
    
    public function message()
    {
        return $this->belongsTo(Messages::class, 'message_id');
    }
}
