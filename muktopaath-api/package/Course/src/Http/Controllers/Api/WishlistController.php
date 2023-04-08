<?php

namespace Muktopaath\Course\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Muktopaath\Course\Models\Course\Wishlist;
use Muktopaath\Course\Http\Resources\Batch as ResourceBatch;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Http\Resources\WishList as ResourceWishList;

class WishlistController extends Controller
{
    public function wishlistall(){
       $user = Auth::user();
       $WishList = Wishlist::where('user_id',$user->id)->where('status',1)->paginate(8);
       return ResourceWishList::collection($WishList);
   }
   public function wishlistCheck($id){
        $user_id = Auth::user()->id;
        $Wishlist = Wishlist::where('user_id',$user_id)->where('course_batch_id',$id)->first();
        if($Wishlist){
            return 1;
        }else{
            return 0;
        }
   }
   
    public function wishlists($id){
        $user_id = Auth::user()->id;
       
        $Wishlist = Wishlist::where('user_id',$user_id)->where('course_batch_id',$id)->first();

        if(empty($Wishlist)){
            $WishlistNew = new Wishlist();
            $WishlistNew->user_id = $user_id;
            $WishlistNew->course_batch_id = $id;
            $WishlistNew->status = 1;
            $WishlistNew->save();
            $data = [
                'status'  => 1,
            ];
        }else{
            $Wishlist->delete();
            $data = [
                'status'  => 0,
            ];
        }
        
        return response()->json($data,200);
    }
}
