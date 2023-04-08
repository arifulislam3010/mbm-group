<?php

namespace App\Http\Resources\Assessment;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Assessment\SyllabusResource as SyllabusResources;
use App\Models\Assessment\Syllabus;
use App\Models\Assessment\SyllabusStatus;

class DetailsResource extends JsonResource
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
            'enrollment_id' => $this->enrollment_id,
            'course_batch_id' => $this->course_batch_id,
            'course_alias_name' => $this->course_alias_name,
            'details' => $this->details,
            'status' => SyllabusStatus::where('course_enrollment_id',$this->enrollment_id)->pluck('syllabus_id')->toArray(),
            'syllabus' => SyllabusResources::collection(Syllabus::where('parent_id',null)->where('course_batch_id',$this->course_batch_id)->get()),
        ];
    }
}
