<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course\CourseBatch;
use App\Models\Account\Order;
use App\Models\Course\CourseEnrollment;
use App\Models\Course\Course;
use App\Http\Resources\Partner\CourseResource;
use App\Http\Resources\Partner\CourseBatchResource;
use App\Lib\EnrollMentPartner;
use Validator;
use Auth;
use DB;
use App\Lib\EnrollMent;
class PartnerCourseController extends Controller
{
   use EnrollMentPartner,EnrollMent;
   public function course_list()
   {   
   		$course = Course::where('owner_id',config('global.partner'))->get();
   		return CourseResource::collection($course);
   }

   public function course_show($course_id)
   {
      
   	  return $course = Course::where('id',$course_id)->where('owner_id',config('global.partner'))->get();
   	  return new CourseResource($course);
   }

   public function course_batch($course_id)
   {
   	  $courseBatch = CourseBatch::where('course_id',$course_id)->where('owner_id',config('global.partner'))->get();
      return CourseBatchResource::collection($courseBatch);
   }

   public function course_enrollment(Request $request,$batch_id)
   {
        try {
           foreach($request->all() as $user)
           {
               $validator = Validator::make($user, [
                   'name' => 'required|max:200',
                   'email' => 'required',
               ]);
               if($validator->fails()) {
                   return response()->json($validator->errors());
               }
           }
       	  $data = $request->all();
       	  if(count($data)<=100)
       	  {
            
              
                $users = $this->userCreateOrUpdate($data);
                
                ///return $users;
                // if($users==1){
                //     return response()->json(['error'=>'Users not valid formet']);
                // }
                $this->restrictedCourseRecord($users,$batch_id);
                //   $course_enrollment = $this->enrollmentById($batch_id,$users);
                  // $this->enrollmentSuccess();
                  return response()->json(['success'=>'enrollment successfully']);
              
              return response()->json(['error'=>'not enrollment successfully']);
          }else{
       	      return response()->json(['error'=>'Numbers of users more than 100']);
          }
        }catch (ValidationException $e){
            return response()->json([
                'code'    => 406,
                'message' => "forbidden",
                'errors'  => $e->getMessage(),
            ]);
        }
   }
   
   public function course_enrollment_accesscode($access_code){
      
       $user_exist = DB::table('restricted_course_records')->where('access_code',$access_code)->where('user_id',Auth::user()->id)->orWhere('user_id',Auth::user()->email)->orWhere('user_id',Auth::user()->phone)->first();
      
      if(!empty($user_exist)){
             $checkEnrollMent = DB::table('orders')->select('course_enrollments.id')->join('course_enrollments','orders.id', '=', 'course_enrollments.order_id')->where('orders.user_id',Auth::user()->id)->where('course_enrollments.course_batch_id',$user_exist->course_batch_id)->first();
          
          	
          	if($checkEnrollMent){
          	    
          	    $endata = $this->EnrollMentById2($user_exist->course_batch_id,Auth::user()->id);
          	    if($endata){
          	       DB::table('restricted_course_records')->where('id',$user_exist->id)->update(['status' => 1]);
          	       return response()->json([
                    'status'    => 1,
                    'enroll_id' => $endata->id,
                  ]);
          	    }else{
          	      return response()->json([
                    'status'    => 0,
                  ]);
          	    }
          	}else{
          	    return response()->json([
                'status'    => 2,
                'enroll_id' => $checkEnrollMent->id,
              ]);
          	}
           	
      }else{
          return response()->json([
            'status'    => 0,
          ]);
      }
   }
   
   public function course_enrollment_certificate_approved(Request $request,$batch_id){
       
      $data = $request->all();
      if(isset($data['status']) && $data['status']==1){
          DB::table('certificate_submit')->whereIn('tracking_code',$data['tracking_code'])->update(['status' =>1]);
      }else if(isset($data['status']) && $data['status']==0){
          DB::table('certificate_submit')->whereIn('tracking_code',$data['tracking_code'])->update(['status' =>0]);
      }else{
          return "asda";
      }
    //   $user_exist = DB::table('restricted_course_records')->where('access_code',$access_code)->where('user_id',Auth::user()->id)->first();
    //   if(!empty($user_exist)){
    //          $checkEnrollMent = DB::table('orders')->join('course_enrollments', 'orders.id', '=', 'course_enrollments.order_id')->where('orders.user_id',$user_exist->user_id)->where('course_enrollments.course_batch_id',$user_exist->course_batch_id)->get();
    //       	if($checkEnrollMent){
    //       	    $this->EnrollMentById2($user_exist->course_batch_id,$user_exist->user_id);
    //       	    return 1;
    //       	}else{
    //       	    return 2;
    //       	}
           	
    //   }else{
    //       return 0;
    //   }
   }

   public function batch_participant($batch_id)
   {
      $batch_participants = CourseEnrollment::with(['coursebatch'])->where('course_batch_id',$batch_id)->get();
      return response()->json($batch_participants);  
   }

   public function quizcheck(Request $request)
   {

    $data = $request->all();
    $arr = array();
    $arr['quiz'] = json_encode($data);
     DB::table('quizchecks')->insert($arr);
   }

   public function examCheck(Request $request)
   {
    $data = $request->all();
    $arr = array();
    $arr['exam'] = json_encode($data);
    DB::table('exam_checks')->insert($arr);
   }

   public function assignmentCheck(Request $request)
   {
    $data = $request->all();
    $arr = array();
    $arr['assignment'] = json_encode($data);
    DB::table('assignment_check')->insert($arr);
   }

   public function completenessUpdate(Request $request)
   {
     $data = $request->all();
    $arr = array();
    $arr['course_completeness'] = json_encode($data);
    DB::table('completeness_check')->insert($arr);
    //return response()->json(['updated'=>'successfully completeness']);
   }
}
