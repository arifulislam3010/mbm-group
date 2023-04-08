<?php

namespace App\Models\Myaccount\Gamification;

use Illuminate\Database\Eloquent\Model;

class Gamification extends Model
{
    protected $connection = 'my-account';
    public function User()
    {
        return $this->belongsTo('App\Myaccount\User','user_id');
    }
    public function UserInfo()
    {
        return $this->belongsTo('App\Myaccount\User','user_id')->with('UserInfo');
    }
    // public function UserInfo()
    // {
    //     return $this->hasManyThrough('App\Models\User\UserInfo','App\User','user_id','id','id','plot_id');
    // }
}
