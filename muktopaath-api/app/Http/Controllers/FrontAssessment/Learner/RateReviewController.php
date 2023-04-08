<?php

namespace App\Http\Controllers\FrontAssessment\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Review;
use App\Models\Assessment\CourseEnrollment;
use App\Http\Resources\Assessment\Reviews;


class RateReviewController extends Controller
{
    public function store(Request $request, $id){
   
    	$check = Review::select('reviews.id')->join('course_enrollments as ce','ce.id','reviews.course_enrollment_id')
                ->join('orders as o','o.id','ce.order_id')
                ->where('ce.course_batch_id',$id)
                ->where('o.user_id',config()->get('global.user_id'))
                ->first();

    	if($check){
            $data = Review::find($check->id);
    		$data->rating = $request->rating;
    		$data->review = $request->review;
    		$data->update();
    	}else{
            $dt = CourseEnrollment::join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                    ->join('orders as o','o.id','course_enrollments.order_id')
                    ->value('course_enrollments.id');
    		$data = $request->all();
            $data['course_batch_id'] = $id;
	        $data['course_enrollment_id'] = $dt;

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

        $res = Review::select('reviews.review','reviews.rating','orders.user_id','reviews.course_enrollment_id','reviews.created_at')
                ->join('course_enrollments','course_enrollments.id','reviews.course_enrollment_id')
                ->join('orders','orders.id','course_enrollments.order_id')
            ->where('reviews.course_batch_id',$id)->orderby('reviews.id','DESC')->paginate(10);
            

         return  Reviews::collection($res)->additional(['userdata' => $userinfo]);

    }
}
