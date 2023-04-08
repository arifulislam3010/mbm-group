<?php

namespace App\Http\Controllers\MyAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Myaccount\Sharing;
use App\Http\Resources\ContentBank\Sharing as ContentShare;
use App\Http\Resources\Assessment\Sharing as AssessmentShare;


class SharingController extends Controller
{
    public function create(Request $request){
    
      foreach ($request->selected as $key => $value) {
        $check = Sharing::where('user_id',$value['id'])
            ->where('table_id',$request->module_id)
            ->first();

            if(!$check){
          $data['user_id'] = $value['id'];
          $data['table_id'] = $request->module_id;
          $data['table_name'] = $request->module_name;
          $data['activity'] = $request->role;
          $data['created_by'] = config()->get('global.user_id');
          if($request->module_name=='questions' || $request->module_name=='learning_contents'){
            $data['db_con_name'] = 'content-bank';
          }else if($request->module_name=='courses'){
            $data['db_con_name'] = 'assessments';
          }
          Sharing::create($data);
      }
    }

      return response()->json(['message' => 'Shared successfully']);

    }

    public function shared_with_me(Request $request){
        

        $res = Sharing::where('db_con_name',$request->module)
                ->where('user_id',config()->get('global.user_id'))
                ->get();


        switch ($request->module) {
            case 'content-bank':
                $count = Sharing::where('table_name',$request->table)
                ->where('user_id',config()->get('global.user_id'))
                ->get();
                return ContentShare::collection($count);

                break;

            case 'assessments':

                $count = Sharing::where('table_name','courses')
                ->where('user_id',config()->get('global.user_id'))
                ->get();
                
                return AssessmentShare::collection($count);

                break;
            
            default:
                // code...
                break;
        }

        

        return response()->json($res);
    }
}
