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
class BatchBasicPublic extends JsonResource
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
        
        return [
            'id'                        => $this->id,
            'title'                     => $this->title,
            'bn_title'                     => $this->bn_title,
            'uuid'                      => $this->uuid,
            'slug'                      => $this->slug,
            'course_alias_name'         => $this->course_alias_name,
            'payment_point_amount'      => $this->payment_point_amount,
            'discount'                  =>json_decode($this->discount),
            'discount_status'           =>$this->discount_status,
            'payment_status'            =>$this->payment_status,
            'courses_for_status'        =>$this->courses_for_status,
            'course_alias_name'         => $this->course_alias_name,
            'course_alias_name_en'      => $this->course_alias_name_en,
            'totalEnroll'               => $this->enroll,
            'averageRating'             => $averageRating,
            'totalRatingCount'          => $this->rating_count,
            'thumbnail'                  => $this->course->thumbnail,
            'course_duration'           =>$this->course->course_duration,
            'admission_status'          =>$this->admission_status,
        ];
    }
}
