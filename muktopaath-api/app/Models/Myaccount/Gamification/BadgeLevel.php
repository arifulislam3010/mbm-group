<?php

namespace App\Models\Myaccount\Gamification;

use Illuminate\Database\Eloquent\Model;

class BadgeLevel extends Model
{
    protected $connection = 'my-account';
    public function badge(){
    	return $this->belongsTo('App\Models\Myaccount\Gamification\Badge','badge_id');
    }
}
