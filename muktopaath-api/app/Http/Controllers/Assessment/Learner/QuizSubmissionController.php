<?php

namespace App\Http\Controllers\Assessment\Learner;

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
    public function store(Request $request,$enroll_id, $lesson_id){

    	    $data = $request->all();
    

    		$res = Syllabus::where('id',$lesson_id)->value('learning_content_id');
    		

    		$content = LearningContent::where('id',$res)->first();
    		$carry = json_decode($content->quiz_marks);


    		$ids = json_decode($content->quiz_data);
    		$ids_ordered = implode(',', $ids);
    		
    		$quiz = Question::wherein('id',$ids)
    				->orderByRaw("FIELD(id, $ids_ordered)")
    				->get();

    	    $answer = [];


    		foreach ($quiz as $key => $value) {
    

    			array_push($answer, json_decode($value->answer));
    		}
    		$count = 0;
    		$mark = 0;


    		foreach ($answer as $key => $value) {
    			$mark+=$carry[$key];

    			foreach ($value as $key1 => $value1) {

    				if($value1->answer!==$data[$key][$key1]['answer']){
    					$count++;
    					$mark-=$carry[$key];


    					break;
    				}
    			}
    		}

    		$batch_id = CourseEnrollment::where('id',$enroll_id)->value('course_batch_id');

    		$submit = new SyllabusStatus;

    		$submit->course_enrollment_id = $enroll_id;
    		$submit->course_batch_id = $batch_id;
    		$submit->syllabus_id = $lesson_id;
    		$submit->status = 'exam';
    		$submit->mark = $mark;
    		$submit->save();

    		return response()->json($mark);
    }
}
