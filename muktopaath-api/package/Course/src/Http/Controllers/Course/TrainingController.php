<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Models\Course\RestrictedUserInfo;
use Illuminate\Support\Str;
use App\Repositories\Validation;

use Auth;
 
class TrainingController extends Controller
{ 

    private  $val;

    public function __construct( Validation $val) 
    {
        $this->val = $val;
    }

    public function add_people_info(Request $request){

        $rules = array(
            'designation'          => 'required',
            'institution'          => 'required',
            'address'              => 'required',
            'id'                   => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $data = new RestrictedUserInfo;
        $data->restricted_user_id =  $request['id'];
        $data->designation        =  $request['designation'];
        $data->institution        =  $request['institution'];
        $data->address            =  $request['address'];
        $data->save();

        return response()->json(['message' => 'Info added', 'data' => $data]);
    }


    public function update_people_info(Request $request, $id){

        $rules = array(
            'designation'          => 'required',
            'institution'          => 'required',
            'address'              => 'required',
            'id'                   => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $data = RestrictedUserInfo::find($id);
        $data->restricted_user_id =  $request['id'];
        $data->designation        =  $request['designation'];
        $data->institution        =  $request['institution'];
        $data->address            =  $request['address'];
        $data->update();

        return response()->json(['message' => 'info updated', 'data' => $data]);
    }
}