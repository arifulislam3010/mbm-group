<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Assessment\Syllabus;
use App\Models\Assessment\SyllabusStatus;

class CourseEnrollment extends Model
{
    protected $connection = 'assessment';
    
    protected $guarded = ['created_at','updated_at'];
    

    public function submissions(){
      return $this->hasOneThrough(Syllabus::class,SyllabusStatus::class);
    }
}
