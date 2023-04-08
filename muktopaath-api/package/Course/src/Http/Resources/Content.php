<?php

namespace Muktopaath\Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Models\Question\Question;
use Muktopaath\Course\Http\Resources\Question as QuestionResource;
class Content extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
            if($this->quiz_data!=null){
                $ids = json_decode($this->quiz_data);
                $ids_ordered = implode(',', $ids);
                
                $quiz = QuestionResource::collection(Question::wherein('id',$ids)
                        ->orderByRaw("FIELD(id, $ids_ordered)")
                        ->get());
            }else{
                $quiz = '';
            }
           $timeD = json_decode($this->contentData->more_data_info);
           if(isset($timeD->time)){
               $duration = gmdate('H:i',$timeD->time);
           }else{
               $duration = '';
           }
           
           $nameD = json_decode($this->description);
           if(isset($nameD->name)){
               $name = $nameD->name;
           }else{
               $name = '';
           }
        
        return [
            'id' => $this->id,
            'content_type' => $this->contentData->content_type,
            'title' => $this->title,
            'description' => $name,
            'duration' => $duration,
            'forward' => $this->forward,
            'forwardable' => $this->forwardable,
            'allow_preview' => $this->allow_preview,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'current_date_time' => $this->current_date_time,
            'more_data_info' => json_decode($this->contentData->more_data_info),
            'quiz' => $quiz,
            'live_class_url'      =>$this->live_class_url,
            'quiz_data' => $this->quiz_data,
            'total_quiz_data' => is_array(json_decode($this->quiz_marks))?sizeof(json_decode($this->quiz_marks)):null,
            'quiz_marks' => $this->quiz_marks,
            'total_quiz_marks' => is_array(json_decode($this->quiz_marks))?array_sum(json_decode($this->quiz_marks)):null,
            'owner_id' => $this->owner_id,
            'created_by' => $this->created_by,

        ];
         /*return [
            'id'                        =>$this->id,            
            'created_at'                =>$this->created_at->diffForHumans(),
            'updated_at'                =>$this->updated_at->diffForHumans(),
        ];*/
       /* return [
            'id'                    =>$this->id,
            'enrolled_id'           =>$this->enrollement_id,
            'unit_id'               =>$this->unit_id,
            'lesson_id'             =>$this->lesson_id,
            'out_of_marks'          =>$this->out_of_marks,
            'obtain_marks'          =>$this->obtain_marks,
            'submitted_ans'         =>json_decode($this->submitted_ans),
            'created_at'            =>$this->created_at->diffForHumans(),
            'updated_at'            =>$this->updated_at->diffForHumans(),
        ];*/
        return parent::toArray($request);
    }
}
