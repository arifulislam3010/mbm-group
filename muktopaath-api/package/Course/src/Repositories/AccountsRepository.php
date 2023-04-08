<?php

namespace Muktopaath\Course\Repositories;

use Muktopaath\Course\Interfaces\AccountsInterface;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Models\Course\Payment;
use DB;

class AccountsRepository implements AccountsInterface
{

    public function payments(){

        $db = config()->get('database.connections.my-account.database');

        $res = CourseEnrollment::Select('cb.course_alias_name','users.name','users.email','users.phone','o.*')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('orders as o','o.id','course_enrollments.order_id')
                ->join($db.'.users','users.id','o.user_id')
                ->where('o.payment_status',1)
                ->where('cb.owner_id',config()->get('global.owner_id'))
                ->paginate(10);

        return $res;
    }

    public function payment_status($id){

        $order_payments = CourseEnrollment::select(DB::raw('SUM(o.amount) as total_amount'))
            ->join('orders as o','o.id','course_enrollments.order_id')
            ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
            ->where('o.payment_status',1)
            ->where('cb.id',$id)
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->value('total_amount');

        $pending = Payment::select(DB::raw('SUM(payments.amount) as total_amount'))
            ->where('owner_id',config()->get('global.owner_id'))
            ->where('payments.course_batch_id',$id)
            ->where('status',0)
            ->value('total_amount');

        $withdrawed = Payment::select(DB::raw('SUM(payments.amount) as total_amount'))
            ->where('owner_id',config()->get('global.owner_id'))
            ->where('payments.course_batch_id',$id)
            ->where('status',1)
            ->value('total_amount');

        $available = $order_payments - $withdrawed;

        return response()->json([
            'available' => $available,
            'pending'   => $pending
        ]);
    }

    public function batch_payments(){

        $res = CourseBatch::Select('course_batches.title as batch_name','c.title as course_name','course_batches.id',DB::raw('SUM(o.amount) as total_amount'))
                ->join('courses as c','c.id','course_batches.course_id')
                ->join('course_enrollments as ce','ce.course_batch_id','course_batches.id')
                ->join('orders as o','o.id','ce.order_id')
                ->where('o.payment_status',1)
                ->where('course_batches.owner_id',config()->get('global.owner_id'))
                ->groupBy('course_batches.id')
                ->with('withdrawed')
                ->with(['pending' => function($query){
                   $query->sum('amount');
                }])
                ->paginate(10);

        return response()->json($res);
    }

    //approve function which set status =1 from payments table by id
    public function approve($id){
        $payment = Payment::find($id); 
        $payment->status = 1;
        $payment->save();
        return response()->json(['data' => $payment, 'success' => true]);
    }

    public function reject($id){
        $payment = Payment::find($id); 
        $payment->status = 2;
        $payment->save();
        return response()->json(['data' => $payment, 'success' => true]);
    }

    //delete function which deletes payment by id
    public function delete($id){
        $payment = Payment::find($id); 
        $payment->delete();
        return response()->json(['data' => $payment, 'success' => true]);
    }

    public function overall_transactions(){

        $res = Order::select(DB::raw('SUM(amount) as total_amount'))
                ->value('total_amount');

        $payment = Payment::select(DB::raw('SUM(amount) as partner_amount'))
                    ->where('status',1)
                    ->value('partner_amount');

        $due = Payment::select(DB::raw('SUM(amount) as due'))
                    ->where('status',0)
                    ->value('due');

        $data = [];
        $data['total_amount'] = $res;
        $data['partner_amount'] = $payment;
        $data['due'] = $due;

        return $data;
    }
    

    public function view_all_requests(){
        // db config myaccount
        $db = config()->get('database.connections.my-account.database');
        //query to return course_batch title, course title, institutions title and amount from payments table
        $res = Payment::Select('payments.id','payments.mp_amount','course_batches.title as batch_name','institution_infos.institution_name','c.title as course_name','payments.amount','payments.status','payments.created_at')                    
                ->join('course_batches','course_batches.id','payments.course_batch_id')
                ->join('courses as c','c.id','course_batches.course_id')
                ->join($db.'.institution_infos','institution_infos.id','course_batches.owner_id')
                ->where('payments.owner_id',config()->get('global.owner_id'))
                ->orderBy('payments.id','DESC')
                ->paginate(10);

        return $res;
    }

    //request function to store payment
    public function storePayment($request){

        $order_payments = CourseEnrollment::select(DB::raw('SUM(o.amount) as total_amount'))
                ->join('orders as o','o.id','course_enrollments.order_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->where('o.payment_status',1)
                ->when($request->course_id,function($query) use($request) {
                        return $query->where('cb.course_id',$request->course_id);
                    })
                ->when($request->course_batch_id,function($query) use($request) {
                        return $query->where('cb.id',$request->course_batch_id);
                    })
                ->where('cb.owner_id',config()->get('global.owner_id'))
                ->value('total_amount');



        $payments = Payment::select(DB::raw('SUM(payments.amount) as total_amount'))
                    ->where('owner_id',config()->get('global.owner_id'))
                    ->when($request->course_id,function($query) use($request) {
                            return $query->where('payments.course_id',$request->course_id);
                        })
                    ->when($request->course_batch_id,function($query) use($request) {
                            return $query->where('payments.course_batch_id',$request->course_batch_id);
                        })->value('total_amount');

        $available = $order_payments - $payments;

        if($available<$request->amount){
            return response()->json(['error' => 'requested amount not available'],404);
        }

        $payment = new Payment;
        $payment->amount = $request->amount;
        $payment->mp_amount = ($request->amount/100)*20;
        $payment->pm_number = $request->pm_number;
        $payment->course_id = $request->course_id;
        $payment->course_batch_id = $request->course_batch_id;
        $payment->owner_id = config()->get('global.owner_id');
        $payment->created_by = config()->get('global.user_id');
        $payment->pm_number = $request->pm_number;
        $payment->save();

        //return payment with success message
        return response()->json(['data' => $payment,
        'success'=>'Payment Successfully Added']);
    }
    
}