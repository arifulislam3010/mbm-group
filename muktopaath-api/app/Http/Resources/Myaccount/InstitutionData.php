<?php

namespace App\Http\Resources\Myaccount;

use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use DB;


class InstitutionData extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'institution_name' => $this->institution_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'instituion_type_id' => $this->instituion_type_id,
            'username' => $this->username,
            'address' => $this->address,
            'google_location' => $this->google_location,
            'contact_person' => $this->contact_person,
            'contact_person_email' => $this->contact_person_email,
            'imei_no' => $this->imei_no,
            'logo' => $this->logo,
            'intital' => $this->initial,
            'total_course'  => $this->total_course_count,
            'total_students'    => CourseEnrollment::select(DB::raw('COUNT(course_enrollments.id) as total'))
                ->join('course_batches','course_batches.id','course_enrollments.course_batch_id')
                ->join('orders','orders.id','course_enrollments.order_id')
                ->where('course_batches.owner_id',$this->id)
                ->groupBy('orders.user_id')
                ->value('total')
        ];
    }
}
