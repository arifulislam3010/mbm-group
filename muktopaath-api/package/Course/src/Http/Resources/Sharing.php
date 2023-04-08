<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Models\Course\Course;

class Sharing extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = Course::where('id',$this->table_id)->first();

        return [

            'id' => $data->id,
            'title' => $data->title,
            'course_code' => $data->course_code,
            'course_duration' => $data->course_duration,
            'owner_id' => $data->owner_id,
            'activity' => $this->activity,
        ];
    }
}
