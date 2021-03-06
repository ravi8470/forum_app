<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function threads(){
        return $this->hasMany('App\Thread');
    }

    public function replies(){
        return $this->hasMany('App\Reply');
    }

    public function sentMessages(){
        return $this->hasMany('App\Message', 'from_id');
    }

    public function receivedMessages(){
        return $this->hasMany('App\Message', 'to_id');
    }

    public function allMessages(){
        return $this->sentMessages->merge($this->receivedMessages);
    }
}
