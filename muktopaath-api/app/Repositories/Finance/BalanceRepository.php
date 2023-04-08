<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Balance;
use App\Interfaces\Finance\BalanceRepositoryInterface;

class BalanceRepository implements BalanceRepositoryInterface
{
    public function allBalance()
    {
        $res = Balance::customPaginate();
        return response()->json($res);
    }
     public function addBalance($request)
    {
        $var = new Balance;
        
        $var->total_earn = $request['total_earn'];
        $var->withdrawn = $request['withdrawn'];
        $var->availabe_withdrawn = $request['availabe_withdrawn'];
        $var->pending_clearance = $request['pending_clearance'];
        
        if($var->save()){
            return response()->json([
                'message' => 'New balance added successfully',
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

    public function updateBalance($request)
    {
        $var = Balance::find($request['id']);
        
        $var->total_earn = $request['total_earn'];
        $var->withdrawn = $request['withdrawn'];
        $var->availabe_withdrawn = $request['availabe_withdrawn'];
        $var->pending_clearance = $request['pending_clearance'];
        
        if($var->save()){
            return response()->json([
                'message' => 'Balance updated successfully',
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

    public function deleteBalance(int $id)
    {
        $del = Balance::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted balance data'],200);
        }else{
            return response()->json(['message' => 'Balance to be deleted not found'],404);
        }
    }
    
    
}