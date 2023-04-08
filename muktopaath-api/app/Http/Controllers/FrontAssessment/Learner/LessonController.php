<?php

namespace App\Http\Controllers\FrontAssessment\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Syllabus;
use App\Models\Myaccount\Sharing;
use App\Models\Assessment\SyllabusStatus;
use App\Models\ContentBank\LearningContent;
use App\Http\Resources\Assessment\LearnerResource;
use App\Models\Question\Question;
use App\Models\Assessment\CourseBatch;
class LessonController extends Controller
{
    public function show($lesson_id){

            $db = config()->get('database.connections.my-account.database');
            $db2 = config()->get('database.connections.classroom.database');

            $datetime = gmdate("Y-m-d H:i:s",strtotime('+6 Hours'));

            $res = Syllabus::where('id',$lesson_id)->with('live_class_info')->first();

            $res['is_teacher_owner'] =  Syllabus::select('u.id')
                ->join('course_batches as cb','cb.id','syllabuses.course_batch_id')
                ->join($db.'.institution_infos as i','i.id','cb.owner_id')
                ->join($db.'.users as u','u.id','i.user_id')
                ->value('u.id') == config()->get('global.user_id')?1:0;

            $res['is_teacher_asigned']  = Sharing::select('sharings.user_id')->join($db2.'.course_batches as cb','cb.id','sharings.table_id')
            ->join($db2.'.syllabuses as s','s.course_batch_id','cb.id')
            ->where('s.id',$lesson_id)
            ->where('sharings.user_id',config()->get('global.user_id'))
            ->value('sharings.user_id')?1:0;
            $content = LearningContent::where('id',$res->learning_content_id)->first();
            $host = false;
            
            if(config()->get('global.owner_id')){
                $CourseBatch = CourseBatch::where('id',$res->course_batch_id)->where('owner_id',config()->get('global.owner_id'))->first();
                if($CourseBatch){
                    $host = true;
                }else{
                    $host = false;
                }
            }else{
                $host = false;
            }
            $res['host'] = $host;
        if($content){
            $content['start_date'] = $res->start_date;
            $content['end_date'] = $res->end_date;
            $content['current_date_time'] = $datetime;
            $content['syllabus_id'] = $res->id;

            
            $content['host'] = $host;

            
            if($content->content_type==!"exam" || $content->content_type==!"quiz" || $content->content_type==!"assignment"){
                $data['data'] = $content;
                return response()->json($data);
            }


            return new LearnerResource($content);
        }else{
            return response()->json($res);
        }
    }

    public function show_details($lesson_id){
        $res = Syllabus::where('id',$lesson_id)->with('live_class_info')->first();
        $res['current_date_time'] = date("Y-m-d H:i:s",time() - 6*3600);
        $host = 0;
        if(config()->get('global.owner_id')){
            $CourseBatch = CourseBatch::where('id',$res->course_batch_id)->where('owner_id',config()->get('global.owner_id'))->first();
            if($CourseBatch){
                $host = true;
            }else{
                $host = false;
            } 
        }else{
            $host = false;
        }
        $res['host'] = $host;

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
