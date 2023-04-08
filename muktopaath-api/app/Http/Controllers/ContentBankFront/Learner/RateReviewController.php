<?php

namespace App\Http\Controllers\ContentBankFront\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContentBank\Review;
use App\Models\ContentBank\OrderContent;
use App\Http\Resources\ContentBank\Reviews;

class RateReviewController extends Controller
{
    public function store(Request $request, $id){

    	$check = Review::join('order_contents','order_contents.id','reviews.order_content_id')
    		->where('reviews.order_content_id',$id)
    		->where('order_contents.learning_content_id',$request->learning_content_id)
    		->first();
            
    	if($check){
    		$rev = Review::where('order_content_id',$id)->first();
    		$rev->rating = $request->rating;
    		$rev->review = $request->review;
    		$rev->save();
    	}else{
    		$data = $request->all();
	        $data['order_content_id'] = $id;

	        $res = Review::create($data);
    	} 

        return response()->json(['success' => 'Review given successfully.']);
    }


    public function reviews($id){
    	$userinfo = Review::where('order_content_id',$id)->first();

    	$content = OrderContent::where('id',$id)->value('learning_content_id');

    	$res = Review::select('reviews.review','reviews.rating','orders.user_id','reviews.order_content_id','reviews.created_at')
    			->join('order_contents','order_contents.id','reviews.order_content_id')
    		->join('orders','orders.id','order_contents.order_id')
    		->where('reviews.learning_content_id',$content)->orderby('reviews.id','DESC')->paginate(10);

    	 return  Reviews::collection($res)->additional(['userdata' => $userinfo]);

    }
}
