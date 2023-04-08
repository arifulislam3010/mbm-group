<?php

namespace App\Http\Controllers\EventManager;

use App\Http\Controllers\Controller;

use App\Models\EventManager\EventDiscussion;
use Illuminate\Http\Request;

class EventDiscussionController extends Controller
{
    
    public function index($event_id)
    {
        $myaccount = config()->get('database.connections.my-account.database');
        $event = config()->get('database.connections.event.database');
        
        $discussion = EventDiscussion::
        // select('b1.event_id as event_id','a1.name as name')
        // ->crossJoin($myaccount.'.users as a1')
        // ->crossJoin($event.'.event_discussions as b1')
        // ->whereRaw('participant.id = b1.user_id')
        where('event_id','=',$event_id)
        ->get();
        return response()->json($discussion);
        
    }

    
    public function store(Request $request){
        $event = new EventDiscussion;
        
        $event->event_id = $request['event_id'];
        $event->user_id = $request['user_id'];
        $event->comment = $request['comment'];
        if($event->save()){
           return response()->json([
             'message' => 'New discussion added successfully',
             'data'  => $event
           ]);
        }
     }
 
     public function update(Request $request){
        $event = EventDiscussion::find($request['id']);
        
        $event->event_id = $request['event_id'];
        $event->user_id = $request['user_id'];
        $event->comment = $request['comment'];
        
 
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
 
     public function destroy($id){
        $event = EventDiscussion::find($id);
        if($event){
          $event->delete();
          return response()->json(['message' =>'Successfully deleted event discussion'],200);
       }else{
          return response()->json(['message' => 'Event discussion to be deleted not found'],404);
      }
     }   
}