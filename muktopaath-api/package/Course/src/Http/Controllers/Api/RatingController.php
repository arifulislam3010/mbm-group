<?php

namespace Muktopaath\Course\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Models\Course\Rating;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Http\Resources\CourseRating as CourseEnrolledRating;
use Auth;
// use App\Lib\GamificationClass;

class RatingController extends Controller
{
    public $successStatus = 200;
    public function CourseRatingSubmit(Request $request,$id){
    	$user = config()->get('global.user_id');
    	$rating = Rating::where('enrollement_id',$id)->first();
    	if($rating){
    		$rating->rating_point = $request['rating_point'];
            $rating->feedback_comments = $request['feedback_comments'];
            $rating->created_by = config()->get('global.user_id');
    	    if($rating->update()){
    	    	return response()->json([
	            'api_info'    => 'Ratting Update',
	            'data'        => new CourseEnrolledRating($rating),
	        	] , $this->successStatus);
    	    }else
    	    {
    	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
    	    }
    	}else{

    		$ratingSave = new Rating();
    		$ratingSave->rating_point = $request['rating_point'];
            $ratingSave->feedback_comments = $request['feedback_comments'];
            $ratingSave->created_by = config()->get('global.user_id');
            $ratingSave->enrollement_id = $id;
    	    if($ratingSave->save()){
                // $gamification_class = new GamificationClass;
                // $gamification_class->gamificationStore('rt',$id,$id,1);
    	    	return response()->json([
	            'api_info'    => 'Ratting Save',
	            'data'        => new CourseEnrolledRating($ratingSave),
	        	] , $this->successStatus);
    	    }else{
    	    	return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
    	    }
    	}
    }

    public function course_reviews($course_id){

        $db = config()->get('database.connections.my-account.database');

        $res = Rating::select('course_enrollment_rating.id','course_enrollment_rating.rating_point','course_enrollment_rating.feedback_comments','course_enrollment_rating.enrollement_id','course_enrollments.user_id')
                ->join('course_enrollments','course_enrollments.id','course_enrollment_rating.enrollement_id')
                ->where('course_enrollments.course_batch_id',$course_id)
                ->with('creator')
                ->paginate(6);

        return response()->json($res);
    }
}
