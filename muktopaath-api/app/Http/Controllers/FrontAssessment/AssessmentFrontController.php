<?php

namespace App\Http\Controllers\FrontAssessment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Course;
use App\Models\Assessment\CourseBatch;
use App\Models\Assessment\CourseEnrollment;
use App\Models\Assessment\Syllabus;
use App\Http\Resources\Assessment\SyllabusResource;
use App\Models\Myaccount\InstitutionInfo;
use App\Models\Assessment\Template;

class AssessmentFrontController extends Controller
{
    
    public function course(){

        $featured  = CourseBatch::with('course')->orderBy('id','DESC')->take(10)->get();
        $popular   = CourseBatch::with('course')->orderBy('id','DESC')->take(10)->get();
        $latest    = CourseBatch::with('course')->orderBy('id','DESC')->take(10)->get();
        $running   = CourseBatch::with('course')->orderBy('id','DESC')->take(10)->get();
        $completed = CourseBatch::with('course')->orderBy('id','DESC')->take(10)->get();
        $upcoming  = CourseBatch::with('course')->orderBy('id','DESC')->take(10)->get();

        $courses = array('featured' => $featured, 'popular' => $popular, 'latest' => $latest, 'running' => $running, 'completed' => $completed, 'upcoming' => $upcoming);
        return response()->json($courses,200);
    }

    public function testpdf(){
        $temp = Template::find(4);
        return response()->json(json_decode($temp->json));
    }

    public function details($id){ 

        $course  = CourseBatch::with('course')->find($id);
        if($course){
        $course['syllabus'] = SyllabusResource::collection(Syllabus::where('parent_id',null)->where('course_batch_id',$id)->get());
        if(config()->get('global.owner_id')==null){
            $partner = '';
        }else{
            $partner = InstitutionInfo::where('id',$course->owner_id)->firstorfail();
        }
        $course['owner_info'] = $partner;
        return response()->json($course,200);
    }else{
        return response()->json(['message' => 'Content not found']);
    }

    } 
    
    public function calender(Request $request){

       return  CourseEnrollment::select('syllabuses.id as syllabus_id','syllabuses.title','course_batches.course_alias_name','syllabuses.start_date')
            ->join('orders','orders.id','course_enrollments.order_id')
            ->join('course_batches','course_batches.id','course_enrollments.course_batch_id')
            ->join('syllabuses','syllabuses.course_batch_id','course_batches.id')
            ->where('orders.user_id',config()->get('global.user_id'))
            ->when($request->month, function ($query,$month) {
                    $year = date("Y");
                    return $query->whereYear('syllabuses.start_date', $year)
                            ->whereMonth('syllabuses.start_date', $month);
                })
            ->when($request->date, function ($query,$date) {
                    return $query->where('syllabuses.start_date', $date);
                })

            ->get();

    }

    public function all(){

    	$course  = CourseBatch::with('course')->groupBy('course_id')->paginate(10);
        return response()->json($course,200);

    }
}



