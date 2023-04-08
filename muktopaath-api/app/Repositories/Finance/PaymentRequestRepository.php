<?php

namespace App\Repositories\Finance;

use App\Models\Finance\PaymentRequest;
use App\Interfaces\Finance\PaymentRequestRepositoryInterface;

class PaymentRequestRepository implements PaymentRequestRepositoryInterface
{
    public function allPaymentRequest()
    {
        $res = PaymentRequest::customPaginate();
        return response()->json($res);
    }
    
     public function addPaymentRequest($request)
    {
        $var = new PaymentRequest;
        
        $var->balance_id = $request['balance_id'];
        $var->request_amount = $request['request_amount'];
        $var->status = 0;
        $var->requested_by = config()->get('global.user_id');
        
        if($var->save()){
            return response()->json([
                'message' => 'New request sent successfully',
                'data'  => $var
            ],200);

        }
        else{
            return response()->json([
                'message' => 'Failed!!',
                'data'  => $var
            ],404);
        }
    }

    public function updatePaymentRequest($request)
    {
        $var = PaymentRequest::find($request['id']);
        
        $var->status = $request['status'];
        $var->granted_by = config()->get('global.user_id');
        
        if($var->save()){
            return response()->json([
                'message' => 'Payment request granted successfully',
                'data'  => $var
            ],200);

        }
        else{
            return response()->json([
                'message' => 'Failed!!',
                'data'  => $var
            ],404);
        }
    }

    public function deletePaymentRequest(int $id)
    {
        $del = PaymentRequest::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted balance data'],200);
        }else{
            return response()->json(['message' => 'Balance to be deleted not found'],404);
        }
    }   
}