<?php

namespace App\Repositories\AdminSettings;
use App\Models\AdminSettings\Degree;

use App\Interfaces\AdminSettings\DegreeRepositoryInterface;


class DegreeRepository implements DegreeRepositoryInterface 
{
    public function degreeByLevel(int $level_id)
    {
        $res = Degree::where('education_level_id',$level_id)->orderBy('id','DESC')->customPaginate();
    	return response()->json($res);   
    }
    
    public function addDegree(array $request)
    {
        $degree = new Degree;
        
        $degree->title = $request['title'];
        $degree->bn_title = $request['bn_title'];
        $degree->weight = $request['weight'];
        $degree->order = $request['order'];
        $degree->status = $request['status'];
        $degree->education_level_id  = $request['education_level_id'];
        $degree->created_by = config()->get('global.user_id');
        //$degree->updated_by = config()->get('global.user_id');
        
        if($degree->save()){
            return response()->json([
                'message' => 'Degree added successfully',
                'data'  => $degree
            ]);

        }
    }
    
    public function updateDegree(array $request)
    {
        $degree = Degree::find($request['id']);
       
        $degree->title = $request['title'];
        $degree->bn_title = $request['bn_title'];
        $degree->weight = $request['weight'];
        $degree->order = $request['order'];
        $degree->status = $request['status'];
        $degree->education_level_id  = $request['education_level_id'];
        //$degree->created_by = config()->get('global.user_id');
        $degree->updated_by = config()->get('global.user_id');


        if($degree->update()){
            return response()->json([
                'message' => 'Successfully updated',
                'data'   => $degree
            ],201);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

    

    public function deleteDegree(int $id)
    {
        $del = Degree::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted degree'],200);
        }else{
            return response()->json(['message' => 'Degree to be deleted not found'],404);
        }
    }

}