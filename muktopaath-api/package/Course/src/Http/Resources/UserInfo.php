<?php

namespace Muktopaath\Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInfo extends JsonResource
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
            'id'                    =>$this->id,
            'photo_name'            =>$this->photo_name,
            'cover_image'           =>$this->cover_image,
            'father_name'           =>$this->father_name,
            'mother_name'           =>$this->mother_name,
            'spouse_name'           =>$this->spouse_name,
            'gender'                =>$this->gender,
            'social'                =>json_decode($this->social),
            'area_of_experiences'   =>json_decode($this->area_of_experiences),
            'employeements_history' =>$this->employeements_history,
            'profession_area'       =>$this->profession_area,
            'institution'           =>$this->institution,
            'edu_institution'       =>$this->edu_institution,
            'designation'           =>$this->designation,
            'education_status'      =>$this->education_status,
            'blood_group'           =>$this->blood_group,
            'contact_number'        =>$this->contact_number,
            'about'                 =>$this->about,
            'dob'                   =>$this->dob,
            'nid'                   =>$this->nid,
            'address'               =>$this->address,
            'education_level_id'    =>$this->education_level_id,
            'degree_id'             =>$this->degree_id,
            'sub_districts'         =>$this->sub_districts,
            'profession'            =>$this->profession,
            'profession_area'       =>$this->profession_area,
            'user_id'               =>$this->user_id,
            'division_id'           =>$this->division_id,
            'district_id'           =>$this->district_id,
            'sub_district_id'       =>$this->sub_district_id,
        ];
    }
}
