<?php

namespace App\Repositories\EventManager;

use App\Models\EventManager\Event;
use App\Interfaces\EventManager\EventRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventRepository implements EventRepositoryInterface
{
     public function allEvents($request)
     {
         $type = (isset($request['type']))?$request['type']:null;
         $user = (isset($request['user']))?$request['user']:null;
         $start_date = (isset($request['start_date']))?$request['start_date']:null;
         $end_date = (isset($request['end_date']))?$request['end_date']:null;

         if($start_date!=null && $end_date!=null){
            $se_s = 1;
         }else{
            $se_s = null;
         }
         
         if(config('global.type')=='1'){
            $user=='all';
         }else{
            if($user=='all'){
               $user=='me';
            }
         }
        
         
         $res = Event::select('events.*')->leftjoin('event_users', 'events.id', '=' , 'event_users.event_id')
         ->when($type, function($q) use($type){return $q->where('events.type',$type);})
         ->when($se_s, function($q) use($start_date,$end_date){return 
            $q->whereDate('events.created_at','<=',$end_date)
            ->whereDate('events.created_at','>=',$start_date);
         })
          ->when($user, function($q) use($user) { 
            if($user=='me'){
               return $q->where('events.created_by',config()->get('global.user_id'));
            }elseif($user=='share'){
               return $q->where('event_users.user_id',config()->get('global.user_id'));
            }elseif($user=='all'){
               return $q->where('events.created_by',config()->get('global.user_id'))->orWhere('event_users.user_id',config()->get('global.user_id'));
            } 
            
         })
         ->orderBy('id','DESC')
        ->customPaginate();
        return response()->json($res);
     }
    
    public function createEvent($request){
       $event = new Event;
       $duration = (int)$request['get_hrs'] * 60;
       $duration += (int)$request['get_mins'];
       
       $event->title = $request['title'];
       $slug = Str::slug($event->title);
       
       $slug_old = Event::where('slug', '=', $slug)->first();
       
       if($slug_old){
         $event->slug= $slug.mt_rand(1111,9999);
       }else{
         $event->slug=$slug;
       }
       
       
       $event->type = $request['type'];
       
       $event->details = $request['details'];
       $event->thumbnail = $request['thumbnail'];
       $event->start_time = date('Y-m-d H:i:s',strtotime($request['start_time']));
       $event->end_time = date('Y-m-d H:i:s',strtotime($event->start_time . ' +' . $duration . ' Minutes'));
       $event->event_id = $request['event_id'];
       $event->duration = $duration;
       $event->live_offline = $request['live_offline'];
       $event->live_link = $request['live_link'];
       $event->location = $request['location'];
       $event->instructor = $request['instructor'];
       $event->course_id = $request['course_id'];
       $event->lesson_id = $request['lesson_id'];
       $event->created_by = config()->get('global.user_id');

       if($event->save()){
          return response()->json([
            'message' => 'New event added successfully',
            'data'  => $event
          ]);
       }
    }

    public function updateEvent($request){
       $event = Event::find($request['id']);
       
       $event->title = $request['title'];
       $event->type = $request['type'];
       $event->details = $request['details'];
       $event->thumbnail = $request['thumbnail'];
       $event->start_time = $request['start_time'];
       $event->end_time = $request['end_time'];
       $event->event_id = $request['event_id'];
       $event->duration = $request['duration'];
       $event->live_offline = $request['live_offline'];
       $event->live_link = $request['live_link'];
       $event->location = $request['location'];
       $event->instructor = $request['instructor'];
       $event->course_id = $request['course_id'];
       $event->lesson_id = $request['lesson_id'];
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

    public function deleteEvent($id){
       $event = Event::find($id);
       if($event){
         $event->delete();
         return response()->json(['message' =>'successfully deleted event'],200);
      }else{
         return response()->json(['message' => 'Event to be deleted not found'],404);
     }
    }

   //  public function dateFilter($request){
       
   //    $start = Carbon::parse($request['start_date']);
   //    $end = Carbon::parse($request['end_date']);
   //    $get_all_event = Event::whereDate('created_at','<=',$end)
   //    ->whereDate('created_at','>=',$start)
   //    ->get();
   //    return $get_all_event;
   //  }
    
}