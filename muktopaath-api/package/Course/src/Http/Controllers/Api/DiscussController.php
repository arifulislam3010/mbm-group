<?php

namespace Muktopaath\Course\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Models\Course\CourseEnrollmentDiscuss;
use Muktopaath\Course\Models\Course\CourseEnrollmentDiscussReply;
use Muktopaath\Course\Http\Resources\Discuss\Discuss as DiscussResources;
use Auth;
use DB;

class DiscussController extends Controller
{
    public $successStatus = 200;

    public function submit(Request $request,$id){
    	$user = config()->get('global.user_id');
    	$CourseEnrollmentDiscuss = CourseEnrollmentDiscuss::where('enrollment_id',$id)->where('unit_id',$request['unit_id'])->where('lesson_id',$request['lesson_id'])->first();
    	if($CourseEnrollmentDiscuss){    		
            $CourseEnrollmentDiscuss->unit_id = $request['unit_id'];
    		$CourseEnrollmentDiscuss->lesson_id = $request['lesson_id'];
    		$CourseEnrollmentDiscuss->comments = $request['comments'];    		
    	    if($CourseEnrollmentDiscuss->update()){
    	    	return response()->json([
	            'api_info'    => 'Discuss update',
	            'data'        => new DiscussResources($CourseEnrollmentDiscuss),
	        	] , $this->successStatus);
    	    }else
    	    {
    	    	
    	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
    	    }
    	}else
    	{
    		$CourseEnrollmentDiscussSave = new CourseEnrollmentDiscuss();
            $CourseEnrollmentDiscussSave->enrollment_id = $id;
    		$CourseEnrollmentDiscussSave->unit_id = $request['unit_id'];
    		$CourseEnrollmentDiscussSave->lesson_id = $request['lesson_id'];
    		$CourseEnrollmentDiscussSave->comments = $request['comments'];    		
    	    if($CourseEnrollmentDiscussSave->save()){
    	    	return response()->json([
	            'api_info'    => 'Discuss add',
	            'data'        => new DiscussResources($CourseEnrollmentDiscussSave),
	        	] , $this->successStatus);
    	    }else{
    	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
    	    }
    	}
    }
    
    public function get_data(Request $request,$id){
        $CourseEnrollmentDiscuss = CourseEnrollmentDiscuss::with('getEnroll')->where('enrollment_id',$id)->where('unit_id',$request['unit_id'])->where('lesson_id',$request['lesson_id'])->with('getReplies')->first();
		if($CourseEnrollmentDiscuss){
			return response()->json(new DiscussResources($CourseEnrollmentDiscuss));
		}else{
			return '';
		}
    	
    }
    
    public function get_all_data(Request $request,$id){        
        // $CourseEnrollmentDiscuss = CourseEnrollmentDiscuss::with('getReplies','getEnroll')
        //     ->select("course_enrollment_discusses.*","u.id as user_id","u.name as user_name","u.username as username")
        //     ->join('course_enrollments as ce','ce.id','course_enrollment_discusses.enrollment_id')
        //     ->join('orders as o','o.id','ce.order_id')
        //     ->join('users as u','u.id','o.user_id')
        //     ->where('ce.course_batch_id',$batch_id)
        //     ->where('course_enrollment_discusses.unit_id',$request['unit_id'])
        //     ->where('course_enrollment_discusses.lesson_id',$request['lesson_id'])
        //     ->whereNotIn('ce.id',[$request['enrolled_id']])->get();
            
            
		$CourseEnrollmentDiscuss = CourseEnrollmentDiscuss::with('getReplies','getEnroll')->whereNotIn('course_enrollment_discusses.enrollment_id',[$id])
		->where('course_enrollment_discusses.lesson_id',$request['lesson_id'])->paginate(10);
		//return Response::Json($CourseEnrollmentDiscuss);
		return DiscussResources::collection($CourseEnrollmentDiscuss);
    }
    
    public function reply_submit(Request $request,$id){
    	$user = config()->get('global.user_id');
    	$CourseEnrollmentDiscussReply = CourseEnrollmentDiscussReply::where('discuss_id',$id)->where('replier_id',$user)->first();
    	if($CourseEnrollmentDiscussReply){    		
            $CourseEnrollmentDiscussReply->replier_id = $user;
    		$CourseEnrollmentDiscussReply->comments = $request['comments'];    		
    	    if($CourseEnrollmentDiscussReply->update()){
    	    	return response()->json([
	            'api_info'    => 'Discuss reply Update',
	            'data'        => $CourseEnrollmentDiscussReply,
	        	] , $this->successStatus);
    	    }else
    	    {
    	    	
    	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
    	    }
    	}else
    	{
    		$CourseEnrollmentDiscussReplySave = new CourseEnrollmentDiscussReply();
            $CourseEnrollmentDiscussReplySave->discuss_id = $id;
    		$CourseEnrollmentDiscussReplySave->replier_id = $user;
    		$CourseEnrollmentDiscussReplySave->comments = $request['comments'];    		
    	    if($CourseEnrollmentDiscussReplySave->save()){
    	    	return response()->json([
	            'api_info'    => 'Discuss reply add',
	            'data'        => $CourseEnrollmentDiscussReplySave,
	        	] , $this->successStatus);
    	    }else{
    	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
    	    }
    	}
    }
}
