<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Mail\MailSender;
use Illuminate\Http\Request;
use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Illuminate\Support\Facades\Config;
use Muktopaath\Course\Models\Course\RestrictedUser;
use App\Repositories\Validation;
use App\Interfaces\Myaccount\UserRepositoryInterface;
use DB;
use Auth;
use App\Models\Myaccount\User;
use App\Lib\ManualEncodeDecode;
use Illuminate\Support\Facades\Mail;
use App\Lib\SMS;
use App\Jobs\SendMailJob;
use Muktopaath\Course\Models\Course\CourseBatch;
use App\Jobs\SendSmsJob;

class RestrictedUserEnrollController extends Controller
{
    private $userRepository;
    private $val;
    
    public function __construct(UserRepositoryInterface $userRepository, Validation $val)
    {
        $this->userRepository = $userRepository;
        $this->val = $val;
    }
    
   public function sendToken(Request $request){
        $info = new ManualEncodeDecode();
        
        $email = (isset($request['email']))?$request['email']:null;
        $restricted_access_code = (isset($request['restricted_access_code']))?$request['restricted_access_code']:null;
        
        $token = $info->encode($email . '<:MP:>' . $restricted_access_code, env('ENCRIPTION_KEY'));
        $front_url = config()->get('global.front_url');
        $link = $front_url.'assessment-front/batch/restricted-user-enroll?token='.$token;

        $course = CourseBatch::where('restricted_access_code', '=', $restricted_access_code)
            ->select('title', 'course_alias_name', 'course_alias_name_en')
            ->first();

        if($course->course_alias_name){
            $course_name = $course->course_alias_name;
        }else{
            $course_name = $course->course_alias_name_en;
        }
        $data = [
            'subject' => 'Action required: You are invited to enroll this Muktopaath course',
            'short_name' => 'Muktopaath course',
            'to'   => $email,
            'link' => $link,
            'template' => 'restrictenroll',
            'course_name' => $course_name,
            'message' => 'You are invited to enroll this Muktopaath course ' .$link
        ];
        
        if(is_numeric($email)){
            dispatch(new SendSmsJob($data));
        }else{
            Mail::to($data['to'])->send(new MailSender($data));
        }
        return response()->json($data);
   }

   public function shareRestrictedCourse(Request $request)
    {
        $restricted_code = (isset($request['token']))?$request['token']:null;
        $user_id = config()->get('global.user_id');
        $user = User::find($user_id);
        $batch = RestrictedUser::where('restricted_code', '=', $restricted_code)
        ->where(function($q) use($user){
            return $q->where('email_or_phone',$user->email)
            ->orWhere('email_or_phone',$user->phone);
        })->first();
        if($batch){
               
            $check = CourseEnrollment::select('course_enrollments.id as enroll_id')
                    ->join('orders','orders.id','course_enrollments.order_id')
                    ->where('orders.user_id',$user_id)
                    ->where('course_enrollments.course_batch_id',$batch->batch_id)
                    ->first();

                    if($check){
                        $userdata = $this->userRepository->authuserinfoById($user_id);
                        $userdata['batch_id']=$batch->batch_id;
                        return response()->json($userdata);
                        return response()->json(['message' => 'You are already enrolled in this assessment .!']);
                    }
                    else{
                        
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
                            $course_enrollments->course_batch_id    = $batch->batch_id;
                            $course_enrollments->journey_status     = '[]';
                            $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
                            $course_enrollments->course_completeness = 0;
                            $course_enrollments->status = 1;
                            $course_enrollments->save();
                            DB::commit();
                            $userdata = $this->userRepository->authuserinfoById($user_id);
                            $userdata['batch_id']=$batch->batch_id;
                            return response()->json($userdata);
                    }catch (\Exception $e) {
                            DB::rollback();
                    }
                
            }
        }else{
           return response()->json(['message' => 'No data found!']); 
        }

    }
    
}