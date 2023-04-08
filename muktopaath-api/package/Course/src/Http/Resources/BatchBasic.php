<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Http\Resources\UserbasicInfo as UserbasicInfoResources;
use Muktopaath\Course\Http\Resources\Course as CourseResources;
use Muktopaath\Course\Http\Resources\InstitutionsBasic as InstitutionsBasicResources;
use Muktopaath\Course\Http\Resources\CourseBatchAssign as CourseBatchAssignResources;
use Muktopaath\Course\Models\Course\Syllabus;
use DB;
use Auth;
use Muktopaath\Course\Models\Course\Wishlist;
class BatchBasic extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        if($this->rating_count==0) $averageRating = 0;
        else $averageRating = $this->rating_sum/$this->rating_count; 


        $wishlist_status = 0;
        
        
        return [
            'wishlist_status'           =>$wishlist_status,
            'id'                        => $this->id,
            'title'                     => $this->title,
            'bn_title'                     => $this->bn_title,
            'uuid'                      => $this->uuid,
            'slug'                      => $this->slug,
            'course_alias_name'         => $this->course_alias_name,
            'payment_point_amount'      => $this->payment_point_amount,
            'study_mode'                => $this->study_mode,
            'enrolment_approval_status' =>$this->enrolment_approval_status,
            'discount'                  =>json_decode($this->discount),
            'discount_status'           =>$this->discount_status,
            'payment_status'            =>$this->payment_status,
            'courses_for_status'        =>$this->courses_for_status,
            'certificate_approval_status' =>$this->certificate_approval_status,
            'courseCategory'            => $this->course->courseCategory,
            'code'                      => $this->code,
            'course_alias_name'         => $this->course_alias_name,
            'course_alias_name_en'         => $this->course_alias_name_en,
            'totalEnroll'               => $this->enroll,
            'averageRating'             => $averageRating,
            'totalRatingCount'          => $this->rating_count,
            'owner'                     => new InstitutionsBasicResources($this->owner),
            'thumbnail'                  => $this->course->thumbnail,
            'course_duration'           =>$this->course->course_duration,
            'admission_status'          =>$this->admission_status,
            'reg_start_date'            =>$this->reg_start_date,
            'reg_end_date'              =>$this->reg_end_date,
            'start_date'                =>$this->start_date,
            'end_date'                  =>$this->end_date,
            'slug'                      => $this->course->languageId?$this->course->languageId->prefix:'',
            // 'courseStatistics'          => $summary
        ];
    }
}
