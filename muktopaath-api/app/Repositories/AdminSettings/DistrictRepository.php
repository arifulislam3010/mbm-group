<?php

namespace App\Repositories\AdminSettings;

use App\Models\Model
use App\Interfaces\

class DistrictRepository implements DistrictRepositoryInterface
{
     public function add(array $request)
    {
        $var = new Model;
        
        $var->title = $request['title'];
        $var->created_by = config()->get('global.user_id');
        $var->updated_by = config()->get('global.user_id');
        
        if($var->save()){
            return response()->json([
                'message' => ' added successfully',
                'data'  => $var
            ]);

        }
    }
    
    public function update(array $request)
    {
        $var = Model::find($request['id']);
       
        $var->title = $request['title'];
        $var->created_by = config()->get('global.user_id');
        $var->updated_by = config()->get('global.user_id');


        if($var->update()){
            return response()->json([
                'message' => 'Successfully updated',
                'data'   => $var
            ],201);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

    

    public function delete(int $id)
    {
        $del = Model::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted '],200);
        }else{
            return response()->json(['message' => ' to be deleted not found'],404);
        }
    }
}