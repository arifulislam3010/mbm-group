<?php

namespace Muktopaath\Course\Repositories;

use Muktopaath\Course\Interfaces\SAttendanceRepositoryInterface;
use Muktopaath\Course\Models\Course\Attendance;
use Muktopaath\Course\Models\Course\Syllabus;
use App\Http\Resources\Assessment\AttendanceResource;
use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use DB;
use Auth;
use Carbon\Carbon;

class SAttendanceRepository implements SAttendanceRepositoryInterface
{

    public function attend($id){
        if(config()->get('global.owner_id')){
            if(Request()->all()==''){
                return response()->json(['message' => 'joined']);
            }
            $find = Attendance::find($id);
            $start_time = Request()->start_time?date('Y-m-d H:i:s', strtotime(Request()->start_time) - 60 * 60 * 6):$find->start_time;
            $end_time = Request()->end_time?date('Y-m-d H:i:s', strtotime(Request()->end_time) - 60 * 60 * 6):$find->end_time;
            $find->start_time = $start_time;
            $find->end_time = $end_time;
            $find->update();

            return response()->json(['data'=>$find,
                'message' =>'Attendance updated']);
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

        $db = config()->get('database.connections.my-account.database');

        $fetch  = CourseEnrollment::select('transactions.id as tr_id','course_enrollments.id','syllabuses.id as syllabus_id','attendances.start_time as attendance_start_time','attendances.end_time','u.name')
                ->join('course_batches','course_batches.id','course_enrollments.course_batch_id')
                ->join('orders','orders.id','course_enrollments.order_id')
                ->join('syllabuses','syllabuses.course_batch_id','course_batches.id')
                ->join($db.'.users as u','u.id','orders.user_id')
                ->with(['attendance' => function($q) use($id) {
                    // Query the name field in status table
                        $q->where('syllabus_id', $id); 
                    }])
                ->leftjoin('attendances','attendances.user_id','orders.user_id')
                ->leftJoin('transactions', function($join) use($id)
                        {
                            $join->on('transactions.course_enrollment_id', '=', 'course_enrollments.id')
                            ->where('transactions.syllabus_id',$id);
                        })
                ->where('syllabuses.id',$id)
                ->groupBy('attendances.user_id')
                ->paginate(10);


        return response()->json($fetch);
    }

    // public function list($id){

    //     $db = config()->get('database.connections.my-account.database');

    //     $fetch  = CourseEnrollment::select('transactions.id as tr_id','course_enrollments.id','syllabuses.id as syllabus_id','attendances.start_time as attendance_start_time','attendances.end_time','u.name')
    //             ->join('course_batches','course_batches.id','course_enrollments.course_batch_id')
    //             ->join('orders','orders.id','course_enrollments.order_id')
    //             ->join('syllabuses','syllabuses.course_batch_id','course_batches.id')
    //             ->join($db.'.users as u','u.id','orders.user_id')
    //             ->leftjoin('attendances','attendances.user_id','orders.user_id')
    //             ->leftJoin('transactions', function($join) use($id)
    //                      {
    //                          $join->on('transactions.course_enrollment_id', '=', 'course_enrollments.id')
    //                          ->where('transactions.syllabus_id',$id);
    //                     })
    //             ->where('syllabuses.id',$id)
    //             ->groupBy('attendances.user_id')
    //             ->paginate(10);

    //     return response()->json($fetch);
    // }
    
}