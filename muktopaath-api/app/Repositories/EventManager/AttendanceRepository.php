<?php

namespace App\Repositories\EventManager;

use App\Models\EventManager\Attendance;
use App\Interfaces\EventManager\AttendanceRepositoryInterface;

class AttendanceRepository implements AttendanceRepositoryInterface
{   
    public function allAttendances()
     {
        $res = Attendance::all();
        return response()->json($res);
     }
    
    public function createAttendance($request){
       $attendance = new Attendance;
       
       $attendance->event_id = $request['event_id'];
       $attendance->user_id = $request['user_id'];
       $attendance->attend_time = $request['attend_time'];

       if($attendance->save()){
          return response()->json([
            'message' => 'Attendance added successfully',
            'data'  => $attendance
          ]);
       }
    }

    public function updateAttendance($request){
       $attendance = Attendance::find($request['id']);
       
       $attendance->event_id = $request['event_id'];
       $attendance->user_id = $request['user_id'];
       $attendance->attend_time = $request['attend_time'];

       if($attendance->update()){
         return response()->json([
             'message' => 'Successfully updated',
             'data'   => $attendance
         ],201);
     }else{
         return response()->json([
             'message' => 'Something went wrong!'
         ], 400);
     }
    }

    public function deleteAttendance($id){
       $attendance = Attendance::find($id);
       if($attendance){
         $attendance->delete();
         return response()->json(['message' =>'Attendance successfully deleted'],200);
      }else{
         return response()->json(['message' => 'Attendance to be deleted not found'],404);
     }
    }   
}