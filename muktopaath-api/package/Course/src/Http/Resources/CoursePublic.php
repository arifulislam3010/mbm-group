<?php

namespace Muktopaath\Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Http\Resources\BatchBasicPublic as BatchBasicResourcePublic;

class CoursePublic extends JsonResource
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
            'title'                 =>$this->title,
            'bn_title'              =>$this->bn_title,
            'batchs'                => BatchBasicResourcePublic::collection($this->batchInfo),
            'course_level'          =>$this->course_level,
            'cat_id'                =>$this->cat_id,
            'language_id'           =>$this->language_id,
            'description'           => $this->description,
            'objective'             => $this->objective,
            'coursetags'            => $this->coursetags,
            'course_code'           =>$this->course_code,
            'learning_outcomes'     => json_decode($this->learning_outcomes),
            'requirement'           => json_decode($this->requirement),
            'course_motto'          => $this->course_motto,
            'course_category'       =>$this->courseCategory,
            'thumbnail'             =>$this->thumbnail,
            'promovideo'            =>$this->promovideo,
            'language'              =>$this->languageId,
            // 'tags'                  =>$this->Tags,
        ];
    }
}
