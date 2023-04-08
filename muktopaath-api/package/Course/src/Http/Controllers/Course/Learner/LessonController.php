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


            $res = Syllabus::where('id',$lesson_id)->value('learning_content_id');
           
            if(!$res){
                return response()->json(
                    [ 
                        'message' => 'No contents found',
                        'data' => ''
                    ]
                );
            }

            $content = LearningContent::where('id',$res)->first();

            return new LearnerResource($content);
    }

    public function show_details($lesson_id){
        $res = Syllabus::where('id',$lesson_id)->first();

        return response()->json($res);
    }


    public function showstatus($syllabus_id){

            $status = SyllabusStatus::select('syllabus_statuses.mark','syllabuses.learning_content_id')
                ->join('syllabuses','syllabuses.id','syllabus_statuses.syllabus_id')
                ->join('course_enrollments ce','ce.id','syllabus_statuses.course_enrollment_id')
                ->join('orders o','o.id','course_enrollments.order_id')
                ->where('o.user_id',config()->get('global.user_id'))
                ->where('syllabuses.id',$syllabus_id)
                ->first();

                if($status){
                    $res = LearningContent::where('id',$status->learning_content_id)->first();
                    $res['obtained_mark'] = $status->mark;
                    $res['total_marks'] = array_sum(json_decode($res['quiz_marks']));
                    return response()->json($res);
                }else{
                    return response()->json(400);
                }
            
    }
}
