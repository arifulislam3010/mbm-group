<?php

namespace App\Http\Controllers\FrontAssessment\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Syllabus;
use App\Models\Assessment\SyllabusStatus;
use App\Models\ContentBank\LearningContent;
use App\Http\Resources\Assessment\LearnerResource;
use App\Models\Question\Question;

class LessonController extends Controller
{
    public function show($lesson_id){

            $datetime = gmdate("Y-m-d H:i:s",strtotime('+6 Hours'));

            $res = Syllabus::where('id',$lesson_id)->first();
           
            if(!$res){
                return response()->json(
                    [ 
                        'message' => 'No contents found',
                        'data' => ''
                    ]
                );
            }
    
            $content = LearningContent::where('id',$res->learning_content_id)->first();
            if($content){
            $content['start_date'] = $res->start_date;
            $content['end_date'] = $res->end_date;
            $content['current_date_time'] = $datetime;

            
            if($content->content_type==!"exam" || $content->content_type==!"quiz" || $content->content_type==!"assignment"){
                $data['data'] = $content;
                return response()->json($data);
            }


            return new LearnerResource($content);
        }
    }

    public function show_details($lesson_id){
        $res = Syllabus::where('id',$lesson_id)->first();
        $res['current_date_time'] = date("Y-m-d H:i:s",time() - 6*3600);

        return response()->json($res);
    }

    public function showstatus($syllabus_id){

        $syllabus = Syllabus::find($syllabus_id);
        if($syllabus){
            $content = LearningContent::where('id',$syllabus->learning_content_id)->first();
            if($content->content_type=='exam' || $content->content_type=='exam'){
             $status = SyllabusStatus::select('syllabus_statuses.mark','syllabuses.learning_content_id')
                ->join('syllabuses','syllabuses.id','syllabus_statuses.syllabus_id')
                ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
                ->join('orders as o','o.id','ce.order_id')
                ->where('o.user_id',config()->get('global.user_id'))
                ->where('syllabus_statuses.syllabus_id',$syllabus_id)
                ->first();

                if($status){
                    $res = LearningContent::where('id',$status->learning_content_id)->first();
                    $res['obtained_mark'] = $status->mark;
                    $res['total_marks'] = array_sum(json_decode($res['quiz_marks']));
                    return response()->json($res);
                }else{
                    return response()->json(400);
                }
            }else{
                return response()->json($content);
            }
        }else{
            return response()->json(['message' => 'This id of syllabus does not exist']);
        }

        
            
    }
}
