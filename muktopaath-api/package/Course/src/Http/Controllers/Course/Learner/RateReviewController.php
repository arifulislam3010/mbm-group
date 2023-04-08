<?php

namespace App\Http\Controllers\Assessment\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Review;
use App\Models\Assessment\CourseEnrollment;
use App\Http\Resources\Assessment\Reviews;


class RateReviewController extends Controller
{
    public function store(Request $request, $id){
   
    	$check = Review::where('course_enrollment_id',$id)
    				->where('course_batch_id',$request->course_batch_id)
    				->first();
    	if($check){
    		$rev = Review::where('course_enrollment_id',$id)->first();
    		$rev->rating = $request->rating;
    		$rev->review = $request->review;
    		$rev->save();
    	}else{
    		$data = $request->all();
	        $data['course_enrollment_id'] = $id;

	        $res = Review::create($data);
    	} 

        return response()->json(['success' => 'Review given successfully.']);
    }


    public function reviews($id){
    	$userinfo = Review::join('course_enrollments as ce','ce.id','reviews.course_enrollment_id')
                ->join('orders as o','o.id','ce.order_id')
                ->where('ce.course_batch_id',$id)
                ->where('o.user_id',config()->get('global.user_id'))
                ->first();
        return $userinfo;

    	$res = Review::select('reviews.review','reviews.rating','orders.user_id','reviews.course_enrollment_id','reviews.created_at')
    			->join('course_enrollments','course_enrollments.id','reviews.course_enrollment_id')
    			->join('orders','orders.id','course_enrollments.order_id')
    		->where('reviews.course_batch_id',$id)->orderby('reviews.id','DESC')->paginate(10);
    		

    	 return  Reviews::collection($res)->additional(['userdata' => $userinfo]);

    }
}
