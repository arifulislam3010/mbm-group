<?php

namespace Muktopaath\Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Http\Resources\UserbasicInfo as UserbasicInfoResources;
use Muktopaath\Course\Models\Course\Course;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Models\Course\CourseBatch;
class InstitutionsBasic extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $totalCourse = Course::where('owner_id',$this->id)->count();
        $totalCourseEnrollment = CourseEnrollment::join('course_batches','course_batches.id','course_enrollments.course_batch_id')->where('course_batches.owner_id',$this->id)->count();
        $totalBatch= CourseBatch::where('course_batches.owner_id',$this->id)->count();
        $initial = json_decode($this->initial);
        if(isset($initial->about)){
            $about = $initial->about;
        }else{
            $about = '';
        }
      
        return [
            
            'id'                        => $this->id,
            'institution_name'          => $this->institution_name,
            'institution_name_bn'       => $this->institution_name_bn,
            'institution_name_bn'       => $this->institution_name_bn,
            'initial'                   => json_decode($this->initial),
            'username'                  => $this->username,
            'logo'                      => $this->logo,
            'totalCourse'               => $totalCourse,
            'totalBatch'                => $totalBatch,
            'totalCourseEnrollment'     => $totalCourseEnrollment,
            'created_at'                =>$this->created_at->diffForHumans(),
            'updated_at'                =>$this->updated_at->diffForHumans(),
        ];
    }
}
