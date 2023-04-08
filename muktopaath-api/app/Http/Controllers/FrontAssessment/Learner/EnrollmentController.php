<?php

namespace App\Http\Controllers\FrontAssessment\Learner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment\CourseEnrollment;
use App\Models\Assessment\SyllabusStatus;
use App\Models\Assessment\Syllabus;
use App\Http\Resources\Assessment\SyllabusResource as SyllabusResources;
use App\Http\Resources\Assessment\DetailsResource;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;


class EnrollmentController extends Controller
{
        public function details($enroll_id){ 

        $res = CourseEnrollment::select('course_enrollments.id as enrollment_id','cb.details','cb.course_alias_name','cb.id as course_batch_id')
                ->join('orders as o','o.id','course_enrollments.order_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('courses as c','c.id','cb.course_id')
                ->where('course_enrollments.id',$enroll_id)
                ->first();

                $journey = [];




            return new DetailsResource($res);
    }
}
