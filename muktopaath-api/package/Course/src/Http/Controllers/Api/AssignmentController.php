<?php

namespace Muktopaath\Course\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Models\Course\Assignment;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Models\Course\CourseAssignmentPeerReview;
use Auth;
use DB;

class AssignmentController extends Controller
{
    public $successStatus = 200;

    public function submit(Request $request,$id){
    	$user = config()->get('global.user_id');
    	$Assignment = Assignment::where('enrollment_id',$id)->where('unit_id',$request['unit_id'])->where('lesson_id',$request['lesson_id'])->first();
    	if($Assignment){
    		$Assignment->unit_id = $request['unit_id'];
    		$Assignment->lesson_id = $request['lesson_id'];
    		$Assignment->out_of_marks = $request['out_of_marks'];
    		$Assignment->obtain_marks = $request['obtain_marks'];
    	    if($Assignment->update()){
    	    	return response()->json([
	            'api_info'    => 'Assignment Update',
	            'data'        => $Assignment,
	        	] , $this->successStatus);
    	    }else
    	    {
    	    	
    	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
    	    }
    	}else
    	{
    		$AssignmentSave = new Assignment();
    		$AssignmentSave->unit_id = $request['unit_id'];
    		$AssignmentSave->lesson_id = $request['lesson_id'];
    		$AssignmentSave->out_of_marks = $request['out_of_marks'];
    		$AssignmentSave->obtain_marks = $request['obtain_marks'];
    	    if($AssignmentSave->save()){
    	    	return response()->json([
	            'api_info'    => 'Assignment Update',
	            'data'        => $AssignmentSave,
	        	] , $this->successStatus);
    	    }else{
    	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
    	    }
    	}
    }
    
    public function peer_review_submit(Request $request){
        $PeerReviewSave = new CourseAssignmentPeerReview();
		$PeerReviewSave->syllabus_id = $request['syllabus_id'];
		$PeerReviewSave->obtain_marks = $request['obtain_marks'];
		$PeerReviewSave->review_comments = $request['review_comments'];
		$PeerReviewSave->reviewer_id = config()->get('global.user_id');;
	    if($PeerReviewSave->save()){
	    	return response()->json([
            'status'    => 1,
            'data'        => $PeerReviewSave,
        	] , $this->successStatus);
	    }else{
	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
	    }
    }
    
    public function load_for_peer_review(Request $request){
        $user_id = config()->get('global.user_id');
        $batch_id = $request->input('batch_id');
        $unit_id = $request->input('unit_id');
        $lesson_id = $request->input('lesson_id');
        
        $getData = array();
        $myaccount = config()->get('database.connections.my-account.database');
        $coursedb = config()->get('database.connections.course.database');
        $fileMdb = config()->get('database.connections.file-manager.database');
        $totalPeerReview = DB::table($coursedb.'.course_assignment_peer_review as capr')
        ->join($coursedb.'.syllabus_statuses as ceas', 'ceas.id', 'capr.syllabus_id')
        ->join($coursedb.'.course_enrollments as ce', 'ce.id', '=', 'ceas.course_enrollment_id')
        ->where('ce.course_batch_id', '=', $batch_id)
        ->where('ceas.id', '=', $lesson_id)
        ->where('capr.reviewer_id', '=', $user_id)
        ->count();
    	
    	$data = DB::table($coursedb.'.syllabus_statuses as ceas')
    	->select('ceas.course_enrollment_id','ceas.id as syllabus_id','ceas.file_id','orders.user_id','cb.type','cb.file_name','cb.file_name','cb.file_encode_path','cb.file_main_path','cb.is_url')
        ->join($coursedb.'.course_enrollments as ce', 'ce.id', '=', 'ceas.course_enrollment_id')
        ->join($coursedb.'.orders', 'orders.id', '=', 'ce.order_id')
        ->join($fileMdb.'.content_banks as cb', 'cb.id', '=', 'ceas.file_id')
        ->where('ce.course_batch_id', '=', $batch_id)
		// ->where('ceas.id', '=', $lesson_id)
        ->where($coursedb.'.orders.user_id', '!=', $user_id)
        ->whereNotIn('ceas.id', function($query) use($coursedb,$user_id){
            $query->select('syllabus_id')
              ->from($coursedb.'.course_assignment_peer_review as capr')
              ->whereRaw('capr.reviewer_id=' . $user_id);
        })->orderBy(DB::raw('RAND()'))
        ->limit(1)
        ->get();
    	
    	$getData['total_peer_review'] = $totalPeerReview;
    	$getData['assignment_data'] = $data;
    	
    	return $getData;
    }
}