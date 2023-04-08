<?php

namespace Muktopaath\Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseRating extends JsonResource
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
            'id'                        =>$this->id,
            'rating_point'              =>$this->rating_point,
            'feedback_comments'         =>$this->feedback_comments,
            'created_at'                =>$this->created_at->diffForHumans(),
            'updated_at'                =>$this->updated_at->diffForHumans(),
        ];
    }
}
