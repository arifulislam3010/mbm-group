<?php

namespace App\Models\Myaccount\Gamification;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = "badges";
    protected $connection = 'my-account';
    public function BadgeLevels(){
    	return $this->hasMany('App\Models\Myaccount\Gamification\BadgeLevel' , 'badge_id' , 'id');
    }
}
