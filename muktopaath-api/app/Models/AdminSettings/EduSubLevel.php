<?php

namespace App\Models\AdminSettings;

use Illuminate\Database\Eloquent\Model;

class EduSubLevel extends Model
{
    public function edulevel(){
    	return $this->belongsTo('App\Models\AdminSettings\EduSubLevel','edu_sub_levels');
    }
    
}