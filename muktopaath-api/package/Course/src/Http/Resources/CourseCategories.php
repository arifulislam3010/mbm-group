<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CourseCategories as ResourceCourseCategories;

class CourseCategories extends JsonResource
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
            'id'              =>$this->id,
            'title'           =>$this->title,
            'bn_title'        =>$this->bn_title,
            'image'           =>$this->image,
            'created_by'      =>$this->CreatedBy,
            'updated_by'      =>$this->UpdatedBy,
            'course_count'    =>count($this->courseCount),
            'childs'          =>ResourceCourseCategories::collection($this->Childs),
        ];
    }
}
