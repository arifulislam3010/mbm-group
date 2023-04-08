<?php

namespace App\Models\AdminSettings;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminSettings\EduSubLevel;

class EducationLevel extends Model
{
    protected $guarded = ['created_at','updated_at'];



    public function degrees(){
    	return $this->hasMany(Degree::class);
    }

    public function edusublevels(){
    	return $this->hasMany(EduSubLevel::class, 'edu_level_id');
    }
}