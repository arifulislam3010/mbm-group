<?php

namespace app\Http\Resources\Assessment;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;

use App\Models\Assessment\Syllabus;
use App\Models\Myaccount\Sharing;
use App\Http\Resources\Assessment\LearnerResourceAdmin;
use App\Http\Resources\Assessment\SyllabusResourceAdmin;


class SyllabusResource extends JsonResource
{
    /** 
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $db = config()->get('database.connections.my-account.database');
        $db2 = config()->get('database.connections.classroom.database');

        $user = config()->get('global.user_id');
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'preview'             => $this->preview,
            'status'              => $this->status,
            'order_number'        => $this->order_number,
            'course_batch_id'     => $this->course_batch_id,
            'parent_id'           => $this->parent_id,
            'content'             => new LearnerResourceAdmin($this->contents),
            'learning_content_id' => $this->learning_content_id,
            'content_title'       => $this->content_title,
            'content_type'        => $this->content_type,
            'start_date'          => $this->start_date ,
            'instruction'         => $this->instruction,
            'content_url'         => $this->content_url,
            'content_or_url'      => $this->content_or_url,
            'live_class_url'      => $this->live_class_url,
            'live_class_url_type' => $this->live_class_url_type,
            'description'         => $this->description,
            'instruction'         => $this->instruction,
            'type'                =>$this->type,
            'end_date'            => $this->end_date ,
            'duration'            => $this->duration,
            'CourseContentUserFeedback' =>$this->CourseContentUserFeedback,
            'UserFeedback'              =>$this->UserFeedback,
            'is_teacher_owner'    => Syllabus::select('u.id')
                ->join('course_batches as cb','cb.id','syllabuses.course_batch_id')
                ->join($db.'.institution_infos as i','i.id','cb.owner_id')
                ->join($db.'.users as u','u.id','i.user_id')
                ->value('u.id')==config()->get('global.user_id')?1:0,
            'is_teacher_asigned'  => Sharing::select('sharings.user_id')->join($db2.'.course_batches as cb','cb.id','sharings.table_id')
            ->join($db2.'.syllabuses as s','s.course_batch_id','cb.id')
            ->where('s.id',$this->id)
            ->where('sharings.user_id',config()->get('global.user_id'))
            ->value('sharings.user_id')?1:0,
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
