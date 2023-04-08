<?php

namespace app\Http\Resources\Assessment;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;

use App\Models\Assessment\Syllabus;
// use App\Http\Resources\Assessment\SyllabusResource as SyllabusChildResource;
use App\Http\Resources\Assessment\LearnerResourceAdmin as LearnerResourceAdmin;

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
            'order_number'        => $this->order_number,
            'course_batch_id'     => $this->course_batch_id,
            'creator'               =>$this->creator,
            'participations_count' => $this->participations_count,
            'total_enrollment' => $this->total_enrollment,
            'parent_id'           => $this->parent_id,
            'content'             => new LearnerResourceAdmin($this->contents),
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
            'CourseContentUserFeedback' =>$this->CourseContentUserFeedback,
            'UserFeedback'              =>$this->UserFeedback,
            'name'                => Str::random(10),
            'sub_id'              => Syllabus::where('parent_id',$this->id)->pluck('id')->toArray(),
            'children'            => SyllabusResourceAdmin::collection(Syllabus::where('parent_id',$this->id)->orderBy('order_number','ASC')->get())
        ];

        // return [
        //     'id'                  => $this->id,
        //     'title'               => $this->title,
        //     'status'              => $this->status,
        //     'order_number'        => $this->order_number,
        //     'course_batch_id'     => $this->course_batch_id,
        //     'parent_id'           => $this->parent_id ,
        //     'learning_content_id' => $this->learning_content_id,
        //     'content_title'       => $this->content_title,
        //     'content_type'        => $this->content_type,
        //     'start_date'          => $this->start_date ,
        //     'live_class_url'      => $this->live_class_url,
        //     'live_class_url_type'      => $this->live_class_url_type,
        //     'end_date'            => $this->end_date,
        //     'batch'               => $this->batch,
        //     'duration'            => $this->duration,
        //     'name'                => Str::random(10),
        //     'sub_id'              => Syllabus::where('parent_id',$this->id)->pluck('id')->toArray(),
        //     'children'            => SyllabusResource::collection(Syllabus::where('parent_id',$this->id)->get())
        // ];
    }
}
