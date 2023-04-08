<?php

namespace App\Repositories\AdminSettings;
use App\Models\AdminSettings\EducationLevel;

use App\Interfaces\AdminSettings\EduLevelRepositoryInterface;


class EduLevelRepository implements EduLevelRepositoryInterface
{
    public function allLevel()
    {
        $eduLevel = EducationLevel::when(Request()->search, function ($query, $field) {
            return $query->where(function($q) use($field){
                $q->where('education_levels.title','like','%'.$field.'%')
                ->orWhere('education_levels.bn_title','like','%'.$field.'%');
            });
        })->orderBy('id','DESC')->customPaginate();
    	return response()->json($eduLevel);
    }
    
    public function addEduLevel(array $request)
    {
        $eduLevel = new EducationLevel;
        
        $eduLevel->title = $request['title'];
        $eduLevel->bn_title = $request['bn_title'];
        $eduLevel->weight = $request['weight'];
        $eduLevel->order = $request['order'];
        $eduLevel->status = $request['status'];
        $eduLevel->created_by = config()->get('global.user_id');
        //$eduLevel->updated_by = config()->get('global.user_id');
        
        if($eduLevel->save()){
            return response()->json([
                'message' => 'Education Level added successfully',
                'data'  => $eduLevel
            ]);

        }
    }
    
    public function updateEduLevel(array $request)
    {
        $eduLevel = EducationLevel::find($request['id']);
       
        $eduLevel->title = $request['title'];
        $eduLevel->bn_title = $request['bn_title'];
        $eduLevel->weight = $request['weight'];
        $eduLevel->order = $request['order'];
        $eduLevel->status = $request['status'];
        //$eduLevel->created_by = config()->get('global.user_id');
        $eduLevel->updated_by = config()->get('global.user_id');


        if($eduLevel->update()){
            return response()->json([
                'message' => 'Successfully updated',
                'data'   => $eduLevel
            ],201);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

    

    public function deleteEduLevel(int $id)
    {
        $del = EducationLevel::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted Education level'],200);
        }else{
            return response()->json(['message' => 'Education Level to be deleted not found'],404);
        }
    }

}