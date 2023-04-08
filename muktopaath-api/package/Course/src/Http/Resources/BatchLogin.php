<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Http\Resources\UserbasicInfo as UserbasicInfoResources;
use Muktopaath\Course\Http\Resources\Course as CourseResources;
use Muktopaath\Course\Http\Resources\InstitutionsBasic as InstitutionsBasicResources;
// use Muktopaath\Course\Http\Resources\CourseBatchAssign as CourseBatchAssignResources;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Http\Resources\SyllabusResourceUnit;
use Auth;
use Muktopaath\Course\Models\Course\Wishlist;
class BatchLogin extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    //Start Get Syllabus
  

    //End Get Syllabus  

    public function toArray($request)
    {
        
        
        if($this->rating_count==0) $averageRating = 0;
        else $averageRating = $this->rating_sum/$this->rating_count; 
        
        
        
        return [
            'id'                        =>$this->id,
            'title'                     =>$this->title,
            'bn_title'                  =>$this->bn_title,
            'uuid'                      => $this->uuid,
            'slug'                      => $this->slug,
            'study_mode'                => $this->study_mode,
            'syllabus'                  =>SyllabusResourceUnit::collection($this->sessions),
            'details'                   =>html_entity_decode($this->details),
            'objective'                 =>html_entity_decode($this->objective),
            'course_motto'              =>html_entity_decode($this->course_motto),
            'course_alias_name'         =>$this->course_alias_name,
            'course_alias_name_en'         =>$this->course_alias_name_en,
            'requirement'               =>html_entity_decode($this->requirement),
            'course_requirment'         =>json_decode($this->course_requirment),
            'marks'                     =>json_decode($this->marks),
            'featured'                  =>$this->featured,
            'status'                    =>$this->status,
            'clone_status'              =>$this->clone_status,
            'payment_status'            =>$this->payment_status,
            'enrolment_approval_status' =>$this->enrolment_approval_status,
            'payment_point_status'      =>$this->payment_point_status,
            'payment_point_amount'      =>$this->payment_point_amount,
            'discount'                  =>json_decode($this->discount),
            'discount_status'            =>$this->discount_status,
            'code'                      =>$this->code,
            'duration'                  =>$this->duration,
            'start_date'                =>$this->start_date,
            'end_date'                  =>$this->end_date,
            'reg_start_date'            =>$this->reg_start_date,
            'reg_end_date'              =>$this->reg_end_date,
            'admission_status'          =>$this->admission_status,
            'limit'                     =>$this->limit,
            'migration_allowe'          =>$this->migration_allowe,
            'migration_fee'             =>$this->migration_fee,
            'mig_pa_status'             =>$this->mig_pa_status,
            'courses_for_status'        =>$this->courses_for_status,
            'mig_payment_amount'        => $this->mig_payment_amount,
            'certificate_alias_name'    => $this->certificate_alias_name,
            'certificate_approval_status' => $this->certificate_approval_status,
            'course_alias_name'         => $this->course_alias_name,
            // 'totalEnroll'               => $totalEnroll,
            'totalEnroll'               => $this->enroll,
            'averageRating'             => $averageRating,
            'totalRatingCount'          => $this->rating_count,
            // 'totalRatingCount'          => $this->totalRatingCount,
            // 'ratings'                    => $this->Rating,
            
            'published_status'          =>$this->published_status,
            'Comments'                  => $this->Rating,
            'owner'                     => new InstitutionsBasicResources($this->owner),
            // 'CreatedBy'                 => new UserbasicInfoResources($this->CreatedBy),
            // 'UpdatedBy'                 => new UserbasicInfoResources($this->UpdatedBy),
            // 'Coordinator'               => CourseBatchAssignResources::collection($this->Coordinator),
            // 'Facilitator'               => CourseBatchAssignResources::collection($this->Facilitator),
            // 'Moderator'               => CourseBatchAssignResources::collection($this->Moderator),
            'course'                    => new CourseResources($this->course),
            'created_at'                =>$this->created_at,
            'updated_at'                =>$this->updated_at,
            'enrollement_validation_status' => $this->enrollement_validation_status,
            'enrollement_validation_details' => json_decode($this->enrollement_validation_details, true)
        ];
    }
}
