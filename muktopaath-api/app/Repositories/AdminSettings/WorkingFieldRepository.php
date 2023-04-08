<?php

namespace App\Repositories\AdminSettings;
use App\Models\AdminSettings\WorkingField;

use App\Interfaces\AdminSettings\WorkingFieldRepositoryInterface;


class WorkingFieldRepository implements WorkingFieldRepositoryInterface 
{
    public function addWf(array $request)
    {
        $wf = new WorkingField;
        
        $wf->title = $request['title'];
        $wf->bn_title = $request['bn_title'];
        $wf->status = $request['status'];
        $wf->order_number  = $request['order_number'];
        $wf->profession_id   = $request['profession_id'];
        $wf->created_by = config()->get('global.user_id');
        //$wf->updated_by = config()->get('global.user_id');
        
        if($wf->save()){
            return response()->json([
                'message' => 'Working field added successfully',
                'data'  => $wf
            ]);

        }
    }
    
    public function updateWf(array $request)
    {
        $wf = WorkingField::find($request['id']);
       
        $wf->title = $request['title'];
        $wf->bn_title = $request['bn_title'];
        $wf->status = $request['status'];
        $wf->order_number  = $request['order_number'];
        $wf->profession_id   = $request['profession_id'];
        //$wf->created_by = config()->get('global.user_id');
        $wf->updated_by = config()->get('global.user_id');


        if($wf->update()){
            return response()->json([
                'message' => 'Successfully updated',
                'data'   => $wf
            ],201);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

    

    public function deleteWf(int $id)
    {
        $del = WorkingField::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted working field'],200);
        }else{
            return response()->json(['message' => 'Working field to be deleted not found'],404);
        }
    }

}
