<?php

namespace App\Http\Controllers\MyAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yaml;
Use App\Models\Myaccount\AssessmentsRole;
Use App\Models\Myaccount\MyaccountRole;
Use App\Models\Myaccount\SystemRole;
Use App\Models\Myaccount\ContentBankRole;
Use App\Models\Myaccount\FilemanagerRole;
Use App\Models\Myaccount\InstitutionsInfos;
Use App\Http\Controller\Resources\Myaccount\UserRole;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Lib\SMS;

class RoleController extends Controller
{
    public function get(Request $request){ 
        
        return $yamlContents = Yaml::parse(file_get_contents(resource_path('yaml/role/assessments.yaml')));
    }

    public function UserRole(Request $request){

          $data =  $request->all();
      

        if(isset($data['service'])){
            $service = $data['service'];

            if($service=='assessments' || $service == 'system-user'){

                if(config()->get('global.type')==1){

                    $data = SystemRole::select('u1.name as created_by','users.id as user_id','users.name','users.email','system_roles.id','system_roles.role')
                       ->join('users','users.id','system_roles.user_id')
                       ->join('users as u1', 'u1.id' , 'system_roles.created_by')
                       ->when($request->role, function ($query, $role) {
                            return $query->where('system_roles.role', $role);
                        })
                       ->when($request->field, function ($query, $field) {
                            return $query->where(function($q) use($field){
                                $q->where('users.name','like','%'.$field.'%')
                                ->orWhere('users.email','like','%'.$field.'%');
                            });
                        })
                       ->where('system_roles.id','!=',1)
                        ->where('system_roles.owner_id',config()->get('global.owner_id'))
                       ->groupBy('system_roles.role','system_roles.owner_id','system_roles.user_id')
                       ->orderby('system_roles.id', 'DESC')
                       ->paginate(50);
               }else{

                    $data = SystemRole::select('u1.name as created_by','users.id as user_id','users.name','users.email','system_roles.id','system_roles.role')
                   ->join('users','users.id','system_roles.user_id')
                   ->join('users as u1', 'u1.id' , 'system_roles.created_by')
                   ->where('owner_id',$request->header('owner_id'))
                   ->when($request->role, function ($query, $role) {
                        return $query->where('system_roles.role', $role);
                    })
                   ->when(!config()->get('global.view_all'), function ($query, $role) {
                        return $query->where('system_roles.created_by', config()->get('global.user_id'));
                    })
                   ->when($request->field, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('users.name','like','%'.$field.'%')
                            ->orWhere('users.email','like','%'.$field.'%');
                        });
                    })
                   ->where('system_roles.id','!=',1)
                   ->where('system_roles.owner_id',config()->get('global.owner_id'))
                   ->groupBy('system_roles.role','system_roles.owner_id','system_roles.user_id')
                   ->orderby('system_roles.id', 'DESC')
                   ->paginate(50);
               }
            }elseif($service=='content-bank'){
                $data = ContentBankRole::select('users.name','users.email','content_bank_roles.id','content_bank_roles.role')
                   ->join('users','users.id','content_bank_roles.user_id')
                   ->where('service','content-bank')
                   ->where('owner_id',$request->header('owner_id'))
                   ->orderby('content_bank_roles.id', 'DESC')
                   ->get();
            }elseif($service=='question-bank'){
                $data = ContentBankRole::select('users.name','users.email','content_bank_roles.id','content_bank_roles.role')
                   ->join('users','users.id','content_bank_roles.user_id')
                   ->where('service','question-bank')
                   ->where('owner_id',$request->header('owner_id'))
                   ->orderby('content_bank_roles.id', 'DESC')
                   ->get();
            }elseif($service=='file-manager'){
                $data = FilemanagerRole::select('users.name','users.email','filemanager_roles.id','filemanager_roles.role')
                   ->join('users','users.id','filemanager_roles.user_id')
                   ->where('owner_id',$request->header('owner_id'))
                   ->orderby('filemanager_roles.id', 'DESC')
                   ->get();
            }elseif($service=='my-account'){
                $data = MyaccountRole::all();
            }else{
                $data =['code'=>'200','message' => 'No data found',];
            }
        }else{
            $data =['code'=>'200','message' => 'No data found',];
            
        } 
        return response()->json($data);
    }

    public function specificaccess($role){
        $yaml = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));

        $arr = $yaml[$role];


        foreach ($arr['service'] as $key => $r) {
            foreach ($r['access'] as $key1 => $access) {
                foreach ($access as $key2 => $val) {
                    $i = 1;
                    //return $key2;
                    if($val==false){
                         unset($arr['service'][$key]['access'][$key1][$key2]);
                    }else{
                        $i = 0;
                    }
                }
                if($arr['service'][$key]['access'][$key1]==null){
                    unset($arr['service'][$key]['access'][$key1]);
                };

                
            }
        }

     

        return response()->json($arr);


    }

    public function servicewiserole($service){

        if($service == 'system-user'){
            $arr = [];

            if(config()->get('global.type')==1){
                $users = SystemRole::select(DB::raw('count(DISTINCT(user_id)) as total'))
                ->where('id','!=',1)
                ->where('owner_id',config()->get('global.owner_id'))
                ->value('total');

                $counts = SystemRole::select('role',DB::raw('count(user_id) as total'))
                ->where('id','!=',1)
                ->where('owner_id',config()->get('global.owner_id'))
                ->groupBy('role')->get();
            }else{
                $users = SystemRole::select(DB::raw('count(DISTINCT(user_id)) as total'))
                ->where('id','!=',1)
                ->where('owner_id',config()->get('global.owner_id'))
                ->value('total');

                $counts = SystemRole::select('role',DB::raw('count(system_roles.user_id) as total'))
                    ->join('institution_infos as ii','ii.id','system_roles.owner_id')
                    ->where('system_roles.owner_id',config()->get('global.owner_id'))
                    ->where('system_roles.id','!=',1)
                    ->groupBy('system_roles.role')->get();

            }

            $yaml = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));

            foreach ($yaml as $key => $role) {
                if(config()->get('global.type')==1 || config()->get('global.sysrole')){
                    if(str_starts_with($key,'sys-')){
                        $ch = str_replace("sys-","",$key);
                        if($ch!=='admin'){
                            $arr[$key]['name'] = $role['name'];
                            $arr[$key]['keyname'] = $key; 
                        }
                        
                    }
                    
                }else if(str_starts_with(config()->get('global.currentrole'),'teacher-')){
                   if(str_starts_with($key,'teacher-')){
                        $ch = str_replace("teacher-","",$key);
                        if($ch!=='admin'){
                            $arr[$key]['name'] = $role['name'];
                            $arr[$key]['keyname'] = $key; 
                        }
                    }
                }else if(str_starts_with(config()->get('global.currentrole'),'blogger-')){
                   if(str_starts_with($key,'blogger-')){
                        $ch = str_replace("blogger-","",$key);
                        if($ch!=='admin'){
                            $arr[$key]['name'] = $role['name'];
                            $arr[$key]['keyname'] = $key; 
                        }
                    }
                }else{
                    if(!str_starts_with($key,'sys-') && !str_starts_with($key,'blogger-') && !str_starts_with($key,'teacher-')){
                        if($key!=='admin'){
                            $arr[$key]['name'] = $role['name'];
                            $arr[$key]['keyname'] = $key; 
                        }
                        
                    }
                }
            }

            $data['users'] = $users;
            $data['counts'] = $counts;
            $data['arr'] = $arr;

            return response()->json($data);
        }

    }

    public function show($service,$id){

        if($service == 'content-bank' || $service == 'question-bank'){
           $res =  ContentBankRole::select('users.name','users.id as user_id','users.email','users.phone','content_bank_roles.id','content_bank_roles.role','content_bank_roles.access')->join('users','users.id','content_bank_roles.user_id')
               ->where('content_bank_roles.id',$id)
               ->first();
        }else if($service == 'file-manager'){
            $res =  FilemanagerRole::select('users.name','users.id as user_id','users.email','users.phone','filemanager_roles.id','filemanager_roles.role','filemanager_roles.access')->join('users','users.id','filemanager_roles.user_id')
               ->where('filemanager_roles.id',$id)
               ->first();
        }else if($service == 'assessments' || $service == 'system-user'){

            $res = SystemRole::select('system_roles.id','system_roles.json_schema','system_roles.user_id','system_roles.access','system_roles.role','users.email')
                ->where('system_roles.id',$id)
                ->join('users','users.id','system_roles.user_id')->first();
  
        }

        return response()->json($res);
    }

    public function access_given(){
        $filemanager = FilemanagerRole::all()->count();
        $questionbank = ContentBankRole::where('service','question-bank')->get()->count();
        $contentbank = ContentBankRole::where('service','content-bank')->get()->count();
        $assessments = AssessmentsRole::all()->count();
        
        return response()->json([
            'filemanager' => $filemanager,
            'questionbank'=> $questionbank,
            'contentbank'=> $contentbank,
            'assessments'=> $assessments
        ]);
    }

    public function UserRoleStore(Request $request){

        $check = SystemRole::where('system_roles.user_id',$request['user_id'])
                    ->join('users','users.id','system_roles.user_id')
                    ->join('institution_infos','institution_infos.id','system_roles.owner_id')
                    ->where('system_roles.owner_id',config()->get('global.owner_id'))
                    ->first();


      
        if($request->id){

                $access = [];

                foreach ($request['access'] as $key => $value) {
                    foreach ($value['access'] as $key1 => $value1) {
                        $access[$key1] = $value1;
                    }
                }

                $data = SystemRole::find($request->id);
                $data->role = $request->role;
                $data->user_id = $request->user_id;
                $data->json_schema = json_encode($request['access']);
                $data->access = json_encode($access);
                $data->owner_id = config()->get('global.owner_id');
                $data->created_by = config()->get('global.user_id');
                $data->updated_by = config()->get('global.user_id');
                $data->save();
                return response()->json(['message' => 'Role updated successfully',
                                    'data' => $data]);
            }

        if(!$check){

            $access = [];

        foreach ($request['access'] as $key => $value) {
            foreach ($value['access'] as $key1 => $value1) {
                $access[$key1] = $value1;
            }
        }
            $dt = new SystemRole;
            $dt->role = $request->role;
            $dt->user_id = $request->user_id;
            $dt->json_schema = json_encode($request['access']);
            $dt->access = json_encode($access);
            $dt->owner_id = config()->get('global.owner_id');
            $dt->created_by = config()->get('global.user_id');
            $dt->updated_by = config()->get('global.user_id');
            $dt->save();

            $ch = SystemRole::where('system_roles.user_id',$dt->user_id)
                    ->join('users','users.id','system_roles.user_id')
                    ->join('institution_infos','institution_infos.id','system_roles.owner_id')
                    ->where('system_roles.owner_id',config()->get('global.owner_id'))
                    ->first();

            $data = [
                'name' => $ch->name,
                'email' => $ch->email,
                'type' => 'newrole',
                'role' => $dt->role,
                'institution' => $ch->institution_name

            ];

            Mail::send('mail.parent', $data, function($message) use($data){
                $message->to($data['email'], $data['name'])->subject('New role assigned in muktopaath');
                $message->from(env('MAIL_VERIFY_ACCOUNT'),'Muktopaath');
            });

            return response()->json(
                ['message' => 'Role created successfully',
                 'data' => $dt
            ]
            );

        }

        return response()->json(
            ['message' => 'User already exists',
            'code' => 1]
        );

    }

    public function deletemultiple(Request $request){
        $data = $request->all();
        foreach ($data['u'] as $value) {
            $del = SystemRole::find($value);
            $del->delete();
        }
    }


    public function delete($service, $id){
            $del = SystemRole::find($id);

        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted role']);
        }else{
            return response()->json(['message' => 'Content to be deleted not found']);
        }

    }
}
