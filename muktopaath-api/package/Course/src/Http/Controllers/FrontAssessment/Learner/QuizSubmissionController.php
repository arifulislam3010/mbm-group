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

            $res = Syllabus::where('id',$request->syllabus_id)->value('learning_content_id');
            

            $content = LearningContent::where('id',$res)->first();
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


            // foreach ($answer as $key => $value) {
            //     $mark+=$carry[$key];

            //     foreach ($value as $key1 => $value1) {
            //         if($quiz[$key]['type']=='true-false'){
            //             if($data[$key][$key1]['answer']==''){
            //                 $ans = 'false';
            //             }else if($data[$key][$key1]['answer']==1){
            //                 $ans = 'true';
            //             }
            //             if($value1->answer!==$ans){

            //             $count++;
            //             $mark-=$carry[$key];


            //             break;
            //         }
            //         }else{

            //         if($value1->answer!==$data[$key][$key1]['answer']){

            //             $count++;
            //             $mark-=$carry[$key];


            //             break;
            //         }
            //     }
            //     }
            // }

            $batch_id = Syllabus::where('id',$request->syllabus_id)->value('course_batch_id');

            $enroll_id = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                    ->where('o.user_id',config()->get('global.user_id'))
                    ->where('course_enrollments.course_batch_id',$batch_id)
                    ->value('course_enrollments.id');
           // return $enroll_id;
            $attempt = isset($content->more_data_info->attempt)?$content->more_data_info->attempt:1;


            $check = SyllabusStatus::where('course_enrollment_id',$enroll_id)
                        ->where('course_batch_id',$batch_id)
                        ->where('syllabus_id',$request->syllabus_id)
                        ->first();
            if($check){
                if($attempt>=$check->attempt){
                    if($request->marks>$check->marks){
                        $check->answers = $request->answers;
                        $check->ques_ids = $request->ques_ids;
                        $check->mark = $request->marks;
                        $check->attempt = $check->attempt+1;
                        $check->update();
                    } 
                }else if(($attempt+$check->extra_attempt)>=($check->attempt+$check->extra_attempt)){
                        $check->answers = $request->answers;
                        $check->ques_ids = $request->ques_ids;
                        $check->mark = $request->marks;
                        $check->attempt = $check->attempt+1;
                        $check->update();
                }else{
                    return response()->json(['message' => 'You dont have any attempt remaining']);
                }

            }else{
                $submit = new SyllabusStatus;

                $file_info = json_decode($request->answers);

                $submit->course_enrollment_id = $enroll_id;
                $submit->course_batch_id = $batch_id;
                $submit->syllabus_id = isset($request->syllabus_id)?$request->syllabus_id:null;
                $submit->answers = isset($request->answers)?$request->answers:null;
                $submit->feedback_arr = isset($request->feedback_arr)?$request->feedback_arr:null;
                $submit->ques_ids = json_encode($ids);
                $submit->file_id =  $file_info->file_id;
                $submit->obtain_marks = isset($request->obtain_marks)?$request->obtain_marks:null;
                $submit->status = $request->status;
                $submit->attempt = 1;
                $submit->mark = isset($request->marks)?$request->marks:null;
                $submit->save();
            }


            return response()->json(['message' => 'submitted successfully']);
    }


}
