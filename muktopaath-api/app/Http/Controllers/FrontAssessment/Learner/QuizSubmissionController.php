<?php

namespace App\Http\Controllers\FrontAssessment\Learner;

use App\Http\Controllers\Controller;
use App\Models\ContentBank\LearningContent;
use App\Models\Question\Question;
use App\Models\Assessment\Syllabus;
use App\Models\Assessment\SyllabusStatus;
use App\Models\Assessment\CourseEnrollment;
use App\Http\Resources\Assessment\LearnerResource;
use Illuminate\Http\Request;

class QuizSubmissionController extends Controller
{
    public function store(Request $request){


          $data = $request->all();

          $res = Syllabus::where('id',$request->syllabus_id)->first();
            
          if($res){
            $content = LearningContent::where('id',$res->learning_content_id)->first();
            $carry = json_decode($content->quiz_marks);

            $ids = json_decode($content->quiz_data);
            if($ids!=''){
                 $ids_ordered = implode(',', $ids);
            
                $quiz = Question::wherein('id',$ids)
                        ->orderByRaw("FIELD(id, $ids_ordered)")
                        ->get();
                $answer = [];
                $ids = [];
    
               
                foreach ($quiz as $key => $value) {
    
                    array_push($ids,$value->id);
                    $answer[$value->id] = json_decode($value->answer);
    
                    //array_push($answer, json_decode($value->answer));
                }
            }else{
                $quiz='';
                $answer = [];
                $ids = [];
            }
            $count = 0;
            $mark = 0;

            $batch_id = Syllabus::where('id',$request->syllabus_id)->value('course_batch_id');

            $Syllabus = Syllabus::where('id',$request->syllabus_id)->with('contents')->first();

            $enroll_id = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                    ->where('o.user_id',config()->get('global.user_id'))
                    ->where('course_enrollments.course_batch_id',$batch_id)
                    ->value('course_enrollments.id');
           // return $enroll_id;
           
            $attempt = isset(json_decode($content->more_data_info)->attempt)?json_decode($content->more_data_info)->attempt:1;


            $check = SyllabusStatus::where('course_enrollment_id',$enroll_id)
                        ->where('course_batch_id',$batch_id)
                        ->where('syllabus_id',$request->syllabus_id)
                        ->first();
             $totalmark= 0;
             $passmark= 0;
             if($Syllabus && $Syllabus->contents){
             	if(isset(json_decode($Syllabus->contents->more_data_info)->total_mark)){
             		$totalmark = json_decode($Syllabus->contents->more_data_info)->total_mark;
             	}

             	if(isset(json_decode($Syllabus->contents->more_data_info)->pass_mark)){
             		$passmark = json_decode($Syllabus->contents->more_data_info)->pass_mark;
             	}
             	
             }
            
            if($check){
                if($res->content_type=='assignment' || $res->content_type=='exam' ||$res->content_type=='quiz'){
                    if(($attempt>=$check->attempt) || ($attempt+$check->extra_attempt)>=($check->attempt+$check->extra_attempt)){
                        
                        if($request->marks>$check->marks){
                            $check->answers = isset($request->answers)?$request->answers:null;
                            $check->submission_time = isset($request->submission_time)?$request->submission_time:null;
                            $check->ques_ids = isset($request->ques_ids)?$request->ques_ids:null;
                            $check->mark = isset($request->marks)?$request->marks:null;
                            $check->attempt = $check->attempt+1;
                            $check->file_id =  isset($request->file_id)?$request->file_id:null;
                            $check->total_marks =  $totalmark;
                            $check->pass_mark =  $passmark;
                        }else{
                            $check->attempt = $check->attempt+1;
                            $check->submission_time = isset($request->submission_time)?$request->submission_time:null;
                        } 
                        $check->update();
                    }else{
                        return response()->json(['message' => 'You dont have any attempt remaining','status'=>false]);
                    }
                }else{
                    
                    if($quiz==''){
                        
                        
                    }else{
                            $check->answers = $request->answers;
                            $check->ques_ids = $request->ques_ids;
                            $check->mark = $request->marks;
                            $check->completeness = $request->completeness;
                            $check->attempt = $check->attempt+1;
                            $check->total_marks =  $totalmark;
                            $check->pass_mark =  $passmark;
                            $check->submission_time = isset($request->submission_time)?$request->submission_time:null;
                            $check->update();
                    }
                }
                

            }else{
                $submit = new SyllabusStatus;

                $file_info = json_decode($request->answers);

                $submit->course_enrollment_id = $enroll_id;
                $submit->course_batch_id = $batch_id;
                $submit->syllabus_id = isset($request->syllabus_id)?$request->syllabus_id:null;
                $submit->submission_time = isset($request->submission_time)?$request->submission_time:null;
                $submit->answers = isset($request->answers)?$request->answers:null;
                $submit->feedback_arr = isset($request->feedback_arr)?$request->feedback_arr:null;
                $submit->ques_ids = json_encode($ids);
                $submit->file_id =  isset($request->file_id)?$request->file_id:null;
                $submit->obtain_marks = isset($request->obtain_marks)?$request->obtain_marks:null;
                $submit->status = $request->status;
                $submit->attempt = 1;
                $submit->total_marks =  $totalmark;
                $submit->pass_mark =  $passmark;
                $submit->mark = isset($request->marks)?$request->marks:null;
                $submit->save();
            }


            return response()->json(['message' => 'submitted successfully','status'=>true]);
          }else{
            return response()->json(['message' => 'Not submitted successfully','status'=>false]);
          }
    }


}
