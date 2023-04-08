<?php

namespace App\Repositories\Myaccount;

use Illuminate\Http\Request;
use App\Models\Myaccount\RatingFeedback;
use App\Interfaces\Myaccount\RatingFeedbackInterface;
use DB;
use Yaml;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Lib\SMS;

class RatingFeedbackRepository implements RatingFeedbackInterface
{
    public function store($request){

    	$data = new RatingFeedback;
    	$data->rating = $request['rating'];
    	$data->feedback = $request['feedback'];
    	$data->rating_for = $request['rating_for'];
    	$data->user_id = config()->get('global.user_id');
    	$data->save();


    	return response()->json(['message' => 'feedback given','data' => $data]);
    }

    
    public function approve($id){

        if(config()->get('global.owner_id')==1){
            $data = RatingFeedback::find($id);
            if($data->status==0){
                $data->status = 1;
            }else{
                $data->status = 0;
            }

            $data->update();

            return response()->json($data);
        }
    }

    public function view(){
        if(Request()->call_from=='front'){
            if(Request()->device=='mobile'){
                return RatingFeedback::with('creator')->orderby('id','DESC')->paginate(1);
            }else{
                return RatingFeedback::with('creator')->orderby('id','DESC')->paginate(3);
            }
            
        }
        else if(config()->get('global.owner_id')==1){
            return RatingFeedback::with('creator')->orderby('id','DESC')->paginate(10);
        }
    }

    public function view_all(){
        $data = RatingFeedback::with('creator')->orderby('id','DESC')->paginate(10);

        return response()->json($data);
    }

    public function update($request){
    	
    	$data = RatingFeedback::find($request['id']);
    	$data->rating = $request['rating'];
    	$data->feedback = $request['feedback'];
    	$data->rating_for = $request['rating_for'];
    	$data->user_id = config()->get('global.user_id');
    	$data->update();

    	return response()->json(['message' => 'feedback updated','data' => $data]);
    }

    public function index(){
    	return 21;
    }
}