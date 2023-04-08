<?php

namespace App\Http\Controllers\MyAccount;

use App\Mail\MailSender;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;
use App\Http\Controllers\Controller;
use App\Interfaces\Myaccount\UserRepositoryInterface;
use App\Interfaces\Myaccount\InstitutionRepositoryInterface;
use App\Repositories\Myaccount\Validation\User as Uservalidation;
use App\Repositories\Validation;
use Illuminate\Http\Request;
Use App\Models\Myaccount\User;
Use App\Models\Myaccount\UserInfo;
Use App\Models\Myaccount\InstitutionInfo;
Use App\Models\Myaccount\ContentBankRole;
Use App\Models\Myaccount\AssessmentsRole;
Use App\Models\Myaccount\SystemRole;
Use App\Models\Myaccount\MyaccountRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Validator;
use App\Http\Resources\MyAccount\User as UserResource;
use DB;
use Auth;
use Illuminate\Support\Facades\Mail;
use App\Lib\ManualEncodeDecode;
use App\Lib\SMS;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Lib\GamificationTrait;
use App\Traits\UpdateRedisKey;
// use App\Traits\DeleteRedisKey;
use Barryvdh\DomPDF\PDF;

class UserController extends Controller
{ 
    use UpdateRedisKey;
    // use DeleteRedisKey;
    use GamificationTrait;
    private  $userRepository;
    private  $institutionRepository;
    private $val;
    private $teacher;

    public function __construct(UserRepositoryInterface $userRepository,InstitutionRepositoryInterface $institutionRepository, Validation $val) 
    {
        $this->userRepository = $userRepository;
        $this->institutionRepository = $institutionRepository;
        $this->val = $val;
    }

    public function search(Request $request){

        $messsages = array(
            'required'=>'e_required',
            'param' => 'param'
        );

        $rules = array(
            'param'      => 'required',
        );

        $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }

        if($request->param){
            $res = User::select('id','name','email','phone')
            ->where('email',$request->param)
            ->orwhere('phone',$request->param)
            ->first();
            if($res){
                return response()->json([
                    'data'=>$res,
                    'code' => 1
                ]);
            }else{
                return response()->json([
                    'data'=>'user not found',
                    'code' => 0
                ]);
            }
        }
    }

    public function sociallogin(Request $request){

        $user = User::where('email',$request['email'])->first();
        if($user){
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['type'] = $user->type;
            $data['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
            
            $response = ['data' => $data];
            $this->gamificationStore('login',$user->id,$user->id,1);
            if($this->userRepository->logtrack($user)==1){
                return response($response, 200);
            }
        }else{
            $socio = [];
            $socio['name'] = $request['name'];
            $socio['password'] = '';
            $socio['email'] = $request['email'];
            $socio['verify_status'] = 1;
            $user = $this->userRepository->createUserBasics($socio);

            $user_id = $user->id;
            
            $socioDetails = [];
            $socioDetails['has_disability'] = 0;
            $socioDetails['disability_type_id'] = null;

            $this->userRepository->createUserDetails($user_id, $socioDetails);

            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['type'] = $user->type;
            $data['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
            
            $response = ['data' => $data];
            $this->gamificationStore('login',$user->id,$user->id,1);
            if($this->userRepository->logtrack($user)==1){
                return response($response, 200);
            }
        }

    }

    public function shift_to_training_module(){
        
        $enc = $this->encode(config()->get('global.user_id').'_'.date("Y/m/d h:i"),'a@@@tyuG678!!');

        return $enc;
    }

    public function encode($string,$key) {
        $key = sha1($key);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        $j=0; $hash = '';
        for ($i = 0; $i < $strLen; $i++) {
            $ordStr = ord(substr($string,$i,1));
            if ($j == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key,$j,1));
            $j++;
            $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
         }   
        return $hash;
    }


    public function teacherlogin(){

        $ch = curl_init();



    curl_setopt($ch, CURLOPT_URL, 'http://103.69.149.41/sso/Services/Security/PublicUser/MerchantSignIn');
        
        $body = [
            'username' => 'MyGov',
            'password' => '1234567856'
        ];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $loginData = curl_exec($ch);

        $exec = json_decode($loginData);

        $ch = curl_init();


    curl_setopt($ch, CURLOPT_URL, 'http://103.69.149.41/sso/Services/Security/PublicUser/GetMpoInfo');
        
        $body = [
            'merchantid' => '000003',
            'token' => $exec->_token,
            'pdsid' => Request()->imei_no
        ];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $loginData = curl_exec($ch);

        return json_decode($loginData);


    }

    public function updateUserinfo(Request $request){

            $rules = array(
                'name'               => 'required',
                'profession_id'      => 'required',
                'newpw'              => 'min:6',
            );

            if(is_numeric($request->get('email'))){
                $request['phone']  = $request->email;
                $rules['phone'] = 'required|min:11';
            }
            elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
                $rules['email'] = 'required|email';
            }else{
                $rules['email'] = 'required|email';
            }
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $user = User::Select('id')->where('id',$request['id'])->first();
        $user->name = $request['name'];
        $user->email = $request['email'];
        if($request->has('newpw')){
            if($request['newpw']!==''){
                $user->password = bcrypt($request['newpw']);
            }
        }

        $user->update();

        $ui = UserInfo::where('user_id',$request['id'])->first();
        $ui->profession_id = $request['profession_id'];
        $ui->gender = $request['gender'];
        $ui->update();

        
        return response()->json(['message' => 'Updated successfully']);
    }

    public function sendPasswordResetCode(Request $request){
        return $this->userRepository->passwordResetCode($request->all());
    }

    public function resendcodeverify(Request $request){
        return $this->userRepository->resendcodeverify($request->all());
    }

    public function passwordResetCodeVerify(Request $request){
        return $this->userRepository->passwordResetCodeVerify($request->all());
    }

    public function passwordReset(Request $request){
        
            $rules = array(
                'password'              => 'required|confirmed|min:6',
                'password_confirmation' => 'required',
            );
            
            if($this->val->validateRequest($rules)){
                return $this->val->validateRequest($rules);
            }
            
            return $this->userRepository->updatePassword($request->all());
            
      
        
    }

    public function showinfo($id){
        $res = User::select('users.phone','users.id as id','users.name','users.email','ui.profession_id','ui.gender','users.password')->leftjoin('user_infos as ui','ui.user_id','users.id')
        ->where('users.id',$id)
        ->first();

        return response()->json($res);
    }

    public function search_list(Request $request){
        
        $ownerID = config()->get('global.owner_id'); 
    
            $messsages = array(
                'required'=>'e_required',
                'param' => 'param'
            );
    
            $rules = array(
                'param'      => 'required',
            );
    
            $validator = Validator::make($request->all(),$rules,$messsages);
    
           if($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
            }
            if($ownerID == 1){
                if($request->param){
                    $res = User::select('id','name','email','phone')
                        ->where('email','like','%'.$request->param.'%')
                        ->orwhere('phone','like','%'.$request->param.'%')
                        ->take(5)->get();

                        $redis_data_add = $this->add_data($request, $res);

                    if($res){
                        return response()->json([
                            'data'=>$res,
                            'code' => 1
                        ]);
                    }else{
                        return response()->json([
                            'data'=>'user not found',
                            'code' => 0
                        ]);
                    }
                }
            }
            else{
                $assessment = config()->get('database.connections.assessment.database');
                $myaccount = config()->get('database.connections.my-account.database');
    
                if($request->param){
                    $res = DB::table($myaccount.'.users as u')
                        ->select('u.id','u.name','u.email','u.phone')
                        ->join($assessment.'.orders as o','u.id','=','o.user_id')
                        ->join($assessment.'.course_enrollments as ce','o.id','=','ce.order_id')
                        ->join($assessment.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                        ->where('cb.owner_id','=', $ownerID)
                        ->where('u.email','like','%'.$request->param.'%')
                        ->orwhere('u.phone','like','%'.$request->param.'%')
                        ->take(5)
                        ->get();

                        $redis_data_add = $this->add_data($request, $res);
    
                    if($res){
                        return response()->json([
                            'data'=>$res,
                            'code' => 1
                        ]);
                    }else{
                        return response()->json([
                            'data'=>'user not found',
                            'code' => 0
                        ]);
                    }
                }
            }

    
        }


    public function index(Request $request){

        $data = $this->userRepository->users();
        
        // $key = config()->get('global.redis_key');
        // $redisKey = $this->get_data($request, $data, $key);
        
        return $this->userRepository->users();
    }

    public function show($id){ 
        $res = User::join('user_infos as ui','ui.user_id','users.id')
                ->where('users.id',$id)
                ->first();
            return response()->json($res);
    }

    public function approve(Request $request,$id){

        $data = User::find($id);
        if($request->type=='email'){
            $data->verify_status = 1;
        }else if($request->type=='phone'){
            $data->verify_status_phone = 1;
        }
        $data->status = 1;
        $data->update();

     return response()->json(['message'=> 'user approved']);

    }

    public function block(Request $request,$id){
        $data = User::find($id);
        $data->status = 2;
        $data->update();

     return response()->json(['message'=> 'user approved']);

    }

    public function delete($id){
        $data = User::find($id);
        $data->delete();
        return response()->json(['message'=> 'deleted successfully']);
    }

    public function profile_info(){
        // return 1;
        $res = User::join('user_infos as ui','ui.user_id','users.id')
                ->where('users.id',config()->get('global.user_id'))
                ->first();


        $res['area_of_experiences'] = json_decode($res->area_of_experiences);
        $res['social'] = json_decode($res->social);
        $res['attachments'] = json_decode($res->attachments);
        $res['area_of_educations'] = json_decode($res->area_of_educations);
        $res['licence_of_certificate'] = json_decode($res->licence_of_certificate);
        $res['skills'] = json_decode($res->skills);
        $res['reference'] = json_decode($res->reference);
        
        return response()->json($res);
    }

    public function download($id){
        $data = UserInfo::find($id);
        // $data = [
        //     'title'=> 'pdf download',
        //     'name'=> 'shanto'
        // ];
        // return $data;
        $pdf = LaravelMpdf::loadView('pdf.profile_info',$data);
        // return $pdf;
        return $pdf->download('profile_info.pdf');
        // return $data;

        // $pdf = PDF::loadView('pdf.profile_info',$data);
        // return $pdf->download('profile_info.pdf');
    }

    public function partner_register(Request $request){
        
            $rules = array(
                'name'                  => 'required',
                'profession_id'         => 'required',
                'gender'                => 'required',
                'password'              => 'required|confirmed|min:6',
                'password_confirmation' => 'required',
                'partner_type' => 'required',
                'institution_name' => 'required',
                'username' => 'required|unique:institution_infos',
            );

            if(is_numeric($request->get('email'))){
                $request['phone']  = $request->email;
                $rules['phone'] = 'required|unique:users|min:11';
            }
            elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
                $rules['email'] = 'required|email|unique:users';
            }else{
                $rules['email'] = 'required|email';
            }

            if($request->partner_type=='institution'){
                $rules['contact_person'] = 'required';
                $rules['ins_phone'] = 'required';
                $rules['ins_email'] = 'required';
                $rules['contact_person_email'] = 'required|email';
                $rules['institution_type_id'] = 'required';
            }

            
            if($this->val->validateRequest($rules)){
                return $this->val->validateRequest($rules);
            }

            $data = [];
            $data['name'] = $request['name'];
            $data['profession_id'] = $request['profession_id'];
            $data['gender'] = $request['gender'];
            if($request->has_disability==1){
                $data['has_disability'] =   1;
                $data['disability_type_id']  =   $request->disability_id;
            }else{
                $data['has_disability'] = 0;
            }
            $data['email'] = $request['email'];
            $data['password'] = $request['password'];
            $data['password_confirmation'] = $request['password_confirmation'];
            $data['designation'] = $request['designation'];
            $data['education_level_id'] = $request['education_level_id'];
            $data['upazila_id'] = $request['upazila_id'];
            //return $data;
            DB::beginTransaction();

            try {
                
                $user = $this->userRepository->createUserBasics($data);

                $user_id = $user->id;

                $this->userRepository->createUserDetails($user_id, $data);

                $this->institutionRepository->create_partner($request->all(),$user_id);

                DB::commit();
            }
                catch (\Exception $e) {
                 DB::rollback();
                 return response()->json(['message' => 'something wrong'],400);
                // something went wrong
            }




            return response()->json(['message' => 'Partner request has been sent to super admin']);

    }

    public function pc($user,$UserInfo){
        $profilecompleteness=0;
        if($user->name!=null){
         $profilecompleteness = $profilecompleteness+20;   
        }
        if($user->phone!=null){
            $profilecompleteness = $profilecompleteness+10;  
        }
        if($user->email!=null){
            $profilecompleteness = $profilecompleteness+10;  
        }
        if($UserInfo->nid!=null){
            $profilecompleteness = $profilecompleteness+10;  
        }
        if($UserInfo->upazila_id!=null){
            $profilecompleteness = $profilecompleteness+10;  
        }
        if($UserInfo->dob!=null){
            $profilecompleteness = $profilecompleteness+10; 
        }
        if($UserInfo->degree_id!=null){

            $profilecompleteness = $profilecompleteness+10;  
        }
        if($UserInfo->education_level_id!=null){
            $profilecompleteness = $profilecompleteness+10;  
        }
        
        if($UserInfo->gender!=null){
            $profilecompleteness = $profilecompleteness+10;  
        }
        return $profilecompleteness;
    }


    public function store(Request $request){
         $data = $request->all();

       
        if(isset($request['phone'])){
            $this->validate($request,[
                'name'             => 'required',
                'phone'            =>'required|min:11|unique:users',
                'password'         => 'required|min:8',
                'confirm_password' => 'required|same:password',
            ]);
        }
        else if(isset($request['email'])){
            $this->validate($request,[
                'name'             => 'required',
                'email'            => 'required|email|unique:users',
                'password'         => 'required|min:8',
                'confirm_password' => 'required|same:password',
            ]);
        }else{
            $this->validate($request,[
                'name' => 'required',
                'email'=> 'required|email|unique:users',
                'password' => 'required|min:8',
                'confirm_password' => 'required|same:password',
            ]);
        }
        // $validator = Validator::make($request->all(),$rules,$messsages);

        // if($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 400);
        // }
        DB::beginTransaction();

        try{
            $user = New User();
        
        if(isset($data['email'])){

            $user->name = $data['name'];
            $user->email = $data['email'];
            $username_u = str_replace(' ','',$data['name']);
            if(User::where('username',$username_u)->first())
            {
               $username_u = $username_u.Str::random(5);
            }
            $user->username = $username_u;
            $user->verify_token = Str::random(40);
            $user->verify_status = 3;
            $user->status = 1;
            $user->password = bcrypt($data['password']);
            $type =1;
        }else
        {
            $user->name = $data['name'];
            $user->phone = $data['phone'];
            $username_u = str_replace(' ','',$data['name']);
            if(User::where('username',$username_u)->first())
            {
               $username_u = $username_u.Str::random(5);
            }
            $user->username = $username_u;
            $user->verify_token_phone   = rand(10000,99999);
            $user->verify_status_phone   = 3;
            $user->status = 1;
            $user->password = bcrypt($data['password']);
            $type =3;
        }

        $user->save();
        $user_id = $user->id;
        
        $UserInfo = new UserInfo;
        $UserInfo->social          = '[{"type":0,"link":null}]';
        $UserInfo->created_by    = 47;
        $UserInfo->updated_by    = 1;
        $UserInfo->user_id       = $user_id;
        $UserInfo->save();
        $this->gamificationStore('reg',$user_id,$user_id,1);
        return response()->json(['success' =>'User saved successfully'], 200);

        }catch (\Exception $e) {
            DB::rollback();
        return response()->json(['error' =>'something went wrong'], 500);

    // something went wrong
        }
        
        //Verify::dispatch($user,$type);
    }

    public function update(Request $request){

        // return $request->certificate_name;
        $messsages = array(
            'required'=>'e_required',
            'min'     => 'e_min_8',
            'unique'  => 'e_unique',
            'email'   => 'e_email',
            'same'    => 'same',
            'mismatch' => 'e_password_confirm',
            'nouser' => 'nouser',
            'newpassword' => 'newpassword',
            'oldpassword' => 'oldpassword',
            'confirmed' => 'e_password_confirm',
            'newpassword_confirmation' => 'newpassword_confirmation'
        );
        if($request->slug == 'pdsid'){
            $u = User::where('id',config()->get('global.user_id'))->first();
            $user->pdsid = isset($request->pdsid)?$request->pdsid:null;
            $u->Save();
        }
        
        if($request->slug == 'myprofile'){

            $this->validate($request,[
                'name' => 'required',
            ]);

            $u = User::where('id',config()->get('global.user_id'))->first();
            $u->name = $request->name;
            $u->certificate_name = $request->certificate_name;
            $u->Save();

            $ui= UserInfo::where('user_id',config()->get('global.user_id'))->first();
            // $ui->certificate_name = $request->certificate_name;
            // $ui->certificate_name = $request->certificate_name;
            
            $ui->education_level_id = $request->education_level_id;
            
            $ui->upazila_id = $request->upazila_id;
            $ui->degree_id = $request->degree_id;
            $ui->disability_type_id = $request->disability_type_id;
            $ui->dob = $request->dob;
            $ui->gender = $request->gender;
            $ui->nid = $request->nid;
            $ui->father_name = $request->father_name;
            $ui->mother_name = $request->mother_name;

            $ui->save();

            $profilecompleteness = $this->pc($u,$ui);
            $u->completeness=$profilecompleteness;
            if($profilecompleteness){
                $this->gamificationStore('pu',$u->id,$u->id,1);
            }
            $u->save();
            
        } else if ($request->slug == 'other-info') {
            $this->validate($request,[
                'name' => 'required',
            ]);

            $u = User::where('id',config()->get('global.user_id'))->first();
            $u->name = $request->name;
            $u->email = $request->email;
            $u->phone = $request->phone;
            $u->Save();

            $ui= UserInfo::where('user_id',config()->get('global.user_id'))->first();
            // $ui->certificate_name = $request->certificate_name;
            $ui->education_level_id = $request->education_level_id;
            $ui->disability_type_id = $request->disability_type_id;
            $ui->upazila_id = $request->upazila_id;
            $ui->degree_id = $request->degree_id;
            $ui->dob = $request->dob;
            $ui->nid = $request->nid;
            $ui->father_name = $request->father_name;
            $ui->mother_name = $request->mother_name;

            $ui->address = $request->address;
            $ui->about = $request->about;
            $ui->area_of_educations = json_encode($request->area_of_educations);
            $ui->area_of_experiences = json_encode($request->area_of_experiences);
            $ui->licence_of_certificate = json_encode($request->licence_of_certificate);
            $ui->reference = json_encode($request->reference);
            $ui->skills = json_encode($request->skills);
            $ui->social = json_encode($request->social);

            $ui->save();
            $profilecompleteness = $this->pc($u,$ui);
            $u->completeness=$profilecompleteness;

            if($profilecompleteness){
                $this->gamificationStore('pu',$u->id,$u->id,1);
            }
            $u->save();

            $redis_data_delete = $this->delete_key($request);


            return response()->json($u);
            
        } else if ($request->slug == 'account-setting'){
            $u = User::where('id',config()->get('global.user_id'))->first();
            
            if($request->has('reqtype') && $request->reqtype=='pw'){
                $rules = array(
                    'newpassword'      => 'required|confirmed|min:6',
                    'oldpassword'      => 'required',
                    'newpassword_confirmation' => 'required'
                );
                if($request->newpassword && $request->oldpassword){

                    if(Hash::check($request->oldpassword, $u->password)){
                        $u->password = Hash::make($request->newpassword);
                    }else{
                        return response()->json(['errors' => ['mismatch' => ['e_password_confirm']],'type'=>1], 400);
                    }
                }
                
            }else{
                $rules = array(
                    'email'      => 'required|email'
                );
                $chk = User::select('email','phone','username')->where('id',config()->get('global.user_id'))->first();

                if($chk->email!==$request->email){
                    $rules['email'] = 'email|unique:users';
                }else{
                    $rules['email'] = 'email';
                }

                if($chk->phone!==$request->phone && !$request->phone){
                    $rules['phone'] = 'regex:/(01)[0-9]{9}/|unique:users';
                }else if($request->phone){
                    $rules['phone'] = 'regex:/(01)[0-9]{9}/';
                }

                if($chk->username!==$request->username){
                    $rules['username'] = 'required|unique:users';
                }else{
                    $rules['username'] = 'required';
                }


                $validator = Validator::make($request->all(),$rules,$messsages);

                if($validator->fails()) {
                    return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
                }
                
                
                $u->username = $request->username;
                $u->phone = $request->phone;
                $u->email = $request->email;
            }

            $validator = Validator::make($request->all(),$rules,$messsages);

            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
            }
            
            $u->save();
        }else if($request->slug == 'other-info'){
            $ui = UserInfo::where('user_id',config()->get('global.user_id'))->first();
            $ui->address = $request->address;
            $ui->social = json_encode($request->social);
            $ui->education_level_id = $request->education_level_id;
            $ui->degree_id = $request->degree_id;
            $ui->area_of_educations = json_encode($request->area_of_educations);
            $ui->licence_of_certificate = json_encode($request->licence_of_certificate);
            $ui->skills = json_encode($request->skills);
            $ui->reference = json_encode($request->reference);
            $ui->edu_institution = $request->edu_institution;
            $ui->profession_id = $request->profession_id;
            $ui->working_field_id = $request->working_field_id;
            $ui->designation = $request->designation;
            $ui->company_name = $request->company_name;
            $ui->area_of_experiences = json_encode($request->area_of_experiences);
            $ui->save();
        }
        
        $res = User::join('user_infos as ui','ui.user_id','users.id')
                ->where('users.id',config()->get('global.user_id'))
                ->first();

        $res['area_of_experiences'] = json_decode($res->area_of_experiences);
        $res['social'] = json_decode($res->social);
        $res['attachments'] = json_decode($res->attachments);
        $res['area_of_educations'] = json_decode($res->area_of_educations);
        $res['licence_of_certificate'] = json_decode($res->licence_of_certificate);
        $res['skills'] = json_decode($res->skills);
        $res['reference'] = json_decode($res->reference);

        // $redis_data_delete = $this->delete_key($request);

        return response()->json($res);            
            $ui->save();
            
        // $redis_data_delete = $this->delete_key($request);

        return response()->json($ui);

}



  //   public function register(Request $request)
  //   {
  //       $validator = Validator::make($request->all(), [
  //           'name' => 'required',
  //           'email' => 'required|email|unique:users',
  //           'password' => 'required'
  //       ]);

  //       if($validator->fails()){
  //           return response(['message' => 'Validation errors', 'errors' =>  $validator->errors(), 'status' => false], 422);
  //       }

  //       $data['name'] = $request->name;
  //       $data['email'] = $request->email;

  //       DB::beginTransaction();
  //       try{

  //        $user = new User;

  //        $user->name = $request->name;
  //           $user->email = $request->email;
  //           $username_u = str_replace(' ','',$request->name);
  //           if(User::where('username',$username_u)->first())
  //           {
  //              $username_u = $username_u.Str::random(5);
  //           }
  //           $user->username = $username_u;
  //           $user->verify_token = Str::random(40);
  //           $user->verify_status = 3;
  //           $user->status = 1;
  //           $user->password = Hash::make($request->password);
  //           $type =1;




  //           $user->save();



  //           $user_id = $user->id;
        
     //        $UserInfo = new UserInfo;

     //        $UserInfo->social          = '[{"type":0,"link":null}]';
     //        $UserInfo->area_of_experiences    = '[{"title":null}]';
     //        $UserInfo->created_by    = 47;
     //        $UserInfo->updated_by    = 1;
     //        $UserInfo->user_id       = $user_id;
     //        $UserInfo->gender       = $request->gender;
     //        $UserInfo->profession_id       = $request->profession_id;
     //        $UserInfo->save();

  //           $user['token'] =  $user->createToken('MyApp')->accessToken;

     //        return response(['data' => $user, 'message' => 'Account created successfully!', 'status' => true]);

     //        }
     //        catch (\Exception $e) {
     //         // DB::rollback();
     //         return response()->json(['error' =>$e], 500);

  //   // something went wrong
        // }

        
  //   } 

    /**
    * method to do registration by a new user
    * @access public
    * @param array $request
    * @return json response
    */
    public function register(Request $request)
    {

        DB::beginTransaction();

        // try{ 

            $rules = array(
                'name'                  => 'required',
                'profession_id'         => 'required',
                'gender'                => 'required',
                'certificate_name'      => 'required',
                'password'              => 'required|confirmed|min:6',
                'password_confirmation' => 'required',
            );

            if(is_numeric($request->get('email'))){
                $request['phone']  = $request['email'];
                $rules['phone'] = 'required|unique:users|min:11';
            }
            elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
                $rules['email'] = 'required|email|unique:users';
            }else{
                $rules['email'] = 'required|email';
            }

            
            if($this->val->validateRequest($rules)){
                return $this->val->validateRequest($rules);
            }

            $user = $this->userRepository->createUserBasics($request->all());

            $user_id = $user->id;
            
            $data = $request->all();
            $data['disability_type_id'] = $request['disability_id'];

            $this->userRepository->createUserDetails($user_id, $data);

            DB::commit();
        // }
        // catch (\Exception $e) {

        //     DB::rollback();
        //     return response()->json(['error' =>$e], 500);
        // }

        /**Take note of this: Your user authentication access token is generated here **/
        //$data['token'] =  $user->createToken('MyApp')->accessToken;
        $data['name'] =  $user->name;
        

        return response(['data' => $data, 'message' => 'Account created successfully!', 'status' => true]);
    } 


    public function verifyBymail(Request $request)
    {
        return $this->userRepository->verifyBymail($request->all());
    }

    public function verifyBySms(Request $request)
    {
        return $this->userRepository->verifyBySms($request->all());
    }

    public function profile_photo(Request $request){

        return $this->userRepository->upload_profile_photo($request->all());
    }


    public function info(Request $request){

        if($request->headers->get('origin')){
        $url = parse_url($request->headers->get('origin'));
        $path =  explode('.', $url['host']);

        }else{

            $path='';
        }


        $user = $this->userRepository->authuserinfoById(config()->get('global.user_id'));

        return response()->json($user);
        
        // $user = $this->userRepository->userInfo($path);

        $user['access'] = '';

        $ins = InstitutionInfo::select('id','institution_name','email')
                ->where('status',1)
                ->where('user_id',Config('global.user_id'))
                ->get();

                $user['institutions'] = $ins;
       switch ($path[0]) {
            case 'myaccount':
               $role = SystemRole::select('system_roles.id as role_id','ins.id as institution_id','system_roles.role','ins.institution_name')
                        ->join('institution_infos as ins','ins.id','system_roles.owner_id')
                        ->where('system_roles.user_id',config()->get('global.user_id'))
                        ->where('ins.status',1)
                        ->get();               
               break;
           
           default:
                $role = [];
               # code...
               break;
       }
                //$user['roles'] = $role;

                $redis_data_add = $this->add_data($request, $user);
                

        return response()->json($user);
    }

    public function autologin(Request $request){

        $user = User::where('phone', $request->email)
            ->where('verify_status_phone',1)
            ->first();

        if ($user) {
                $data['name'] = $user->name;
                $data['email'] = $user->email;
                $data['type'] = $user->type;
                $data['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
                
                $response = ['data' => $data];
                return response($response, 200);
            } else{
                return response()->json([
                    'msg' => 'User does not exists',
                    'lang' => 'nouser']);
            }
    }

    /** 
     * to login users
     * @return json
     * @param $request array
     * */
    public function login (Request $request) {

            $rules = array(
                'email'                  => 'required',
                'password'               => 'required'
            );

            
            if($this->val->validateRequest($rules)){
                return $this->val->validateRequest($rules);
            }
            if(is_numeric($request->get('email'))){
                // return 1;
                $user = User::where('phone', $request->email)
                        ->where('verify_status_phone',1)
                        ->with('roles')
                        // ->where('status','!=', 2)
                        ->first();
            }else{
                // return 3;
                $user = User::where('email',$request->email)
                        ->where('verify_status',1)
                        ->with('roles')
                        // ->where('status','!=', 2)
                        ->first();
            }
            // return $request->email;
            $access = null;
            if($user && $request->owner_id){
                $access = SystemRole::where('owner_id',$request->owner_id)->where('user_id',$user->id)->first();
            }
            // else{
            //     return response()->json(['errors' => ['nouser' => ['nouser']],'type'=>'nouser'], 400);
            // }   
            if ($user) {
                if (Hash::check($request->password, $user->password)) {

                    $data['name'] = $user->name;
                    $data['email'] = $user->email;
                    $data['type'] = $user->type;
                    $data['roles'] = $user->roles;
                    $data['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
                    
                    $response = ['data' => $data];
                    $this->gamificationStore('login',$user->id,$user->id,1);
                    if($access){
                        $user->role_id = $access->id;
                        $user->save();
                    }
                    if($this->userRepository->logtrack($user)==1){
                        return response($response, 200);
                    }
                } else {
                   return response()->json(['errors' => ['mismatch' => ['e_password_confirm']],'type'=>'mismatch'], 400);
                }
            } else {
                    $blocked = $user = User::where('status',2)
                    ->when(is_numeric($request->get('email')), function ($query, $field) {
                        return $query->where(function($q) {
                            $q->where('phone',Request()->email);
                        });
                    })->when(!is_numeric($request->get('email')), function ($query, $field) {
                        return $query->where(function($q) {
                            $q->where('email',Request()->email);
                        });
                    })->first();


                if($blocked){
                    return response()->json(['errors' => ['blocked_user' => ['blocked_user']],'type'=>'blocked_user'], 400);

                }else{
                    $check = $user = User::when(is_numeric($request->get('email')), function ($query, $field) {
                            return $query->where(function($q) {
                                $q->where('phone',Request()->email);
                            });
                        })->when(!is_numeric($request->get('email')), function ($query, $field) {
                            return $query->where(function($q) {
                                $q->where('email',Request()->email);
                            });
                        })->where('verify_status',0)
                            ->where('verify_status_phone',0)
                            ->orwhere('phone',$request->email)
                            ->first();
                    if($check){
                         return response()->json(['errors' => ['notverified' => ['notverified']],'type'=>'notverified'], 400);
                    }
                }

                return response()->json(['errors' => ['nouser' => ['nouser']],'type'=>'nouser'], 400);
            }

    } 


    public function singleLogin(Request $request){



        if(config()->get('global.owner_id')==1 && config()->get('global.user_id')==1){

            $user = User::where(function($query) use($request) {
                $query->where('Username', $request->username);
            })
            ->first();

            if($user){

                    $data['name'] = $user->name;
                    $data['email'] = $user->email;
                    $data['type'] = $user->type;
                    $data['roles'] = $user->roles;
                    $data['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
                    
                    $response = ['data' => $data];
                    
                    return response($response, 200);
            }
        }


    }

    public function logoutApi()
    { 
        if (Auth::check()) {
           Auth::user()->token()->revoke();
        }
    }


    public function switchaccount(Request $request, $institute){
    
        if($request->headers->get('origin')){

        $url = parse_url($request->headers->get('origin'));
        $path =  explode('.', $url['host']);
        }else{
            $path='';
        }
        if($path[0]=='assessments'){
            $access = AssessmentsRole::select('access')
                        ->where('role','admin')
                        ->where('owner_id',$institute)
                        ->where('user_id',Config::get('global.user_id'))
                        ->first();
        }
        else if($path[0]=='contentbank'){
            $access = ContentBankRole::select('access')
                        ->where('role','admin')
                        ->where('owner_id',$institute)
                        ->where('user_id',Config::get('global.user_id'))
                        ->first();      
                    }
        else if($path[0]=='myaccount'){
            $access = MyaccountRole::select('access')
                        ->where('role','admin')
                        ->where('owner_id',$institute)
                        ->where('user_id',Config::get('global.user_id'))
                        ->first();      
                    }

        return response()->json($access);

        
    }

    public function switchrole(Request $request, $role_id){
        
        $user = User::find(config()->get('global.user_id'));
        if($role_id==0){
            $user->role_id = null;
            $user->save();
        }else{
            $access = SystemRole::select('role','access')
                        ->join('institution_infos as ins','ins.id','system_roles.owner_id')
                        ->where('system_roles.id',$role_id)
                        ->where('system_roles.user_id',config()->get('global.user_id'))
                        ->where('ins.status',1)
                        ->first();
                        
            if($access){
                $user->role_id = $role_id;
                $user->save();
            }
        }
        
        $user = $this->userRepository->authuserinfoById(config()->get('global.user_id'));
        return response()->json($user);
        
        if($request->headers->get('origin')){

        $url = parse_url($request->headers->get('origin'));
        $path =  explode('.', $url['host']);
        }else{
            $path='';
        }
        if($path[0]=='assessments'){
            $access = AssessmentsRole::select('role','access')
                        ->where('id',$role_id)
                        ->first();
        }
        else if($path[0]=='contentbank'){
            $access = ContentBankRole::select('role','access')
                        ->where('id',$role_id)
                        ->first();    
                    }
        else if($path[0]=='myaccount'){
            $access = SystemRole::select('role','access')
                        ->where('id',$role_id)
                        ->first();    
                    }

        return response()->json($access);
    }

    public function create_user(Request $request){
            DB::beginTransaction();

            try{

                $password = mt_rand(1000000000, 9999999999);
            $rules = array(
                'name'                  => 'required',
                'profession_id'         => 'required',
                'gender'                => 'required',
                'password'              => 'required|confirmed|min:6',
                'password_confirmation' => 'required',
            );

            if(is_numeric($request->get('email'))){
                $request['phone']  = $request->email;
                $rules['phone'] = 'required|unique:users|min:11';
            }
            elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
                $rules['email'] = 'required|email|unique:users';
            }else{
                $rules['email'] = 'required|email';
            }

            $request['password'] = $password;
            $request['password_confirmation'] = $password;
            
            if($this->val->validateRequest($rules)){
                return $this->val->validateRequest($rules);
            }

            $data = [
                'name'=>$request['name'],
                'subject' => 'Muktopaath account',
                'short_name' => 'Muktopaath account',
                'to'   => $request->get('email'),
                'password' => $password,
                'template' => 'newidp',
                'message' => 'Hi, '.$request['name'].'. Your Muktopaath id is ' .$request->get('email') .' and password is '.$password .'. Click to login http://muktopaath.gov.bd/login'
            ];

            if(is_numeric($request->get('email'))){
                dispatch(new SendSmsJob($data));
            }else{
                Mail::to($data['to'])->send(new MailSender($data));
            }

            $user = $this->userRepository->createUserBasics($request->all());

            $user_id = $user->id;

            $this->userRepository->createUserDetails($user_id, $request->all());

            DB::commit();
            $data['name'] =  $user->name;
            

            return response(['data' => $data, 'message' => 'Account created successfully!', 'status' => true]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>'something went wrong'], 500);
        }

    }


}