<?php

namespace App\Http\Controllers\MyAccount;

use App\Interfaces\Myaccount\InstitutionRepositoryInterface;
use App\Interfaces\Myaccount\UserRepositoryInterface;
use App\Mail\MailSender;
use App\Repositories\Validation;

use App\Http\Controllers\Controller;
//use App\Http\Resources\Myaccount\User;
use App\Models\Myaccount\User;
use Illuminate\Http\Request;
Use App\Models\Myaccount\UserInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Myaccount\InstitutionType;
use App\Models\Myaccount\InstitutionInfo;
use App\Models\Myaccount\AssessmentsRole;
use App\Models\Myaccount\FilemanagerRole;
use App\Models\Myaccount\MyaccountRole;
use App\Models\Myaccount\ContentBankRole;
use App\Repositories\Myaccount\InsRepo;
use Illuminate\Support\Facades\Config;
use Validator;
use Auth;
use DB;
use Yaml;
use Illuminate\Support\Facades\Mail;
use App\Lib\SMS;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;

class InstitutionController extends Controller
{
	private  $institutionRepository;
	private  $userRepository;
    private $val;

    public function __construct(InstitutionRepositoryInterface $institutionRepository,UserRepositoryInterface $userRepository, Validation $val) 
    {
        $this->institutionRepository = $institutionRepository;
		$this->userRepository = $userRepository;
        $this->val = $val;
    }
	

	public function index(){
		return $this->institutionRepository->index();
	}

	public function public_index(){
		return $this->institutionRepository->public_index();
	}

	public function show($id){
		return $this->institutionRepository->show($id);
	}

	public function all(){
		return $this->institutionRepository->all();
	}

	public function partners(){
		return $this->institutionRepository->partners();
	}

	public function types(){
    	$res = InstitutionType::all();
    	return response()->json($res);
    }

	public function unapproved(Request $request){
		return $this->institutionRepository->unapproved($request->all());
	}

	public function approve(Request $request, $id){
		return $this->institutionRepository->approve($request->all(),$id);
    	
    }


    public function store(Request $request){

		$rules = array(
			'institution_name'  =>'required',
			'username'			=> 'required|unique:institution_infos',
			'institution_type_id'	=> 'required'
		);

		if(isset($request->partner_type) && $request->partner_type=='institution'){
			$rules['contact_person'] = 'required';
		}

		if(!$request['from_frontend']){

			$rules['address'] = 'required';
			$rules['email'] = 'required';
			$rules['contact_person'] = 'required';
		}
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }


		return $this->institutionRepository->create($request->all());
    }

    public function update(Request $request){
    	
    	$rules = array(
			'institution_name'  =>'required',
			'email'             => 'required|email',
			'contact_person'    => 'required',
			'institution_type_id'   => 'required',
			'address'   => 'required',
			'phone' 	=> 'regex:/(01)[0-9]{9}/'
		);

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }


		return $this->institutionRepository->update($request->all());
    }

	public function create_user(Request $request){
		// DB::beginTransaction();

  //       try{ 

			$password = mt_rand(1000000000, 9999999999);
			
            $rules = array(
                'name'                  => 'required',
                'profession_id'         => 'required',
                'gender'                => 'required',
                'password'              => 'required',
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

				if($this->val->validateCondition($rules, $request->all())){
					return $this->val->validateCondition($rules, $request->all());
				}


			$user = $this->userRepository->createUserBasics($request->all());
	
			$user_id = $user->id;

			$this->userRepository->createUserDetails($user_id, $request->all());

			$data = [
	            'name'=>$request['name'],
	            'password' => $password,
	            'to' => $request['email'],
				'subject' => 'Muktopaath id and password',
				'short_name' => 'Muktopaath account',
				'template' => 'newidp',
	        ];
	        if(!is_numeric($request->get('email'))){
                // Mail::send('mail.parent', $data, function($message) use($data){
                // $message->to($data['email'],$data['name'])->subject('Muktopaath id and password');
                // $message->from(env('MAIL_VERIFY_ACCOUNT'),'Muktopaath');
            	// });

                Mail::to($data['to'])->send(new MailSender($data));
            }


        

			// DB::commit();
            
            
   //      }
   //      catch (\Exception $e) {

   //          DB::rollback();
   //          return response()->json(['error' =>'something went wrong'], 500);
   //      }

		// Send password to the user
		$this->institutionRepository->sendPassword($password);

        /**Take note of this: Your user authentication access token is generated here **/
        $data['token'] =  $user->createToken('MyApp')->accessToken;
        $data['name'] =  $user->name;

        return response(['data' => $data, 'message' => 'Account created successfully!', 'status' => true]);
	}


	public function addpartner(Request $request){
	
		if ($request->user_id !== "") {
			// try{
				$rules = array(
					'institution_name'  =>'required',
					'email'             => 'required',
					'contact_person'    => 'required',
					'institution_type_id'   => 'required',
					'username'   => 'required|unique:institution_infos',
				);
				
				if($this->val->validateRequestCondition($rules, $request->insform)){
					return $this->val->validateRequestCondition($rules, $request->insform);
				}
				
				$institution = $this->institutionRepository->createIns($request->insform);
				return $this->institutionRepository->autoApprove($institution->id);
				
			// }
			// catch (\Exception $e) {

			// 	DB::rollback();
			// 	return response()->json(['error' =>'something went wrong'], 500);
			// }
		}
		else
		{
			// DB::beginTransaction();

   //      try{ 

			$password = mt_rand(1000000000, 9999999999);
			$this->institutionRepository->sendPassword($password);
            $rules = array(
                'name'                  => 'required',
                'profession_id'         => 'required',
                'gender'                => 'required',
                'email'                 => 'required|email|unique:users',
                'password'              => 'required|confirmed|min:6',
                'password_confirmation' => 'required',
            );

			if(Request()->has('password'))
			{
				$request->userform['password'] = $password;
				$request->userform['password_confirmation'] = $password;

				if($this->val->validateCondition($rules, $request->userform)){
					return $this->val->validateCondition($rules, $request->userform);
				}
	
				
			}

			$user = $this->userRepository->createUserBasics($request->userform);
	
			$user_id = $user->id;

			$this->userRepository->createUserDetails($user_id, $request->userform);

			// DB::commit();
            
            
   //      }
   //      catch (\Exception $e) {

   //          DB::rollback();
   //          return response()->json(['error' =>'something went wrong'], 500);
   //      }

        /**Take note of this: Your user authentication access token is generated here **/
        $data['token'] =  $user->createToken('MyApp')->accessToken;
        $data['name'] =  $user->name;
		
		$institution = $this->institutionRepository->createIns($request->insform);
		$this->institutionRepository->autoApprove($institution->id);

        return response(['data' => $data, 'message' => 'Account created successfully!', 'status' => true]);
		}
	}
	
}