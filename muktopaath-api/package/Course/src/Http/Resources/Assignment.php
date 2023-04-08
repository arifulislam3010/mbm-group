<?php

namespace Muktopaath\Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Assignment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        //return parent::toArray($request);
        return [
            'id'                    =>$this->id,
            'assignment_info'       =>$this->assignment_info,
            'enrollment_id'         =>$this->enrollment_id,
            'file_name'             =>$this->file_name,
            'unit_id'               =>$this->unit_id,
            'lesson_id'             =>$this->lesson_id,
            'out_of_marks'          =>$this->out_of_marks,
            'obtain_marks'          =>$this->obtain_marks,
            'peer_review_info'      =>$this->PeerReview,
            'created_at'            =>$this->created_at->diffForHumans(),
            'updated_at'            =>$this->updated_at->diffForHumans(),
            'attempt'               =>$this->attempt,
        ];
    }
}