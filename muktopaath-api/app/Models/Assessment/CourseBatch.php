<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseBatch extends Model
{
    use SoftDeletes;

    protected $connection = 'assessment';
    
    public function course(){
    	return $this->belongsTo('App\Models\Assessment\Course','course_id');
    }

    public function sessions(){
        return $this->hasMany('App\Models\Assessment\Syllabus')->where('parent_id',null);
    }

    public function reviewals(){
        return $this->hasManyThrough('App\Models\Assessment\SyllabusStatus','App\Models\Assessment\Syllabus')->where('syllabus_statuses.mark',null);

    }
}
