<?php

namespace App\Http\Controllers\ContentBankFront\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContentBank\OrderContent;
use App\Models\ContentBank\LearningContent;
use App\Models\ContentBank\Order;
use App\Models\ContentBank\Submission;
use App\Models\Question\Question;
use App\Http\Resources\ContentBank\ContentDetails;
use DB;
use Auth;

class ContentBankController extends Controller
{
    public function details($id){
    	
    	$res = OrderContent::join('learning_contents','learning_contents.id','order_contents.learning_content_id')
    			->where('order_contents.id',$id)
    			->first();

            if($res){
    		return new ContentDetails($res);
        }else{
            return response()->json(['message' => 'COntent not found']);
        }
    		
    }

            public function enroll(Request $request, $content_id){

        $user_id = Auth::user()->id;
        
        $check = OrderContent::select('order_contents.id')->join('orders','orders.id','order_contents.order_id')
                    ->where('order_contents.learning_content_id',$content_id)
                    ->where('orders.user_id',$user_id)
                    ->first();
                    if($check){
                        return  response()->json($check->id);
                    }
                    else{
                        DB::beginTransaction();
        try{
            $order = new Order;
                $order->amount              = 0;
                $order->payment_status      = 0;
                $order->type                = 0;
                $order->user_id             = $user_id;
                $order->save();

                $content     = new OrderContent;
                $content->learning_content_id           = $content_id;
                $content->order_id           = $order->id;
                $content->save();
                DB::commit();

                return response()->json($content->id);
        }catch (\Exception $e) {
            DB::rollback();
    // something went wrong
}

        }

                
    }


    public function submit(Request $request,$id){

    	    $data = $request->all();
    

    		$res = OrderContent::where('id',$id)->value('learning_content_id');
    		

    		$content = LearningContent::where('id',$res)->first();
    		$carry = json_decode($content->quiz_marks);

    		
    		$quiz = Question::wherein('id',json_decode($content->quiz_data))
    				->get();

    	    $answer = [];


    		foreach ($quiz as $key => $value) {

    			array_push($answer, json_decode($value->answer));
    		}

    		$count = 0;
    		$mark = 0;


    		foreach ($answer as $key => $value) {
    			$mark+=$carry[$key];

    			foreach ($value as $key1 => $value1) {

    				if($value1->answer!==$data[$key][$key1]['answer']){
    					$count++;
    			$mark-=$carry[$key];


    					break;
    				}
    			}
    		}

    		// $batch_id = CourseEnrollment::where('id',$enroll_id)->value('course_batch_id');

    		$submit = new Submission;

    		$submit->submitted_answers = json_encode($request->all());
    		$submit->submission_type = 'quiz';
    		$submit->order_content_id = $id;
    		$submit->marks = $mark;
    		$submit->save();

    		return response()->json($mark);
    }

    public function recomendations(){
    	$res = LearningContent::select('id','content_type','title','description')->paginate(100);
    	return response()->json($res);
    }
}
