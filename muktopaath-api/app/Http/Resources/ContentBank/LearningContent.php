<?php

namespace App\Http\Resources\ContentBank;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Question\Question;
use App\Http\Resources\ContentBank\Questions as QuestionsResource;
class LearningContent extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        if($this->quiz_data){
            if($this->quiz_data!='null' ){
                $ids = json_decode($this->quiz_data);
                $ids_ordered = implode(',', $ids);
                $questions = QuestionsResource::collection(Question::wherein('id',$ids)
                        ->orderByRaw("FIELD(id, $ids_ordered)")
                        ->get());
            }else{
                $questions = []; 
            }
            
        }else{
            $questions = [];
        }
            
            
        
        return [
            'id' => $this->id,
            'content_type' => $this->content_type,
            'title' => $this->title,
            'cat_id' => $this->cat_id,
            'level' => $this->level,
            'language_id' => $this->language_id,
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
            'question_setup' => $this->question_setup,
            'quiz' => $this->quiz,
            'folders'=>$this->folders,
            'questions' => $questions,
            'live_class_url'      =>$this->live_class_url,
            'content_url'         => $this->content_url,
            'content_or_url'      => $this->content_or_url,
            'quiz_data' => json_decode($this->quiz_data),
            'quiz_marks' => json_decode($this->quiz_marks),
            'folder_id' => $this->folder_id,
            'folder_marks' => $this->folder_marks,
            'total_quiz_marks' => is_array(json_decode($this->quiz_marks))?array_sum(json_decode($this->quiz_marks)):null,
            'owner_id' => $this->owner_id,
            'created_by' => $this->created_by

        ];
    }
}
