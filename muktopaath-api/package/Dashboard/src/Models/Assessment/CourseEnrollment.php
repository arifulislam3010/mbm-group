<?php

namespace Muktopaath\Dashboard\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Muktopaath\Dashboard\Models\Assessment\Syllabus;
use Muktopaath\Dashboard\Models\Assessment\SyllabusStatus;

class CourseEnrollment extends Model
{
    protected $connection = 'assessment';
    
    protected $guarded = ['created_at','updated_at'];
    

    public function submissions(){
      return $this->hasOneThrough(Syllabus::class,SyllabusStatus::class);
    }
}
