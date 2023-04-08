<?php

namespace App\Http\Controllers\AdminSettings;

use App\Http\Controllers\Controller;
use App\Models\AdminSettings\EducationLevel;
use App\Models\AdminSettings\EduSubLevel;
use Illuminate\Http\Request;

class EduSubLevelController extends Controller
{
    
    public function index(Request $request)
    {
        $id = (isset($request['id']))?$request['id']:null;
        $res = EduSubLevel::when($id, function($q) use($id){return $q->where('edu_sub_levels.id',$id);})
        ->customPaginate();
        return response()->json($res);
    }

    
    public function levelsWithSub()
    {
        $res = EducationLevel::with('edusublevels')
        // ->where('edu_sub_levels.edu_level_id', '=', 'education_levels.id')
        ->get();
        return response()->json($res);
    }

    
    public function store(Request $request)
    {
        $var = new EduSubLevel;
        
        $var->title = $request['title'];
        $var->bn_title = $request['bn_title'];
        $var->edu_level_id = $request['edu_level_id'];
        $var->created_by = config()->get('global.user_id');
        
        if($var->save()){
            return response()->json([
                'message' => 'New Education sub level added successfully',
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

    public function update(Request $request)
    {
        $var = EduSubLevel::find($request['id']);
        
        $var->title = $request['title'];
        $var->bn_title = $request['bn_title'];
        $var->edu_level_id = $request['edu_level_id'];
        $var->updated_by = config()->get('global.user_id');
        
        if($var->save()){
            return response()->json([
                'message' => 'Education sub level updated successfully',
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

    public function destroy($id)
    {
        $del = EduSubLevel::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted balance data'],200);
        }else{
            return response()->json(['message' => 'Balance to be deleted not found'],404);
        }
    }
}