<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Question\Question;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\SyllabusStatus;
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
        $total_quiz_data = null;
        $total_quiz_marks = null;
        if($this->quiz_data){
            $ids = json_decode($this->quiz_data);
            $ids_ordered = implode(',', $ids);
            if($ids_ordered!=''){
                $quiz = $this->quiz_data;
                $more_data_info = json_decode($this->more_data_info);
                if(isset($more_data_info->random_question) && $more_data_info->random_question==1){
                    if(isset($more_data_info->q_number) && $more_data_info->q_number>0){
                        $quiz = Question::wherein('id',$ids)->inRandomOrder()
                        ->take($more_data_info->q_number)->get();
                    }else{
                        $quiz = Question::wherein('id',$ids)->inRandomOrder()
                        ->get();
                    }
                    
                    $total_quiz_data = $more_data_info->q_number;
                    $total_quiz_marks = $more_data_info->q_number;
                }else{
                    $quiz = Question::wherein('id',$ids)->orderByRaw("FIELD(id, $ids_ordered)")->get();
                    $total_quiz_data = is_array(json_decode($this->quiz_marks))?sizeof(json_decode($this->quiz_marks)):null;
                    $total_quiz_marks = is_array(json_decode($this->quiz_marks))?array_sum(json_decode($this->quiz_marks)):null;
                }
                if($quiz){
                    $quiz_data_id = [];
                    $quiz_marks = [];
                    foreach($quiz as $qk => $qv){
                        array_push($quiz_data_id,$qv->id);
                        array_push($quiz_marks,1);
                    }
                    $this->quiz_data = json_encode($quiz_data_id);
                    if(isset($more_data_info->random_question) && $more_data_info->random_question==1){
                        $this->quiz_marks = json_encode($quiz_marks);
                    }
                    
                }
            }else{
                $more_data = json_decode($this->more_data_info);
                if(isset($more_data->ques_cat_id) && $more_data->ques_cat_id){
                    $getQList = [];
                    if($more_data->ques_limit->low>0){
                        $getRes = Question::where([['partner_category',$more_data->ques_cat_id],['dif_level',1]])->inRandomOrder()->take($more_data->ques_limit->low)->get();
                        foreach($getRes as $qk => $qv) array_push($getQList,$qv);
                    }
                    if($more_data->ques_limit->medium>0){
                        $getRes = Question::where([['partner_category',$more_data->ques_cat_id],['dif_level',2]])->inRandomOrder()->take($more_data->ques_limit->medium)->get();
                        foreach($getRes as $qk => $qv) array_push($getQList,$qv);
                    }
                    if($more_data->ques_limit->high>0){
                        $getRes = Question::where([['partner_category',$more_data->ques_cat_id],['dif_level',3]])->inRandomOrder()->take($more_data->ques_limit->high)->get();
                        foreach($getRes as $qk => $qv) array_push($getQList,$qv);
                    }

                    if(!empty($getQList)){
                        $quiz_data_id = [];
                        foreach($getQList as $qk => $qv){
                            array_push($quiz_data_id,$qv->id);
                            // $getQList[$qk]['options'] = json_decode($qv->options, true);
                            if($more_data->ques_limit->low>0 && $qv->dif_level=='1') $getQList[$qk]['mark'] = $more_data->ques_num->low;
                            elseif($more_data->ques_limit->medium>0 && $qv->dif_level=='2') $getQList[$qk]['mark'] = $more_data->ques_num->medium;
                            elseif($more_data->ques_limit->high>0 && $qv->dif_level=='3') $getQList[$qk]['mark'] = $request->ques_num->high;
                        }
                        $this->quiz_data = json_encode($quiz_data_id);
                        $quiz = $getQList; 
                    }else{
                        $quiz = ''; 
                    }
                }else{
                    $quiz = ''; 
                } 
            }
        }else{
            
            $more_data = json_decode($this->more_data_info);
            if(isset($more_data->ques_cat_id) && $more_data->ques_cat_id){
                $getQList = [];
                if($more_data->ques_limit->low>0){
                    $getRes = Question::where([['partner_category',$more_data->ques_cat_id],['dif_level',1]])->inRandomOrder()->take($more_data->ques_limit->low)->get();
                    foreach($getRes as $qk => $qv) array_push($getQList,$qv);
                }
                if($more_data->ques_limit->medium>0){
                    $getRes = Question::where([['partner_category',$more_data->ques_cat_id],['dif_level',2]])->inRandomOrder()->take($more_data->ques_limit->medium)->get();
                    foreach($getRes as $qk => $qv) array_push($getQList,$qv);
                }
                if($more_data->ques_limit->high>0){
                    $getRes = Question::where([['partner_category',$more_data->ques_cat_id],['dif_level',3]])->inRandomOrder()->take($more_data->ques_limit->high)->get();
                    foreach($getRes as $qk => $qv) array_push($getQList,$qv);
                }

                if(!empty($getQList)){
                    $quiz_data_id = [];
                    foreach($getQList as $qk => $qv){
                        array_push($quiz_data_id,$qv->id);
                        // $getQList[$qk]['options'] = json_decode($qv->options, true);
                        if($more_data->ques_limit->low>0 && $qv->dif_level=='1') $getQList[$qk]['mark'] = $more_data->ques_num->low;
                        elseif($more_data->ques_limit->medium>0 && $qv->dif_level=='2') $getQList[$qk]['mark'] = $more_data->ques_num->medium;
                        elseif($more_data->ques_limit->high>0 && $qv->dif_level=='3') $getQList[$qk]['mark'] = $request->ques_num->high;
                    }
                    $this->quiz_data = json_encode($quiz_data_id);
                    $quiz = $getQList; 
                }else{
                    $quiz = ''; 
                }
            }else{
                $quiz = ''; 
            }
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
            'instruction' => $this->instruction,
            'completeness' => $this->completeness?$this->completeness->completeness:0,
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
            'quiz_data' =>     $this->quiz_data,
            'total_quiz_data' => $total_quiz_data,
            'quiz_marks' => $this->quiz_marks,
            'total_quiz_marks' => $total_quiz_marks,
            'owner_id' => $this->owner_id,
            'created_by' => $this->created_by,
            'suggested_lesson' => $this->suggested_lesson,
            'submitinfo' => $submitinfo,
            'UserFeedback' => $this->UserFeedback?$this->UserFeedback:null,

        ];
    }
}
