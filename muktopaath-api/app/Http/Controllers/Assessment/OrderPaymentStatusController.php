<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Order;
use App\Models\Finance\Balance;

class OrderPaymentStatusController extends Controller
{
    public function updatePaymentStatus(Request $request)
    {
        $var = Order::find($request['id']);
        
        $var->payment_status = $request['payment_status'];
        $var->save();
        
        if($var->save()){
            return response()->json([
                'message' => 'Payment approved updated successfully',
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
}