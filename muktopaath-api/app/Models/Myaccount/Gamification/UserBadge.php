<?php

namespace App\Models\Myaccount\Gamification;

use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    protected $connection = 'my-account';
    protected $table = "user_badges";

    public function badge(){
    	return $this->belongsTo('App\Models\Myaccount\Gamification\Badge','badge_id');
    }
    public function BadgeLevel(){
    	return $this->belongsTo('App\Models\Myaccount\Gamification\BadgeLevel','badge_level_id')->with('badge');
    }
    public function User()
    {
        return $this->belongsTo('App\Myaccount\User','user_id')->with('UserInfo');
    }
}
