<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;

use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\SyllabusStatus;
use Muktopaath\Course\Models\Course\Completeness;
// use App\Http\Resources\Assessment\SyllabusResource as SyllabusChildResource;
use Muktopaath\Course\Http\Resources\LearnerResourcePublic;

class SyllabusResourcePublic extends JsonResource
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
        $datetime = gmdate("Y-m-d H:i:s",strtotime('+6 Hours'));
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'preview'             => $this->preview,
            'status'              => $this->status,
            'order_number'        => $this->order_number,
            'course_batch_id'     => $this->course_batch_id,
            'parent_id'           => $this->parent_id,
            // 'content'             => $this->preview==1?new LearnerResourcePublic($this->contents):'',
            'content'             => new LearnerResourcePublic($this->contents),
            'learning_content_id' => $this->learning_content_id,
            'content_title'       => $this->content_title,
            'content_type'        => $this->content_type,
            'content_url'         => $this->content_url,
            'content_or_url'         => $this->content_or_url,
            'start_date'          => $this->start_date ,
            'live_class_url'      => $this->live_class_url,
            'live_class_url_type' => $this->live_class_url_type,
            'description'         => $this->description,
            'type'                =>$this->type,
            'end_date'            => $this->end_date,
            'duration'            => $this->duration,
            // 'duration'            => $this->contents?$this->contents->duration:null,
            'current_date_time'   => $datetime,
        ];
    }
}
