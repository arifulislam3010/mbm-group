<?php

namespace Muktopaath\Course\Repositories;

use Muktopaath\Course\Interfaces\DiscussionRepositoryInterface;
use Muktopaath\Course\Models\Course\Attendance;
use Muktopaath\Course\Models\Course\Discussion;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\Order;
use DB;
use Auth;
use Carbon\Carbon;

class DiscussionRepository implements DiscussionRepositoryInterface
{
    public function index(){
        $request = Request();
        $check = $request->has('syllabus_id')?$request->syllabus_id:null;
        
        $myaccount = config()->get('database.connections.my-account.database');
        $assessment = config()->get('database.connections.assessment.database');
        
        $discussion = Discussion::select('discussions.id','discussions.syllabus_id as syllabus_id','a1.name as name','a1.username','a1.photo','discussions.created_at','discussions.updated_at','discussions.message')
        ->crossJoin($myaccount.'.users as a1','discussions.user_id','a1.id')
        // ->whereRaw('participant.id = b1.user_id')
        ->where('discussions.syllabus_id','=',$check)
        ->orderBy('discussions.created_at', 'ASC')->paginate(10);
        return response()->json($discussion);
        
        
        $data = Discussion::where('syllabus_id',$check)->paginate(10);
        return response()->json($data);
    }

    public function store(){
        $myaccount = config()->get('database.connections.my-account.database');
        $assessment = config()->get('database.connections.assessment.database');
        
        $request = Request();
        $check = $request->has('syllabus_id')?$request->syllabus_id:null;

        if($this->verify($check)){

            $data = new Discussion;
            $data->syllabus_id = $check;
            $data->user_id = config()->get('global.user_id');
            $data->message = $request->message;
            $data->save();
            $datamain = Discussion::select('discussions.id','discussions.syllabus_id as syllabus_id','a1.name as name','a1.username','a1.photo','discussions.created_at','discussions.updated_at','discussions.message')
            ->crossJoin($myaccount.'.users as a1','discussions.user_id','a1.id')
            ->where('discussions.id','=',$data->id)->first();
            return response()->json($datamain,200);
        }else{
            return response()->json(['message' => 'This user has no relation with this module','code'=>400],500);
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
    
}