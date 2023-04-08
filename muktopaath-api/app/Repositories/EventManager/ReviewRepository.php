<?php

namespace App\Repositories\EventManager;

use App\Models\EventManager\Review;
use App\Interfaces\EventManager\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function allReview(){
        $res = Review::all();
        return response()->json($res);
    }

    public function addReview($request){
       $event = new Review;
       
       $event->event_id = $request['event_id'];
       $event->rating = $request['rating'];
       $event->review = $request['review'];
       $event->created_by = config()->get('global.user_id');

       if($event->save()){
          return response()->json([
            'message' => 'New review added successfully',
            'data'  => $event
          ]);
       }
    }

    public function updateReview($request){
        $event = Review::find($request['id']);
        
        $event->event_id = $request['event_id'];
        $event->rating = $request['rating'];
        $event->review = $request['review'];
        $event->updated_by = config()->get('global.user_id');
 
        if($event->update()){
          return response()->json([
              'message' => 'Successfully updated',
              'data'   => $event
          ],201);
      }else{
          return response()->json([
              'message' => 'Something went wrong!'
          ], 400);
      }
     }

     public function deleteReview($id){
        $event = Review::find($id);
        if($event){
          $event->delete();
          return response()->json(['message' =>'successfully deleted review'],200);
       }else{
          return response()->json(['message' => 'Review to be deleted not found'],404);
      }
     }
}