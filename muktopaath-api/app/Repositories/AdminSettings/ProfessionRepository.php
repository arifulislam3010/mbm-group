<?php

namespace App\Repositories\AdminSettings;
use App\Models\AdminSettings\Profession;
use App\Models\AdminSettings\WorkingField;

use App\Interfaces\AdminSettings\ProfessionRepositoryInterface;


class ProfessionRepository implements ProfessionRepositoryInterface 
{
    public function allProfession(){
        $res = Profession::when(Request()->search, function ($query, $field) {
            return $query->where(function($q) use($field){
                $q->where('professions.title','like','%'.$field.'%')
                ->orWhere('professions.bn_title','like','%'.$field.'%');
            });
        })->whereNotIn('title',['Unemployed','Student'])->orderBy('id','ASC')->customPaginate();
        return response()->json($res);    
    }

    public function getfields($profession_id){
        $res = WorkingField::where('profession_id',$profession_id)->get();
        return response()->json($res);
    }
    
    public function addProfession(array $request)
    {
        $profession = new Profession;
        
        $profession->title = $request['title'];
        
        $profession->bn_title = $request['bn_title'];
        $profession->status = $request['status'];
        $profession->order_number  = $request['order_number'];
        $profession->created_by = config()->get('global.user_id');
        //$profession->updated_by = config()->get('global.user_id');
        
        if($profession->save()){
            return response()->json([
                'message' => 'Profession added successfully',
                'data'  => $profession
            ]);

        }
    }
    
    public function updateProfession(array $request)
    {
        $profession = Profession::find($request['id']);
       
        $profession->title = $request['title'];
        $profession->bn_title = $request['bn_title'];
        $profession->status = $request['status'];
        $profession->order_number = $request['order_number'];
        //$profession->created_by = config()->get('global.user_id');
        $profession->updated_by = config()->get('global.user_id');


        if($profession->update()){
            return response()->json([
                'message' => 'Successfully updated',
                'data'   => $profession
            ],201);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

    

    public function deleteProfession(int $id)
    {
        $del = Profession::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted profession'],200);
        }else{
            return response()->json(['message' => 'Profession to be deleted not found'],404);
        }
    }

}