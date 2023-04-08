<?php

namespace App\Http\Controllers\FrontAssessment;

use App\Http\Controllers\Controller;
use App\Mail\MailSender;
use Illuminate\Http\Request;
use App\Models\Assessment\Course;
use App\Models\Assessment\CourseBatch;
use App\Models\Assessment\CourseEnrollment;
use App\Models\Assessment\Syllabus;
use App\Http\Resources\Assessment\SyllabusResource;
use App\Models\Myaccount\InstitutionInfo;
use App\Models\Assessment\Template;
use App\Lib\ManualEncodeDecode;
use App\Models\Myaccount\User;
use App\Models\Myaccount\SystemRole;
use App\Models\Myaccount\Sharing;
use App\Models\Assessment\CourseCategory;
use App\Models\Assessment\CourseTag;
use App\Models\Assessment\Tag;
use App\Models\Assessment\Order;
use App\Models\Assessment\RestrictedUser;
use DB;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Validation;
use App\Interfaces\Myaccount\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use App\Lib\SMS;
use Yaml;
use App\Jobs\SendMailJob;
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
public function enrollCourse(Request $request){
        $data = new ManualEncodeDecode();
        
        list($email,$restricted_access_code) = explode('<:MP:>',$data->decode($request['token'], env('ENCRIPTION_KEY')));
        $user = User::where('email', '=', $email)->OrWhere('phone',$email)
                ->first();
      
        if(empty($user)){
            $password = mt_rand(1000000000, 9999999999);
			$request['name'] = 'user_name';
            $request['gender'] = 1;
            $request['profession_id'] = 3;
            $request['password'] = $password;
            $request['password_confirmation'] = $password;
            
            if(is_numeric($email)){
                $request['phone']  = $email;

                $student = new User;
                $student->phone = $request['phone'];
                $student->name = $request['name'];
                $student->password = Hash::make($request['password']);
                $student->verify_status_phone = 1;
                $student->status = 1;
                $student->save();
            }else{
                $request['email'] = $email;

                $student = new User;

                $student->email =$request['email'];
                $student->name = $request['name'];
                $student->password = Hash::make($request['password']);
                $student->verify_status = 1;
                $student->status = 1;
                $student->save();
            }

                
                    
                // if($this->val->validateCondition($rules, $request->all())){
                //  return $this->val->validateCondition($rules, $request->all());
                // }


    			//$user = $this->userRepository->createUserBasics($request->all());
    	
    		
                $user = $student;
    
                $this->userRepository->createUserDetails($user->id, $request->all());
                
                $otpCode = mt_rand(1111,9999);

    			$data = [
                    'subject' => 'Action required: You are invited to enroll this Muktopaath course',
                    'short_name' => 'Muktopaath account',
    	            'name'=> 'user',
    	            'password' => $password,
    	            'to' => $email,
    	            'template' => 'newidp',
                    'message' => "Your Muktopaath id " .$email ."\n"." and password is " ."\n" .$password
    	        ];
    
                if(is_numeric($email)){
                    dispatch(new SendSmsJob($data));
                }else{
                    Mail::to($data['to'])->send(new MailSender($data));
                }
    
                // Mail::send('mail.newidp', $data, function($message) use($data){
                //     $message->to($data['email'],$data['name'])->subject('Muktopaath id and password');
                //     $message->from(env('MAIL_VERIFY_ACCOUNT'),'Muktopaath');
                // });

                
        }

        $batch = RestrictedUser::select('restricted_users.user_type','restricted_users.batch_id','cb.owner_id')
                ->join('course_batches as cb','cb.id','restricted_users.batch_id')
                ->where('restricted_users.restricted_code', '=', $restricted_access_code)
                ->where('restricted_users.email_or_phone',$email)
                ->first();
        
        if(!$batch){
            return response()->json(['message' => "You aren't permitted in this assessment!"]);
        }

        $yaml = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));
        
        
        if($batch->user_type==1){
            if($batch->owner_id==1){
                $role = 'sys-teacher';
                $arr = $yaml['sys-teacher'];
            }else{
                $role = 'teacher';
                $arr = $yaml['teacher'];
            }
        }

        $arr['service']['classroom']['access']['class']['view_all'] = false;
        $arr['service']['classroom']['access']['schedule']['view-syllabus-all_all'] = false;
        $arr['service']['classroom']['access']['dashboard']['header-stats_all'] = false;
        $arr['service']['classroom']['access']['people']['delete'] = false;

        $acc = [];

        foreach ($arr['service'] as $key => $value) {
            foreach ($value['access'] as $key1 => $value1) {
                $acc[$key1] = $value1;
            }
        }

                $check = CourseEnrollment::select('course_enrollments.id as enroll_id')
                   ->join('orders','orders.id','course_enrollments.order_id')
                   ->where('orders.user_id',$user->id)
                   ->where('course_enrollments.course_batch_id',$batch->batch_id)
                   ->first();
                   
                    DB::beginTransaction();
                    
                if($batch->user_type==1){
                    try{
                        $role_exist = SystemRole::where('owner_id',$batch->owner_id)
                            ->where('user_id',$user->id)
                            ->first();


                    if(!$role_exist){

                        $access = new SystemRole;
                        $access->role = 'teacher';
                        $access->json_schema = json_encode($yaml['teacher']);
                        $access->access = json_encode($acc);
                        $access->owner_id = $batch->owner_id;
                        $access->user_id = $user->id;
                        $access->created_by = config()->get('global.user_id');
                        $access->save();

                        $share = new Sharing;
                        $share->db_con_name = 'classroom';
                        $share->table_name = 'course_batches';
                        $share->table_id = $batch->batch_id;
                        $share->activity = 'all';
                        $share->user_id = $user->id;
                        $share->created_by = config()->get('global.owner_id');
                        $share->save();
                    }else{
                        $already_shared = Sharing::where('table_id',$batch->batch_id)
                                            ->where('user_id',$user->id)
                                            ->first();
                        if(!$already_shared){
                            $share = new Sharing;
                            $share->db_con_name = 'classroom';
                            $share->table_name = 'course_batches';
                            $share->table_id = $batch->batch_id;
                            $share->activity = 'all';
                            $share->user_id = $user->id;
                            $share->created_by = config()->get('global.owner_id');
                            $share->save(); 
                        }


                    }


                   

                        DB::commit();
                    }
                    catch (\Exception $e) {
                        DB::rollback();
                    }
                        }

                   if($check){
                       
                        $user['token'] =  $user->createToken('Laravel Password Grant Client')->accessToken;
                        $userdata = $this->userRepository->authuserinfoById($user->id);
                        $userdata['batch_id'] = $batch->batch_id;
                        $response = ['data' => $user,'user'=>$userdata];

                       return response()->json($response,200);
                       return response()->json(['message' => 'You are already enrolled in this assessment.!']);
                   }
                   else{


                       
                    // DB::beginTransaction();
                               
                    //  try{
                            $order = new Order;
                            $order->amount              = 0;
                            $order->payment_status      = 0;
                            $order->type                = 0;
                            $order->user_id             = $user->id;
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
                            $user['token'] =  $user->createToken('Laravel Password Grant Client')->accessToken;
                            $userdata = $this->userRepository->authuserinfoById($user->id);
                            $userdata['batch_id'] = $batch->batch_id;
                            $response = ['data' => $user,'user'=>$userdata];

                            return response()->json($response,200);
                    // }catch (\Exception $e) {
                    //     DB::rollback();
                    // }
                    //djdkj
                }

    }

}