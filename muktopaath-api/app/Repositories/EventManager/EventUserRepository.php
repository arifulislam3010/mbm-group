<?php

namespace App\Repositories\EventManager;

use App\Models\EventManager\EventUser;
use App\Interfaces\EventManager\EventUserRepositoryInterface;

class EventUserRepository implements EventUserRepositoryInterface
{
    public function allUserEvents()
     {
        $res = EventUser::all();
        return response()->json($res);
     }
    
    public function createUserEvent($request){
       $event = new EventUser;
       
       $event->event_id = $request['event_id'];
       $event->user_id = $request['user_id'];
       $event->status = $request['status'];
       $event->created_by = config()->get('global.user_id');
       $event->updated_by = config()->get('global.user_id');
       if($event->save()){
          return response()->json([
            'message' => 'New event user added successfully',
            'data'  => $event
          ]);
       }
    }

    public function updateUserEvent($request){
       $event = EventUser::find($request['id']);
       
       $event->event_id = $request['event_id'];
       $event->user_id = $request['user_id'];
       $event->status = $request['status'];
       

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

    public function deleteUserEvent($id){
       $event = EventUser::find($id);
       if($event){
         $event->delete();
         return response()->json(['message' =>'successfully deleted event user'],200);
      }else{
         return response()->json(['message' => 'Event user to be deleted not found'],404);
     }
    }   
}