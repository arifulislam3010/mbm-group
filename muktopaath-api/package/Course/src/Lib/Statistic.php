<?php

namespace App\Lib;
use App\Models\Course\CourseEnrollment;
class Statistic
{
    public function EnrolleStudent($id)
    {
    	return $course = CourseEnrollment::where('course_batch_id',$id)->count();
    }
}