<?php

namespace Muktopaath\Dashboard\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $connection = 'assessment';


    protected $guarded = ['created_at','updated_at'];

    public function category(){
    	return $this->belongsTo('App\Models\Assessment\CourseCategory','cat_id');
    }
    public function batch(){
    	return $this->hasMany('App\Models\Assessment\CourseBatch','course_id')->withCount('sessions','reviewals');
    }
}
