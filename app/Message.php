<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    public $timestamps = false;

    public function fromUser(){
        return $this->belongsTo('App\User','from_id');
    }

    public function toUser(){
        return $this->belongsTo('App\User', 'to_id');
    }
}
