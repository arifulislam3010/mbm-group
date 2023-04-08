<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Question\Question;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\SyllabusStatus;
class LearnerResourcePublic extends JsonResource
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
            'id' => $this->id,
            'content_type' => $this->content_type,
            'title' => $this->title,
            'description' => $this->description,
            'instruction' => $this->instruction,
            'duration' => $this->duration,
            'forward' => $this->forward,
            'forwardable' => $this->forwardable,
            'allow_preview' => $this->allow_preview,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'files'     => $this->files,
            'file_id'     => $this->file_id,
            'current_date_time' => $this->current_date_time,
            'more_data_info' => json_decode($this->more_data_info),
            'live_class_url'      =>$this->live_class_url,
            'content_or_url' => $this->content_or_url,
            'content_url'      =>$this->content_url,

        ];
    }
}
