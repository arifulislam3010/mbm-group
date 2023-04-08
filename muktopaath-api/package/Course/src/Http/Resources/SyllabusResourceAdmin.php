<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;

use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\SyllabusStatus;
use Muktopaath\Course\Models\Course\Completeness;
use Muktopaath\Course\Http\Resources\SyllabusResourceChildAdmin;
use Muktopaath\Course\Http\Resources\LearnerResourceAdmin;

class SyllabusResourceAdmin extends JsonResource
{
    /** 
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
    	$user = config()->get('global.user_id');
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'preview'             => $this->preview,
            'status'              => $this->status,
            // 'completeness'        => $this->Completeness?$this->Completeness->completeness:0,
            'order_number'        => $this->order_number,
            'course_batch_id'     => $this->course_batch_id,
            'parent_id'           => $this->parent_id,
            'content'             => '',
            'learning_content_id' => $this->learning_content_id,
            'content_title'       => $this->content_title,
            'content_type'        => $this->content_type,
            'start_date'          => $this->start_date ,
            'instruction'         => $this->instruction,
            'content_url'         => $this->content_url,
            'content_or_url'         => $this->content_or_url,
            'live_class_url'      => $this->live_class_url,
            'live_class_url_type' => $this->live_class_url_type,
            'description'         => $this->description,
            'instruction'         => $this->instruction,
            'type'                =>$this->type,
            'end_date'            => $this->end_date ,
            'duration'            => $this->duration,
            'suggested_lesson'    => $this->suggested_lesson,
            // 'CourseContentUserFeedback' =>$this->CourseContentUserFeedback,
            // 'UserFeedback'              =>$this->UserFeedback,
            'name'                => Str::random(10),
            // 'sub_id'              => Syllabus::where('parent_id',$this->id)->pluck('id')->toArray(),
             'children'            => $this->lessons?SyllabusResourceChildAdmin::collection($this->lessons):$this->lessons,
        ];
    }
}
