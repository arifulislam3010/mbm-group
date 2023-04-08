<?php

namespace Muktopaath\Course\Repositories;

use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Interfaces\CourseRepositoryInterface;
use DB;
use Auth;

class CourseRepository implements CourseRepositoryInterface
{
     public function enrollbyadmin($request, $batch_id)
    {
        $user_id = Auth::user()->id;
        
        $check = CourseEnrollment::select('course_enrollments.id as enroll_id')
                   ->join('orders','orders.id','course_enrollments.order_id')
                   ->where('orders.user_id',$user_id)
                   ->where('course_enrollments.course_batch_id',$batch_id)
                   ->first();

                   if($check){
                       return  response()->json($check->enroll_id);
                   }
                   else{
       

       $check = Order::select('course_enrollments.id')->join('course_enrollments','course_enrollments.order_id','orders.id')
                   ->where('course_enrollments.course_batch_id',$batch_id)
                   ->where('orders.user_id',$user_id)
                   ->first();


                   if($check){
                       return response()->json(['message' => 'You are already enrolled in this assessment .!']);
                   }else{
                       DB::beginTransaction();
       try{
           $order = new Order;
               $order->amount              = 0;
               $order->payment_status      = 0;
               $order->type                = 0;
               $order->user_id             = $user_id;
               $order->save();

               $course_enrollments     = new CourseEnrollment;
               $course_enrollments->order_id           = $order->id;
               $course_enrollments->course_batch_id    = $batch_id;
               $course_enrollments->journey_status     = '[]';
               $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
               $course_enrollments->course_completeness = 0;
               $course_enrollments->status = 1;
               $course_enrollments->save();
               DB::commit();

               return response()->json($course_enrollments->id);
       }catch (\Exception $e) {
           DB::rollback();
   // something went wrong
}
                   }

       }
    }
    
    
}