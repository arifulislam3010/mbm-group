<?php

namespace App\Http\Controllers\ContentBankFront\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ContentBank\LearningContent as LearningResource;
use App\Http\Resources\ContentBank\LearningContentShow as LearningResourceShow;
use App\Models\ContentBank\LearningContent;

use App\Models\ContentBank\Order;
use App\Models\ContentBank\OrderContent;
use Auth;
use DB;

class EnrollmentController extends Controller
{
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
}
