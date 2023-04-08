<?php

namespace App\Http\Controllers\MyAccount;

use App\Http\Controllers\Controller;
use App\Interfaces\Myaccount\UserRepositoryInterface;
use App\Repositories\Myaccount\Validation\User as Uservalidation;
use App\Repositories\Validation;
use Illuminate\Http\Request;
Use App\Models\Myaccount\User;
Use App\Models\Myaccount\UserInfo;
Use App\Models\Myaccount\PasswordReset;
Use App\Models\Myaccount\InstitutionInfo;
Use App\Models\Myaccount\ContentBankRole;
Use App\Models\Myaccount\AssessmentsRole;
Use App\Models\Myaccount\MyaccountRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Validator;
use App\Http\Resources\MyAccount\User as UserResource;
use DB;
use Auth;

class MygovController extends Controller
{

    private  $userRepository;
    private $val;

    public function __construct(UserRepositoryInterface $userRepository, Validation $val) 
    {
        $this->userRepository = $userRepository;
        $this->val = $val;
    }

  
    public function login(Request $request)
    {  
        $rdata = $request->all();
        if($rdata['mobile']){
            $mobile = $rdata['mobile'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need mobile','code'=>02],200);
        }
        if($rdata['password']){
            $password = $rdata['password'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need Password','code'=>02],200);
        }
       
        $TokenCreate = array("client_id"=>"nwzFcJD2Rv2hK7agoyyj","api_key"=>"suxRep82Li.QQUpedIAn7");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://idp-api.live.mygov.bd/token/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $TokenCreate,
            CURLOPT_HTTPHEADER => array(
            ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $resultdata = json_decode($result,true);
        if($resultdata && $resultdata['status']=='success'){
            $ssologin = array("token"=>$resultdata['token'],"password"=>$password,"mobile"=>$mobile);
            $curllogin = curl_init();

            curl_setopt_array($curllogin, array(
                CURLOPT_URL => "https://idp-api.live.mygov.bd/user/login",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $ssologin,
                CURLOPT_HTTPHEADER => array(
                ),
            ));
            $resultlogin = curl_exec($curllogin);
            curl_close($curllogin);
            $resultlogindata = json_decode($resultlogin,true);
            
            if($resultlogindata && $resultlogindata['status']=='success'){
              
                if(isset($resultlogindata['data']['mobile'])){
                    // $user = 'asd';
                    $user = User::where('phone', $resultlogindata['data']['mobile'])->first();
                }else{
                    $user = null;
                }
                // return response()->json(['status'=>'error','message'=>$user,'code'=>2],500);
                    
                
                if($user){
                    
                    $user['token'] =  $user->createToken('Laravel Password Grant Client')->accessToken;
                    $userdata = $this->userRepository->authuserinfoById($user->id);
                    $response = ['data' => $user,'user'=>$userdata];
                    return response($response, 200);
                    
                }else{
                    
                    $passChar = Str::random(8);
                    //$passChar = 12345678;

                    $new_user = new User;
                    if($resultlogindata['data']['name_en']==''){
                        $new_user->name             = $resultlogindata['data']['name'];
                    }else{
                        $new_user->name             = $resultlogindata['data']['name_en'];
                    }
                    $new_user->bn_name          = $resultlogindata['data']['name'];
                    $new_user->email            = $resultlogindata['data']['email'];
                    $new_user->phone            = $resultlogindata['data']['mobile'];
                    $new_user->password         = bcrypt($passChar);
                    $new_user->verify_status    = 1;
                    $new_user->verify_status_phone  = 1;
                    $new_user->completeness     = 10;
                    $new_user->username         = str_replace(' ','',$resultlogindata['data']['name'].substr(md5(rand()), 0, 7));

                    if($new_user->save()){

                        $user_infos = new UserInfo;

                        $user_infos->user_id                = $new_user->id;
                        $user_infos->mother_name_bn            = $resultlogindata['data']['mother_name'];
                        $user_infos->mother_name         = $resultlogindata['data']['mother_name_en'];
                        $user_infos->father_name_bn            = $resultlogindata['data']['father_name'];
                        $user_infos->father_name         = $resultlogindata['data']['father_name_en'];
                        $user_infos->spouse_name_bn         = $resultlogindata['data']['spouse_name'];
                        
                        if($resultlogindata['data']['gender']=='Male'){
                            $user_infos->gender=1;
                        }elseif($resultlogindata['data']['gender']=='Female'){
                            $user_infos->gender=2;
                        }
                        
                        $user_infos->spouse_name         = $resultlogindata['data']['spouse_name_en'];
                        $user_infos->dob                    = $resultlogindata['data']['date_of_birth'];
                        $user_infos->social                 = '[{"type":"1","link":null}]';
                        $user_infos->area_of_experiences    = '[{"title":""}]';

                        $user_infos->save();
                        
                        $data['name'] = $new_user->name;
                        $data['email'] = $new_user->email;
                        $data['type'] = $new_user->type;
                        $data['token'] = $new_user->createToken('Laravel Password Grant Client')->accessToken;
                        $userdata = $this->userRepository->authuserinfoById($new_user->id);
                        $response = ['data' => $data,'user'=>$userdata];
                        return response($response, 200);
                        
                    }
                }
            }else{
                return $resultlogindata;
                
            }
        }else{
             return response()->json(['status'=>'error','message'=>'Server Error','code'=>2],200);
        }
 
    }
    
    public function forgetpassword(Request $request)
    {  
        $rdata = $request->all();
        if($rdata['mobile']){
            $mobile = $rdata['mobile'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need mobile','code'=>02],200);
        }
        if($rdata['password']){
            $password = $rdata['password'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need Password','code'=>02],200);
        }
        if($rdata['confirm_password'] && $rdata['password'] == $rdata['confirm_password']){
            
        }else{
            return response()->json(['status'=>'error','message'=>'Confirm password not match','code'=>02],200);
        }
        
        $password_reset = PasswordReset::where('phone',$mobile)->first();
        if($password_reset){
            
        }else{
            return response()->json(['status'=>'error','message'=>'Otp not match','code'=>02],200);
        }
        
        $TokenCreate = array("client_id"=>"nwzFcJD2Rv2hK7agoyyj","api_key"=>"suxRep82Li.QQUpedIAn7");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://idp-api.live.mygov.bd/token/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $TokenCreate,
            CURLOPT_HTTPHEADER => array(
            ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $resultdata = json_decode($result,true);
        if($resultdata && $resultdata['status']=='success'){
            $ssologin = array("token"=>$resultdata['token'],"password"=>$password,"mobile"=>$mobile);
            $curllogin = curl_init();

            curl_setopt_array($curllogin, array(
                CURLOPT_URL => "https://idp-api.live.mygov.bd/user/resetPassword",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $ssologin,
                CURLOPT_HTTPHEADER => array(
                ),
            ));
            $resultlogin = curl_exec($curllogin);
            curl_close($curllogin);
           $resultlogindata = json_decode($resultlogin,true);
            if($resultlogindata && $resultlogindata['status']=='success'){

                
                $user = User::Where('phone',$mobile)->first();
                
                if($user){
                    
                    $user['token'] =  $user->createToken('Laravel Password Grant Client')->accessToken;
                    $userdata = $this->userRepository->authuserinfoById($user->id);
                    $response = ['data' => $user,'user'=>$userdata];
                    return response($response, 200);
                    
                }else{
                    return response()->json(['status'=>'error','message'=>'User not found','code'=>02],200);
                }
            }else{
                return $resultlogindata;
                
            }
        }else{
             return response()->json(['status'=>'error','message'=>'Server Error','code'=>02],200);
        }
 
    }
   
    public function register(Request $request)
    {  
        $rdata = $request->all();
        if($rdata['mobile']){
            $mobile = $rdata['mobile'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need mobile','code'=>02],200);
        }
        if($rdata['otp']){
            $otp = $rdata['otp'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need otp','code'=>02],200);
        }
        if($rdata['password']){
            $password = $rdata['password'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need mobile','code'=>02],200);
        }
        if($rdata['confirm_password'] && $rdata['password'] == $rdata['confirm_password']){
            
        }else{
            return response()->json(['status'=>'error','message'=>'Confirm password not match','code'=>02],200);
        }
        if($rdata['name']){
            $name = $rdata['name'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need name','code'=>02],200);
        }
        if($rdata['email']){
            $email = $rdata['email'];
        }else{
            $email = "";
        }
        $password_reset = PasswordReset::where('phone',$mobile)->first();
        if($password_reset){
            
        }else{
            return response()->json(['status'=>'error','message'=>'Otp not match','code'=>02],200);
        }
        $TokenCreate = array("client_id"=>"nwzFcJD2Rv2hK7agoyyj","api_key"=>"suxRep82Li.QQUpedIAn7");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://idp-api.live.mygov.bd/token/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $TokenCreate,
            CURLOPT_HTTPHEADER => array(
            ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $resultdata = json_decode($result,true);
        if($resultdata && $resultdata['status']=='success'){
            $ssologin = array("token"=>$resultdata['token'],"password"=>$password,"mobile"=>$mobile,"name"=>$name,"email"=>$email);
            $curllogin = curl_init();

            curl_setopt_array($curllogin, array(
                CURLOPT_URL => "https://idp-api.live.mygov.bd/user/create",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $ssologin,
                CURLOPT_HTTPHEADER => array(
                ),
            ));
            $resultlogin = curl_exec($curllogin);
            curl_close($curllogin);
            $resultlogindata = json_decode($resultlogin,true);
            if($resultlogindata && $resultlogindata['status']=='success'){

                if((isset($resultlogindata['data']['email']) && isset($resultlogindata['data']['mobile'])) && ($resultlogindata['data']['email']!=null || $resultlogindata['data']['mobile']!=null)){
                    $user = User::where('email', $resultlogindata['data']['email'])->orWhere('phone', $resultlogindata['data']['mobile'])->first();
                }else{
                    $user = '';
                }
                    
                
                if($user){
                    
                    $user['token'] =  $user->createToken('Laravel Password Grant Client')->accessToken;
                    $userdata = $this->userRepository->authuserinfoById($user->id);
                    $response = ['data' => $user,'user'=>$userdata];
                    return response($response, 200);
                    
                }else{
                    
                    $passChar = Str::random(8);
                    //$passChar = 12345678;

                    $new_user = new User;
                    if($resultlogindata['data']['name_en']==''){
                        $new_user->name             = $resultlogindata['data']['name'];
                    }else{
                        $new_user->name             = $resultlogindata['data']['name_en'];
                    }
                    $new_user->name_bn          = $resultlogindata['data']['name'];
                    $new_user->email            = $resultlogindata['data']['email'];
                    $new_user->phone            = $resultlogindata['data']['mobile'];
                    $new_user->password         = bcrypt($passChar);
                    $new_user->verify_status    = 1;
                    $new_user->verify_status_phone  = 1;
                    $new_user->completeness     = 10;
                    $new_user->username         = str_replace(' ','',$resultlogindata['data']['name'].substr(md5(rand()), 0, 7));

                    if($new_user->save()){

                        $user_infos = new UserInfo;

                        $user_infos->user_id                = $new_user->id;
                        $user_infos->mother_name_bn            = $resultlogindata['data']['mother_name'];
                        $user_infos->mother_name         = $resultlogindata['data']['mother_name_en'];
                        $user_infos->father_name_bn            = $resultlogindata['data']['father_name'];
                        $user_infos->father_name         = $resultlogindata['data']['father_name_en'];
                        $user_infos->spouse_name_bn         = $resultlogindata['data']['spouse_name'];
                        
                        if($resultlogindata['data']['gender']=='Male'){
                            $user_infos->gender=1;
                        }elseif($resultlogindata['data']['gender']=='Female'){
                            $user_infos->gender=2;
                        }
                        
                        $user_infos->spouse_name         = $resultlogindata['data']['spouse_name_en'];
                        $user_infos->dob                    = $resultlogindata['data']['date_of_birth'];
                        $user_infos->social                 = '[{"type":"1","link":null}]';
                        $user_infos->area_of_experiences    = '[{"title":""}]';

                        $user_infos->save();
                        
                        $data['name'] = $new_user->name;
                        $data['email'] = $new_user->email;
                        $data['type'] = $new_user->type;
                        $data['token'] = $new_user->createToken('Laravel Password Grant Client')->accessToken;
                        $userdata = $this->userRepository->authuserinfoById($new_user->id);
                        $response = ['data' => $data,'user'=>$userdata];
                        return response($response, 200);
                        
                    }
                }
            }else{
                return $resultlogindata;
                
            }
        }else{
             return response()->json(['status'=>'error','message'=>'Server Error','code'=>02],200);
        }
 
    }
    public function sendOtp(Request $request)
    {  
        $rdata = $request->all();
        if($rdata['mobile']){
            $mobile = $rdata['mobile'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need mobile','code'=>02],200);
        }
        if($rdata['password']){
            $password = $rdata['password'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need password','code'=>02],200);
        }
        
        if($rdata['confirm_password'] && $rdata['password'] == $rdata['confirm_password']){
            
        }else{
            return response()->json(['status'=>'error','message'=>'Confirm password not match','code'=>02],200);
        }
        if($rdata['from']==1){
          
            if($rdata['name']){
                $name = $rdata['name'];
            }else{
                return response()->json(['status'=>'error','message'=>'Need name','code'=>02],200);
            }
        }
        
        
        $rdata = $request->all();

        if($rdata['mobile']){
            $mobile = $rdata['mobile'];
        }else{
            return response()->json(['status'=>'error','message'=>'Need Mobile','code'=>02],200);
        }
        
        $otp = rand(1000,9999);
        $password_reset = PasswordReset::where('phone',$mobile)->first();
        if($password_reset){
            
        }else{
            $password_reset = new PasswordReset();
        }
        
        $password_reset->phone = $mobile;
        $password_reset->otp = $otp;
        $password_reset->save();
       
        $TokenCreate = array("client_id"=>"nwzFcJD2Rv2hK7agoyyj","api_key"=>"suxRep82Li.QQUpedIAn7");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://idp-api.live.mygov.bd/token/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $TokenCreate,
            CURLOPT_HTTPHEADER => array(
            ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        
        $resultdata = json_decode($result,true);
        if($resultdata && $resultdata['status']=='success'){
            $ssologin = array("token"=>$resultdata['token'],"otp"=>$otp,"mobile"=>$mobile);
            $curllogin = curl_init();

            curl_setopt_array($curllogin, array(
                CURLOPT_URL => "https://idp-api.live.mygov.bd/user/sendOtp",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $ssologin,
                CURLOPT_HTTPHEADER => array(
                ),
            ));
            $resultlogin = curl_exec($curllogin);
            curl_close($curllogin);
            $resultlogindata = json_decode($resultlogin,true);
            if($resultlogindata && $resultlogindata['status']=='success'){
               return response()->json(['status'=>'success','otp'=>'','code'=>02],200);
            }else{
                return $resultlogindata;
                
            }
        }else{
             return response()->json(['status'=>'error','message'=>'Server Error','code'=>02],200);
        }
 
    }

   


}