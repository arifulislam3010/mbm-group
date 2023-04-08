<?php

namespace App\Http\Resources\ContentBank;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Question\Question;

class ContentDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       $quiz = Question::wherein('id',json_decode($this->quiz_data))->get();
        
        return [
            'id' => $this->id,
            'quiz' => $quiz,
            'content_type' => $this->content_type,
            'title' => $this->title,
            'description' => $this->description,
            'duration' => $this->duration,
            'forward' => $this->forward,
            'forwardable' => $this->forwardable,
            'allow_preview' => $this->allow_preview,
            'more_data_info' => json_decode($this->more_data_info),
            'quiz' => $quiz,
            'quiz_data' => $this->quiz_data,
            'total_quiz_data' => !$this->quiz_marks?null:sizeof(json_decode($this->quiz_marks)),
            'quiz_marks' => $this->quiz_marks,
            'total_quiz_marks' => !$this->quiz_marks?null:array_sum(json_decode($this->quiz_marks)),
            'owner_id' => $this->owner_id,
            'created_by' => $this->created_by,

        ];
    }
}
