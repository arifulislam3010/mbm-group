<?php

namespace App\Repositories\Assessment;

use App\Interfaces\Assessment\SAttendanceRepositoryInterface;
use App\Models\Assessment\Attendance;
use App\Models\Assessment\Syllabus;
use App\Http\Resources\Assessment\AttendanceResource;
use App\Models\Assessment\Order;
use DB;
use Auth;
use Carbon\Carbon;

class SAttendanceRepository implements SAttendanceRepositoryInterface
{

    public function attend($id){
        if(config()->get('global.owner_id')){
            return response()->json(['message' => 'joined']);
        }
        if($this->verify($id)){
        $find = Attendance::where('user_id',config()->get('global.user_id'))
        ->where('syllabus_id',$id)
        ->first();
        
        if($find){
            $find->end_time = Carbon::now();
            $find->update();
            $user = config()->get('database.connections.my-account.database');
            
                $data = Attendance::select('u.name','attendances.syllabus_id','attendances.start_time','attendances.end_time')
                ->crossJoin($user.'.users as u')->where('attendances.id',$find->id)
                ->first();
            return response()->json(['data'=>$data,
                'message' =>'Attendance updated']);
        }else{
           $data = new Attendance;
           $data['user_id'] = config()->get('global.user_id');
           $data['syllabus_id'] = $id;
           $data['start_time'] = Carbon::now();
           if($data->save()){
            $user = config()->get('database.connections.my-account.database');
            $data = Attendance::select('u.name','u.id as user_id','attendances.syllabus_id','attendances.start_time','attendances.end_time')
                ->crossJoin($user.'.users as u')->where('attendances.id',$data->id)
                ->first();

                return response()->json(['data' => $data,
                    'message' =>'Attendance given']);
           }
        }
       }else{
                return response()->json(['message' =>'this user has no relation with this module']);
       }

    }


    public function verify($id){
        
        $check = Syllabus::join('course_batches as cb','cb.id','syllabuses.course_batch_id')
        ->join('course_enrollments as ce','ce.course_batch_id','cb.id')
        ->join('orders as o','ce.order_id','o.id')
        ->where('o.user_id',config()->get('global.user_id'))
        ->where('syllabuses.id',$id)
        ->first();

        return $check;
    }

    public function list($id){

        $user = config()->get('database.connections.my-account.database');

        $fetch = Attendance::select('u.name','attendances.id','u.id as user_id','attendances.start_time','attendances.end_time')->crossJoin($user.'.users as u','u.id','attendances.user_id')
                ->where('attendances.syllabus_id',$id)
                ->get();


        return AttendanceResource::collection($fetch);
    }
    
}