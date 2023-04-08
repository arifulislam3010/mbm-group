<?php

namespace App\Repositories\Myaccount;

use App\Interfaces\Myaccount\UserRepositoryInterface;
use App\Models\Myaccount\User;
use App\Models\Myaccount\UserInfo;
use App\Models\Myaccount\PasswordReset;
use App\Models\Myaccount\TokenHistory;
use App\Http\Resources\Myaccount\UserResource;
use App\Http\Resources\Myaccount\UserCol;
use App\Models\Assessment\CourseEnrollment;
use App\Models\Assessment\Order;
use App\Models\AdminSettings\ApplicationSetting;
use App\Models\AdminSettings\Profession;
use App\Models\Assessment\CourseBatch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Lib\ManualEncodeDecode;
//use Illuminate\Support\Facades\Mail;
use App\Lib\SMS;
use DB;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
Use \Carbon\Carbon;
use App\Mail\MailSender;
use Illuminate\Support\Facades\Mail;

class UserRepository implements UserRepositoryInterface 
{
    // public function getAllOrders() 
    // {
    //     return Order::all();
    // }u

    // public function getOrderById($orderId) 
    // {
    //     return Order::findOrFail($orderId);
    // }

    public function createUserBasics($userBasics){
        
        $user = new User;
        $data = new ManualEncodeDecode();
        
        $user->name = $userBasics['name'];
          if(is_numeric($userBasics['email'])){
                $user->phone = $userBasics['email'];
            }else{
                $user->email = $userBasics['email'];
            }

        $user->certificate_name          = isset($userBasics['certificate_name'])?$userBasics['certificate_name']:null;
        $user->pdsid          = isset($userBasics['pdsid'])?$userBasics['pdsid']:null;
        
        $username_u = str_replace(' ','',$userBasics['name']);
        if(User::where('username',$username_u)->first())
            {
               $username_u = $username_u.Str::random(5);
            }
        $user->username = $username_u;

        //$getAuthCode = Str::random(16);
        $getAuthCode = mt_rand(10, 999);

            
        $verifyAuthCodeFormat = $data->encode($user->email . '<:MP:>' . $getAuthCode, env('ENCRIPTION_KEY'));


        $user->password = Hash::make($userBasics['password']);
        $user->verify_token = $getAuthCode;

        if(is_numeric($userBasics['email'])){
            $otpCode = mt_rand(1111,9999);
            $user->verify_token_phone = $otpCode;
            $user->verify_status_phone = 0;
        }
        
        $user->verify_status = isset($userBasics['varify_status'])?1:0;
        $user->status = 0;

        $user->save();


        $base_url = config()->get('global.front_url');
        $link = $base_url.'verification-by-mail?token='.$verifyAuthCodeFormat;
        //$data = ['name'=>$user->name,'token'=>$verifyAuthCodeFormat,'link'=>$link];
        // $data = [
        //     'name'=>$user->name,
        //     'email'   => $user->email, 
        //     'token' => $verifyAuthCodeFormat,
        //     'link'    => $link
        // ];


        if(is_numeric($userBasics['email'])){
            $data = [
            'name'=>$user->name,
            'subject' => 'Action required: Activate your Muktopaath account',
            'short_name' => 'Muktopaath account',
            'to'   => $userBasics['email'],
            'link' => $link,
            'token' => $verifyAuthCodeFormat,
            'template' => 'verifyaccount',
            'message' => 'Your Muktopaath Account Verification Code is ' .$otpCode
        ];
            dispatch(new SendSmsJob($data));
        }else{
            $getAuthCode = mt_rand(10, 999);
            //$verifyAuthCodeFormat = $data->encode($user['email'] . '<:MP:>' . $getAuthCode, env('ENCRIPTION_KEY'));
            $link = $base_url.'verification-by-mail?token='.$verifyAuthCodeFormat;
            $data = [
            'name'=>$user->name,
            'subject' => 'Action required: Activate your Muktopaath account',
            'short_name' => 'Muktopaath account',
            'to'   => $userBasics['email'],
            'link' => $link,
            'token' => $verifyAuthCodeFormat,
            'template' => 'verifyaccount'
        ];
            Mail::to($data['to'])->send(new MailSender($data));
        }

            return $user;
        }

        public function resendcodeverify($request){

            $chk = ApplicationSetting::select('settings')
                    ->where('settings_for','token')->first();

            $data = json_decode($chk->settings);

            $user_m = User::where('email', '=', $request['user'])->first();
            $user_p = User::where('phone', '=', $request['user'])->first();

             if(!($user_m) && !($user_p)){
                return response()->json(['message' => 'No account found!'],400);
            }

            if(is_numeric($request['user'])){
                $data = [
                'name'=>$user_p['name'],
                'subject' => 'Action required: Activate your Muktopaath account',
                'short_name' => 'Muktopaath account',
                'to'   => $user_p['phone'],
                'template' => 'verifyaccount',
                'message' => 'Your Muktopaath Account Verification Code is ' .$user_p['verify_token_phone']
            ];
                dispatch(new SendSmsJob($data));
            }else{
                $data = [
                'name'=>$user_m['name'],
                'subject' => 'Action required: Activate your Muktopaath account',
                'short_name' => 'Muktopaath account',
                'to'   => $user_m['email'],
                'link' => $link,
                'token' => $verifyAuthCodeFormat,
                'template' => 'verifyaccount'
            ];
                Mail::to($data['to'])->send(new MailSender($data));
            }

        }

        public function tokenhistory($request){
            $data = new TokenHistory;
            $data['email_or_phone'] =  $request['email_or_phone'];
            $data->save();

            return 1;
        }

        public function passwordResetCode($request){

            $chk = ApplicationSetting::select('settings')
                    ->where('settings_for','token')->first();

            $data = json_decode($chk->settings);

            $history = TokenHistory::where('email_or_phone',$request['email'])
                                    ->orWhere('email_or_phone',$request['phone'])
                                    ->orderby('id','DESC')
                                    ->first();
            

            if($history){
                $count = TokenHistory::select(DB::raw('COUNT(id) as total'))
                                    ->whereDate('created_at',Carbon::today())
                                    ->where('email_or_phone',$request['email'])
                                    ->orWhere('email_or_phone',$request['phone'])
                                    ->value('total');

                if($count >= $data->max_try){
                    return response()->json(['message' => 'Your can not send any more request for today','lang' => 'no_more_token_request','type' => 100 ],400);
                }

                $duration =  (strtotime(Carbon::now()) - strtotime($history->created_at))/60;

                if($duration<$data->wait_for){
                    return response()->json(['message' => 'wait for '.($data->wait_for - floor($duration)).' minutes to resend','minute' => ($data->wait_for - floor($duration)), 'type' => 101],400);
                }
            }

            $otp = mt_rand(1111,9999);
            
            $user_m = User::where('email', '=', $request['email'])->first();
            $user_p = User::where('phone', '=', $request['phone'])->first();


            $otp_m_old = PasswordReset::where('email', '=', $request['email'])->first();
            $otp_p_old = PasswordReset::where('phone', '=', $request['phone'])->first();

            if(!($user_m) && !($user_p)){
                return response()->json(['message' => 'No account found!'],400);
            }

            if(!empty($request['email'])){
                $data = [
                    'subject' => 'Password Reset Request',
                    'short_name' => 'Muktopaath account',
                    'name'=>$user_m->name,
                    'to'   => $user_m->email, 
                    'template' => 'passwordreset',
                    'otp'    => $otp
                ];
                
                if($otp_m_old){
                    $password_reset = $otp_m_old;
                    $password_reset->email = $request['email'];
                    $password_reset->otp = $otp;
                
                    if($password_reset->update()){

                        Mail::to($data['to'])->send(new MailSender($data));

                        $dt['email_or_phone'] = $request['email'];
                        if($this->tokenhistory($dt)){
                                return response()->json([
                                'message' => 'An OTP code send your email address'
                            ]);
                        }
                    }
                    
                }else{
                    $password_reset = new PasswordReset();
                    $password_reset->email = $request['email'];
                    $password_reset->otp = $otp;
                
                    if($password_reset->save()){

                        Mail::to($data['to'])->send(new MailSender($data));

                        $dt['email_or_phone'] = $request['email'];
                        if($this->tokenhistory($dt)){
                                return response()->json([
                                'message' => 'An OTP code send your email address'
                            ]);
                        }
                        
                    }
                }
                
            }elseif(!empty($request['phone'])){
                $data = [
                    'to' => $user_p->phone,
                    'template' => 'verifyaccount',
                    'message' => 'Your OTP code to reset password is ' .$otp
                ];

                if($otp_p_old){
                    $password_reset = $otp_p_old;
                    $password_reset->phone = $request['phone'];
                    $password_reset->otp = $otp;
                
                    if($password_reset->update()){
                        // $message = 'Your OTP code to reset password. is ' .$otp;
                        // SMS::send($user_p->phone, $message);
                        dispatch(new SendSmsJob($data));

                        $dt['email_or_phone'] = $request['phone'];
                        if($this->tokenhistory($dt)){
                                return response()->json([
                                'message' => 'An OTP is send to your phone number'
                            ]);
                        }
                    }
                }else{
                    $password_reset = new PasswordReset();
                    $password_reset->phone = $request['phone'];
                    $password_reset->otp = $otp;
                
                    if($password_reset->save()){
                        dispatch(new SendSmsJob($data));

                        $dt['email_or_phone'] = $request['phone'];
                        if($this->tokenhistory($dt)){
                                return response()->json([
                                'message' => 'An OTP is send to your phone number'
                            ]);
                        }
                    }
                }
                
            }else{
                return response()->json(['message' => 'Something wrong'],400);
            }
        }

        public function passwordResetCodeVerify($request){

            $user_m = (isset($request['email']))?$request['email']:null;
            $user_p = (isset($request['phone']))?$request['phone']:null;

            $otp = (isset($request['otp']))?$request['otp']:null;

            //return response()->json($user_m);
            
            $user_m_verify = PasswordReset::where('email', '=', $user_m)->where('otp',$otp)->first();
            $user_p_verify = PasswordReset::where('phone', '=', $user_p)->where('otp',$otp)->first();
            if($user_m_verify or $user_p_verify){
                return response()->json([
                    'message' => 'your account is verified',
                ], 200);
            }else{
                return response()->json([
                    'message' => 'OTP did not match!',
                    'lang' => 'otp_not_match'
                ], 409);
            }
        } 

    public function verifyBySms($request){

            $user = User::where('phone', '=', $request['phone'])
            ->first();
            //return response()->json($user->verify_status_phone);
            if($user->verify_token_phone == $request['verify_token_phone']){
                if($user->verify_status_phone==0){
                
                    $user->verify_status_phone=1;
                    
                    if($user->update()){
                        $data['name'] = $user->name;
                        $data['email'] = $user->email;
                        $data['phone'] = $user->phone;
                        $data['type'] = $user->type;
                        $data['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
                        $userdata = $this->authuserinfoById($user->id);
                        $response = ['data' => $data,'user'=>$userdata];
                        
                        return response()->json([
                            'data' => $response,
                            'message' => 'Account successfully verified',
                            'verify_status' => '1'
                        ],201);
                    }else{
                        return response()->json([
                            'message' => 'Something went wrong!'
                        ], 400);
                    }
                }else{
                    return response()->json([
                     'msg' => 'Account already verified',
                     'lang' => 'already_verified',
                     'verify_status' => '1'], 403);
                }
            }else{
                return response()->json([
                 'msg' => 'OTP did not match',
                 'lang' => 'otp_not_match',
                 'verify_status' => '0'], 400);
            }
            
            
        }
        
        public function updatePassword($request){
            
            $otp = $request['otp'];
            $user_m_verify = PasswordReset::where('email', '=', $request['email'])->where('otp',$otp)->first();
            $user_p_verify = PasswordReset::where('phone', '=', $request['phone'])->where('otp',$otp)->first();
            
            if($user_m_verify){
                $user = User::where('email', '=', $request['email'])->first();
                $user->password = bcrypt($request['password']);
                
                if($user->update()){
                    return response()->json([
                        'message' => 'Successfully updated',
                        'data'   => $user
                    ],201);
                }else{
                    return response()->json([
                        'message' => 'Something went wrong!'
                    ], 400);
                }
                
            }elseif($user_p_verify){
                $user = User::where('phone', '=', $request['phone'])->first();
                $user->password = bcrypt($request['password']);
                
                if($user->update()){
                    return response()->json([
                        'message' => 'Successfully updated',
                        'data'   => $user
                    ],201);
                }else{
                    return response()->json([
                        'message' => 'Something went wrong!'
                    ], 400);
                }
            }else{
                return response()->json([
                    'message' => 'OTP did not match!'
                ], 409);
            }
        }

        public function verifyBymail($request)
        {
           
        $data = new ManualEncodeDecode();


        list($getUserEmail,$authToken) = explode('<:MP:>',$data->decode($request['token'], env('ENCRIPTION_KEY')));
            
            $getUserInfo = User::where('email', '=', $getUserEmail)
            ->first();


            
            if($getUserInfo && $getUserInfo->verify_status==0){
                    if($getUserInfo->verify_token == $authToken){
                        $getUserInfo->verify_status=1;

                        if($getUserInfo->update()){
                                
                                $sata['name'] = $getUserInfo->name;
                                $sata['email'] = $getUserInfo->email;
                                $sata['phone'] = $getUserInfo->phone;
                                $sata['type'] = $getUserInfo->type;
                                $sata['token'] = $getUserInfo->createToken('Laravel Password Grant Client')->accessToken;
                                $userdata = $this->authuserinfoById($getUserInfo->id);
                                $response = ['data' => $sata,'user'=>$userdata];
                                
                                return response()->json([
                                    'data' => $response,
                                    'message' => 'Account successfully verified',
                                    'verify_status' => '1'
                                ],201);
                        }
                    
                    }else{
                        return response()->json([
                            'message' => 'Something went wrong!'
                        ], 400);
                    }


            }elseif($getUserInfo && $getUserInfo->verify_status==1){
                $sata['name'] = $getUserInfo->name;
                $sata['email'] = $getUserInfo->email;
                $sata['phone'] = $getUserInfo->phone;
                $sata['type'] = $getUserInfo->type;
                $sata['token'] = $getUserInfo->createToken('Laravel Password Grant Client')->accessToken;
                $userdata = $this->authuserinfoById($getUserInfo->id);
                $response = ['data' => $sata,'user'=>$userdata];
                
                return response()->json([
                    'data' => $response,
                    'message' => 'Account successfully verified',
                    'verify_status' => '1'
                ],201);
                // return response()->json(['msg' => 'Account already verified', 'verify_status' => '1'], 200);
            }else{
                return response()->json(['msg' => 'Invalid user request'], 500);
            }
        
    }

    public function logtrack($user){
        //$user = $event->user;
        $dateTime = date('Y-m-d H:i:s');
        
        $user_array = [
            'id' => $user->id,
            'name' => $user->name ?? '',
            'designation' => $user->designation ?? '',
            'officeNameEng' => auth()->user()->officeNameEng ?? '',
            'officeNameBng' => auth()->user()->officeNameBng ?? ''
        ];
        $userInfo = json_encode($user_array);

        $data = [
            'ip'         => Request()->ip(),
            'user_agent' => Request()->userAgent()
        ];
        DB::table('logtrackers')->insert([
            'users'      => $userInfo, // Need to add this filed in database field type text/VARCHAR(250)
            'user_id'    => $user->id,
            'log_date'   => $dateTime,
            'table_name' => 'users',
            'log_type'   => 'login',
            'data'       => json_encode($data)
        ]);

        return 1;
    }

    public function users(){
        $data =  Request()->search;
        $user =[];
        if(config()->get('global.owner_id')==1){
            $user = User::orderBy('id','DESC')
            ->when(Request()->search, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('users.name','like','%'.$field.'%')
                            ->orWhere('users.email','like','%'.$field.'%');
                        });
                    })->paginate(10);

            return UserCol::collection($user);

          
        }else if(config()->get('global.owner_id')!==null){
            $user = CourseEnrollment::select('o.user_id','cb.id')
                    ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                    ->join('orders as o','o.id','course_enrollments.order_id')
                    ->where('cb.owner_id',config()->get('global.owner_id'))
                    ->paginate(10);

            return UserResource::collection($user);
            //$user = User::orderBy('id','DESC')->paginate(20);
        }

        return response()->json($user);
    }

    public function upload_profile_photo($request){

        $user = User::where('id',config()->get('global.user_id'))->first();
        $user->photo_id   =   $request['photo'];
        $user->update();

        $data = $this->authuserinfoById(config()->get('global.user_id'));

        return response()->json(['data' => $data, 'message' => 'User profile Photo updated']);
    }

    public function createUserDetails($userId, $userDetails){

        $userinfo = new UserInfo;

        $userinfo->created_by      = config()->get('global.user_id');
        $userinfo->updated_by      = config()->get('global.user_id');
        $userinfo->user_id         = $userId;
        $userinfo->gender          = isset($userDetails['gender'])?$userDetails['gender']:null;
        $userinfo->designation     = isset($userDetails['designation'])?$userDetails['designation']:null;


        if(isset($userDetails['education_level_id'])){
            $userinfo->education_level_id     = $userDetails['education_level_id']!=''?$userDetails['education_level_id']:0;
        }

        if(isset($userDetails['upazila_id'])){
            $userinfo->upazila_id     =       $userDetails['upazila_id']!=''?$userDetails['upazila_id']:0;
        }
        if(isset($userDetails['has_disability'])){
            if($userDetails['has_disability']==1){
                $userinfo->has_disability =   1;
                $userinfo->disability_type_id =   $userDetails['disability_type_id'];
            }
        }

        $userinfo->profession_id   = isset($userDetails['profession_id'])?$userDetails['profession_id']:null;
        $userinfo->profession_type   = isset($userDetails['profession_type'])?$userDetails['profession_type']:null;
        $userinfo->save();

        return $userinfo;
    }


    public function userInfo($path){
        
        $user = User::select('users.name','users.completeness','users.email','users.type')
                ->where('users.id',config()->get('global.user_id'))
                ->when($path, function ($query, $path) {
                        if($path[0]=='assessments'){
                            return $query->addSelect('assessments_roles.access','assessments_roles.role')->leftjoin('assessments_roles','assessments_roles.user_id','users.id')
                                ->where('assessments_roles.owner_id',0);
                        }
                        else if($path[0]=='contentbank'){
                            return $query->addSelect('content_bank_roles.access')->leftjoin('content_bank_roles','content_bank_roles.user_id','users.id')
                            ->where('content_bank_roles.owner_id',0);

                        }
                        else if($path[0]=='myaccount'){
                            return $query->addSelect('system_roles.access')->leftjoin('system_roles','system_roles.user_id','users.id')
                            ->where('system_roles.owner_id',0);

                        }
                        })
                        ->first();
                        
                if(!$user){
                    $user = User::select('users.name','users.email','users.completeness','users.type')
                        ->where('users.id',config()->get('global.user_id'))
                        ->first();
                }

                return $user;

    }

    public function authuserinfo(){
        $user = User::select('users.id','users.username','users.name','users.completeness','users.email','users.type','users.role_id')->with('roles','currentrole')->where('id',config()->get('global.user_id'))->first();
        if($user){
            return $user;
        }
        return $user;

    }

     public function authuserinfoById($id){

        $user = User::select('users.id','users.photo_id','user_infos.photo_name','users.name','users.completeness','users.username','users.email','users.type','users.role_id','users.gamification_point','users.old_user_points','users.pdsid')
            ->join('user_infos','user_infos.user_id','users.id')
            ->with('roles','currentrole','photo')->where('users.id',$id)->first();
        if($user){
            return $user;
        }
        return $user;

    }




    // public function deleteOrder($orderId) 
    // {
    //     Order::destroy($orderId);
    // }

    // public function createOrder(array $orderDetails) 
    // {
    //     return Order::create($orderDetails);
    // }

    // public function updateOrder($orderId, array $newDetails) 
    // {
    //     return Order::whereId($orderId)->update($newDetails);
    // }

    // public function getFulfilledOrders() 
    // {
    //     return Order::where('is_fulfilled', true);
    // }
}