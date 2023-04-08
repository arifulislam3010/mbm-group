<?php

namespace App\Repositories\Myaccount;

use Illuminate\Http\Request;

use App\Models\Myaccount\InstitutionInfo;
use App\Models\Myaccount\InstitutionType;
use App\Models\Myaccount\AssessmentsRole;
use App\Models\Myaccount\FilemanagerRole;
use App\Models\Myaccount\MyaccountRole;
use App\Models\Myaccount\SystemRole;
use App\Models\Myaccount\User;
use App\Models\Myaccount\ContentBankRole;

 
use Illuminate\Support\Facades\Config;
use App\Interfaces\Myaccount\InstitutionRepositoryInterface;
use App\Http\Resources\Myaccount\InstitutionData;
use DB;
use Yaml;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Lib\SMS;

class InstitutionRepository implements InstitutionRepositoryInterface
{
    
    public function index()
    {
        $res = InstitutionInfo::where('user_id',Config::get('global.user_id'))
        ->when(Request()->search, function ($query, $field) {
            return $query->where(function($q) use($field){
                $q->where('institution_infos.institution_name','like','%'.$field.'%')
                ->orWhere('institution_infos.email','like','%'.$field.'%')
                ->orWhere('institution_infos.phone','like','%'.$field.'%');
            });
                    })
        ->when(!config()->get('global.view_all'), function ($query, $role) {
            return $query->where('institution_infos.created_by', config()->get('global.user_id'));
        })
        ->orderby('id','DESC')->paginate(10);

		return response()->json($res);
    }

    public function public_index(){

    	$res = InstitutionInfo::where('status',1)
    			->withCount('total_course')->paginate(16);

    	if(Request()->with_data){
    		return InstitutionData::collection($res);
    	}

    	return response()->json($res);
    }
 
    public function show($id){

    	$res = InstitutionInfo::select('institution_infos.*','institution_types.instype_eng')
    		->where('institution_infos.id',$id)
    			->leftjoin('institution_types','institution_types.id','institution_type_id')
    		->with('logo','cover')
    		->first();

        $res['social'] = json_decode($res['social']);
        $res['initial'] = json_decode($res['initial']);

    	return response()->json($res);
    }
    
    public function all(){
        $res = InstitutionInfo::where('status',1)
                    ->when(Request()->search, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('institution_infos.institution_name','like','%'.$field.'%')
                            ->orWhere('institution_infos.email','like','%'.$field.'%')
                            ->orWhere('institution_infos.phone','like','%'.$field.'%');
                        });
                    })
                    ->orderby('id','DESC')
                    ->paginate(10);
		return response()->json($res);
	}

	public function partners(){

		$res = InstitutionInfo::select('institution_infos.id','institution_infos.institution_name','institution_name_bn','institution_infos.institution_type_id')
				->with('institution_type','logo')
					->where('status',1)
					->paginate(6);

		return response()->json($res);
	}

	public function unapproved(array $request)
    {

		if(isset($request['type']) && $request['type'] =='rejected'){
			$res = InstitutionInfo::where('status',2)
			->when(!config()->get('global.type'), function ($query, $field) {
                       return $query->where('institution_infos.user_id',config()->get('global.user_id'));
                    })
			->orderby('institution_infos.id','DESC')
			->paginate(10);
		}else if(isset($request['type']) && $request['type'] =='Institutions'){
			$res = InstitutionInfo::when(!config()->get('global.type'), function ($query, $field) {
                       return $query->where('institution_infos.user_id',config()->get('global.user_id'));
                    })
			->orderby('institution_infos.id','DESC')
					->paginate(10);
		}else if(isset($request['type']) && $request['type'] =='approved'){
			$res = InstitutionInfo::when(!config()->get('global.type'), function ($query, $field) {
                       return $query->where('institution_infos.user_id',config()->get('global.user_id'));
                    })
					->where('institution_infos.status',1)
					->orderby('institution_infos.id','DESC')
					->paginate(10);
		}else{
			if(config()->get('global.type')==1){
			
				$res = InstitutionInfo::where('status',0)
				->orderby('institution_infos.id','DESC')
				->paginate(10);
			}else{
				$res = InstitutionInfo::where('status',0)
					->where('institution_infos.user_id',config()->get('global.user_id'))
					->orderby('institution_infos.id','DESC')
					->paginate(10);
			}

		}
        

		return response()->json($res);
    }

    public function types(){
    	$res = InstitutionType::all();
    	return response()->json($res);
    }

    public function approve($request, $id){


    	if($request['type'] =='reject'){
    		$ins = InstitutionInfo::find($id);
	    	$ins->status = 2;
	    	$ins->save();

	    	return response()->json(['msg' => 'Partner request rejected']);
    	}
    	else if($request['type'] =='block'){
    		$ins = InstitutionInfo::find($id);
	    	$ins->status = 3;
	    	$ins->save();

	    	return response()->json(['msg' => 'Partner blocked']);
    	}else{
    	// 	DB::beginTransaction();
    	// try {
		    $ins = InstitutionInfo::find($id);
	    	$ins->status = 1;
	    	$ins->save();

	    	$schema = [];
	    	$myaccount = new SystemRole;

	    	array_push($schema, $myaccount);
	    	
	    	foreach ($schema as $key => $model) {
	    		$model->role = 'admin';

	    		if($key==0){
	    			$role = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));


	    			$access = [];
	    			if($ins->partner_type=='teacher'){
	    				$model->role = 'teacher-admin';
		    			foreach ($role['teacher-admin']['service'] as $key => $value) {
		                    foreach ($value['access'] as $key1 => $value1) {
		                        $access[$key1] = $value1;
		                    }
		                }
	    			}else if($ins->partner_type=='blogger'){
	    				$model->role = 'blogger-admin';
	    				foreach ($role['blogger-admin']['service'] as $key => $value) {
		                    foreach ($value['access'] as $key1 => $value1) {
		                        $access[$key1] = $value1;
		                    }
		                }
	    			}else{
	    				foreach ($role['admin']['service'] as $key => $value) {
		                    foreach ($value['access'] as $key1 => $value1) {
		                        $access[$key1] = $value1;
		                    }
		                }
	    			}

	                $model->json_schema = json_encode($role['admin']['service']);
	    			$model->access = json_encode($access);	    		
	    		}
	    		
	    		$model->owner_id = $ins->id;
	    		$model->user_id = $ins->user_id;
	    		$model->created_by = Config::get('global.user_id');
	    		$model->updated_by = Config::get('global.user_id');
	    		$model->save();

	    		$user = User::where('id',$ins->user_id)->first();

        
	    }
	    if($user->email!==null){

    	    $data = [
	            'name'=>$user->name,
	            'email'   => $user->email,
	            'type' => 'approval'
	        ];


            Mail::send('mail.parent', $data, function($message) use($data){
                $message->to($data['email'], $data['name'])->subject('Your institution is approved
                now');
                $message->from(env('MAIL_VERIFY_ACCOUNT'),'Muktopaath');
            });
        }


		//     DB::commit();
	 //    	return response()->json(['messege' => 'Approved successfully']);

		   
		// } catch (\Exception $e) {
		//     DB::rollback();
	 //    	return response()->json(['messege' => 'Something went wrong'],400);

		//     // something went wrong
		// }
    	}
    	

    	
    }

	public function autoApprove($id){
    	DB::beginTransaction();
    	try {
		    $ins = InstitutionInfo::find($id);
	    	$ins->status = 1;
	    	$ins->save();

	    	$schema = [];
	    	$assessment = new AssessmentsRole;
	    	$filemanager = new FilemanagerRole;
	    	$myaccount = new MyaccountRole;
	    	$contentbank = new ContentBankRole;
	    	$questionbank = new ContentBankRole;

	    	array_push($schema, $assessment,$filemanager,$myaccount,$contentbank,$questionbank);
	    	
	    	foreach ($schema as $key => $model) {
	    		$model->role = 'admin';
	    		if($key==0){
	    			$model->service = 'assessments';
	    			$role = Yaml::parse(file_get_contents(resource_path('yaml/role/assessments.yaml')));

			    	$admin = $role['admin'];
			    	unset($admin['access']['active'],$admin['access']['name']);
	    			$model->access = json_encode($admin);
	    		}
	    		else if($key==1){
	    			$model->service = 'file-manager';
	    			$role = Yaml::parse(file_get_contents(resource_path('yaml/role/filemanager.yaml')));

	    			$admin = $role['admin'];
			    	unset($admin['access']['active'],$admin['access']['name']);
	    			$model->access = json_encode($admin);	    		
	    		}
	    		else if($key==2){ 
	    			$model->service = 'my-account';
	    			$role = Yaml::parse(file_get_contents(resource_path('yaml/role/service_access.yaml')));

	    			$admin = $role['my-account']['access'];
	    			$model->access = json_encode($admin);	    		
	    		}
	    		else if($key==3){
	    			$model->service = 'contentbank';
	    			$role = Yaml::parse(file_get_contents(resource_path('yaml/role/contentbank.yaml')));
	    			$admin = $role['admin']['content'];
			    	unset($admin['access']['active'],$admin['access']['name']);
	    			$model->access = json_encode($admin);
	    		
	    		}
	    		else if($key==4){
	    			$model->service = 'question-bank';
	    			$role = Yaml::parse(file_get_contents(resource_path('yaml/role/contentbank.yaml')));
	    			$admin = $role['admin']['question-bank'];
			    	unset($admin['access']['active'],$admin['access']['name']);
	    			$model->access = json_encode($admin);
	    		
	    		}
	    		$model->owner_id = $ins->id;
	    		$model->user_id = $ins->user_id;
	    		$model->created_by = Config::get('global.user_id');
	    		$model->updated_by = Config::get('global.user_id');
	    		$model->save();
	    	}


		    DB::commit();
	    	return response()->json(['messege' => 'Auto Approved successfully']);

		   
		} catch (\Exception $e) {
		    DB::rollback();
	    	return response()->json(['messege' => 'Something went wrong']);

		    // something went wrong
		}
    	
    }

    public function create($request){

    	$data = $request;
    	$data['user_id'] = Config::get('global.user_id');

    	$user = User::select('name','email','phone','verify_status','verify_status_phone')
		    	->where('id',config()->get('global.user_id'))
		    	->first();

    	$ins  = new InstitutionInfo;
    	$ins->institution_name = $request['institution_name'];
    	$ins->institution_name_bn = isset($request['institution_name_bn'])?$request['institution_name_bn']:null;;
    	$ins->email = isset($request['email'])?$request['email']:null;
    	$ins->phone = isset($request['phone'])?$request['phone']:null;
    	$ins->institution_type_id = $data['institution_type_id'];
    	$ins->username = $request['username'];
    	// $ins->designation = $data['designation'];
    	$ins->address = isset($request['address'])?$request['address']:null;
    	$ins->cover_id = isset($request['cover_id'])?$request['cover_id']:null;
    	$ins->logo_id = isset($request['logo_id'])?$request['logo_id']:null;
    	$ins->contact_person = isset($request['contact_person'])?$request['contact_person']:null;
    	$ins->contact_person_email = isset($request['contact_person_email'])?$request['contact_person_email']:null;
    	$ins->contact_person_mobile = isset($request['contact_person_mobile'])?$request['contact_person_mobile']:null;
    	$ins->partner_type = isset($request['partner_type'])?$request['partner_type']:null;
    	$ins->user_id = $data['user_id'];
    	$ins->save();

    	if(isset($request['contact_person_email'])){
    	 $data = [
            'institution_name'=>$ins->institution_name,
            'name' => $ins->contact_person,
            'email'  => $request['contact_person_email'],
            'type' => 'partner_request'
        ];
            // Mail::send('mail.parent', $data, function($message) use($data){
            //     $message->to($data['email'],$data['name'])->subject('New partner request in muktopaath');
            //     $message->from(env('MAIL_VERIFY_ACCOUNT'),'Muktopaath');
            // });
        
        }

        	if($user->verify_status_phone==1){
        		$message = 'Mr '.$user->name.'.Your partner request to be '.$ins->partner_type.' in muktopaath is requested. wait for the approval';
        		SMS::send($user->phone, $message);
        	}else{
	        	$data = [
	            'institution_name'=>$ins->institution_name,
	            'name' => $ins->contact_person,
	            'email'  => $request['email'],
	            'type' => 'partner_request'
	        ];
	            // Mail::send('mail.parent', $data, function($message) use($data, $user){
	            //     $message->to($user->email,$user->name)->subject('New partner request in muktopaath');
	            //     $message->from(env('MAIL_VERIFY_ACCOUNT'),'Muktopaath');
	            // });
        	}

        

    	//InstitutionInfo::create($data);
    	return response()->json(['data' => 'Institution Created successfully', 'ins_id' => $ins->id]);
    }

    public function create_partner($request,$id){

    	$data = $request;
    	$data['user_id'] = $id;

    	$ins  = new InstitutionInfo;
    	if(Request()->partner_type!='institution'){
    		$ins->institution_name = $request['name'];
    	}else{
    		$ins->institution_name = $request['institution_name'];
    	}

    	if($request['partner_type']!=='institution'){
    		$res['institution_name'] = $request['institution_name'];
    		$ins->metadata = json_encode($res);
    		if(is_numeric($request['email'])){
                $ins->phone = $request['email'];
            }else{
                $ins->email = $request['email'];
            }
    	}else{
    		$ins->email = $request['ins_email'];
			$ins->phone = $request['ins_phone'];
    	}

    	if($request['partner_type'] == 'institution'){
    		$ins->institution_type_id = $request['institution_type_id'];
    	}

    	
    	$ins->username = $request['username'];
    	$ins->website = isset($request['website'])?$request['website']:null;
    	$ins->imei_no = isset($request['imei_no'])?$request['imei_no']:null;
    	$ins->partner_type = $request['partner_type'];
    	// $ins->designation = $data['designation'];
    	//$ins->address = $request['address'];
    	$ins->contact_person = isset($request['contact_person'])?$request['contact_person']:null;
    	$ins->contact_person_email = isset($request['contact_person_email'])?$request['contact_person_email']:null;
    	//$ins->contact_person_mobile = $request['contact_person_mobile'];
    	$ins->user_id = $data['user_id'];
    	$ins->save();

    	if(is_numeric($request['email'])){
    		if($ins->partner_type=='institution'){
            $message = 'Mr. '.$request['name'].'.Your partner request for the institution '.$ins->institution_name.' in muktopaath is requested. wait for the approval';
        	}else{
        		$message = 'Mr '.$request['name'].'.Your partner request to be '.$ins->partner_type.'in muktopaath is requested. wait for the approval';
        	}
            SMS::send($request['email'], $message);
        }else{
        $data = [
            'name'=>$ins->institution_name,
            'partner_type' => $ins->partner_type,
            'email'  => $request['email']
        ];
            Mail::send('mail.partner_request', $data, function($message) use($data){
                $message->to($data['email'],$data['name'])->subject('New partner request in muktopaath');
                $message->from(env('MAIL_VERIFY_ACCOUNT'),'Muktopaath');
            });
        }

    	//InstitutionInfo::create($data);
    	return response()->json(['data' => 'Institution Created successfully', 'ins_id' => $ins->id]);
    }

	public function createIns($request){

    	$data = $request;
    	$data['user_id'] = Request()->user_id;

    	$ins  = new InstitutionInfo;
    	$ins->institution_name = $request['institution_name'];
    	$ins->institution_name_bn = $request['institution_name_bn'];
    	$ins->email = $request['email'];
    	$ins->phone = $request['phone'];
    	$ins->institution_type_id = $request['institution_type_id'];
    	$ins->username = $request['username'];
    	// $ins->designation = $data['designation'];
    	$ins->address = $request['address'];
    	$ins->contact_person = $request['contact_person'];
    	$ins->contact_person_email = $request['contact_person_email'];
    	$ins->contact_person_mobile = $request['contact_person_mobile'];
    	$ins->user_id = $data['user_id'];
    	$ins->save();

    	// InstitutionInfo::create($data);

		return $ins;
    	//return response()->json(['data' => 'Institution Created successfully', 'ins_id' => $ins->id]);
    }

    	public function update($request){

    	$ins = InstitutionInfo::where('id',$request['id'])->first();


    	$ins->institution_name = $request['institution_name'];
    	$ins->institution_name_bn = $request['institution_name_bn'];
    	$ins->email = $request['email'];
    	$ins->phone = $request['phone'];
    	$ins->institution_type_id = $request['institution_type_id'];
    	$ins->cover_id = isset($request['cover_id'])?$request['cover_id']:null;
    	$ins->logo_id = isset($request['logo_id'])?$request['logo_id']:null;
    	$ins->username = $request['username'];
    	// $ins->designation = $data['designation'];
    	$ins->address = $request['address'];
    	$ins->social =  isset($request['social'])?json_encode($request['social']):null;
    	$ins->initial = isset($request['initial'])?json_encode($request['initial']):null; 
    	$ins->contact_person = $request['contact_person'];
    	$ins->contact_person_email = $request['contact_person_email'];
    	$ins->contact_person_mobile = $request['contact_person_mobile'];
 

    	$ins->save();

		return $ins;
    	//return response()->json(['data' => 'Institution Created successfully', 'ins_id' => $ins->id]);
    }

	public function sendPassword($id)
	{
		
		return 'Password  sent';
	}
}