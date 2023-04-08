<?php

namespace App\Http\Resources\Assessment;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Question\Question;
use App\Models\Assessment\SyllabusStatus;
use App\Http\Resources\ContentBank\Questions as QuestionsResource;

class LearnerResource extends JsonResource
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
                $more_data_info = json_decode($this->more_data_info);
                if(isset($more_data_info->random_question) && $more_data_info->random_question==1){
                    $quiz = Question::wherein('id',$ids)
                        ->orderByRaw("FIELD(id, $ids_ordered)")->inRandomOrder()
                        ->get();
                }else{
                    $quiz = Question::wherein('id',$ids)
                        ->orderByRaw("FIELD(id, $ids_ordered)")
                        ->get();
                }
                
            }else{
                $quiz = ''; 
            }
            
        }else{
            $quiz = '';
        }
        $user = config()->get('global.user_id');
        if($user && isset($this->syllabus_id)){
            $submitinfo= SyllabusStatus::where('syllabus_id',$this->syllabus_id)->join('course_enrollments','course_enrollments.id','syllabus_statuses.course_enrollment_id')->join('orders','orders.id','course_enrollments.order_id')->where('orders.user_id',$user)->first();  
        }else{
            $submitinfo='';
        }
        if($this->duration!=null){
            $duration = $this->duration;
            $srchStr = ['HH','mm','ss'];
            $duration = str_replace($srchStr,'00',$duration);
        }else{
            $duration = null;
        }
        
        return [
            'id' => $this->id,
            'content_type' => $this->content_type,
            'title' => $this->title,
            'description' => $this->description,
            'duration' => $duration,
            'forward' => $this->forward,
            'forwardable' => $this->forwardable,
            'allow_preview' => $this->allow_preview,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'files'     => $this->files,
            'file_id'     => $this->file_id,
            'current_date_time' => $this->current_date_time,
            'more_data_info' => json_decode($this->more_data_info),
            'quiz' => $quiz,
            'live_class_url'      =>$this->live_class_url,
            'content_or_url' => $this->content_or_url,
            'content_url'      =>$this->content_url,
            'quiz_data' => $this->quiz_data,
            'total_quiz_data' => is_array(json_decode($this->quiz_marks))?sizeof(json_decode($this->quiz_marks)):null,
            'quiz_marks' => $this->quiz_marks,
            'total_quiz_marks' => is_array(json_decode($this->quiz_marks))?array_sum(json_decode($this->quiz_marks)):null,
            'owner_id' => $this->owner_id,
            'created_by' => $this->created_by,
            'submitinfo' => $submitinfo,
            'host' => isset($this->host)?$this->host:null

        ];
    }
}
