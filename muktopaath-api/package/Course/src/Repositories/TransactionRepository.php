<?php

namespace Muktopaath\Course\Repositories;

use Muktopaath\Course\Interfaces\TransactionInterface;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Models\Course\Translation;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Models\Course\Transaction;
use DB;

class TransactionRepository implements TransactionInterface
{

    public function create($request){

        $data = new Transaction;
        $data->course_enrollment_id = $request['enrollment_id'];
        $data->course_batch_id = $request['course_batch_id'];
        $data->syllabus_id   = $request['syllabus_id'];
        $data->person  = $request['person'];
        $data->type = $request['type'];
        $data->amount = $request['amount'];
        $data->created_by = config()->get('global.user_id');
        $data->save();

        $result = Transaction::select('transactions.id as tr_id','transactions.course_enrollment_id as id')
                  ->where('id',$data->id)
                  ->first();

        return response()->json(['data' => $result, 'mesaage' => 'transaction successful.']);

    }

    public function delete($id){
        
        $res = Transaction::find($id);
        $res->delete();

        return response()->json(['data' => $res->course_enrollment_id, 'message' => 'old transaction removed successfully']);
    }

    
}