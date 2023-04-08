<?php

namespace App\Repositories\EventManager;

use App\Models\EventManager\Material;
use App\Interfaces\EventManager\MaterialRepositoryInterface;

class MaterialRepository implements MaterialRepositoryInterface
{
    public function allMaterials()
    {
       $res = Material::all();
       return response()->json($res);
    }
   
   public function createMaterial($request){
      $material = new Material;
      
      $material->event_id = $request['event_id'];
      $material->user_id = $request['user_id'];
      $material->material_url = $request['material_url'];

      if($material->save()){
         return response()->json([
           'message' => 'Material added successfully',
           'data'  => $material
         ]);
      }
   }

   public function updateMaterial($request){
      $material = Material::find($request['id']);
      
      $material->event_id = $request['event_id'];
      $material->user_id = $request['user_id'];
      $material->material_url = $request['material_url'];

      if($material->update()){
        return response()->json([
            'message' => 'Successfully updated',
            'data'   => $material
        ],201);
    }else{
        return response()->json([
            'message' => 'Something went wrong!'
        ], 400);
    }
   }

   public function deleteMaterial($id){
      $material = Material::find($id);
      if($material){
        $material->delete();
        return response()->json(['message' =>'Material successfully deleted'],200);
     }else{
        return response()->json(['message' => 'Material to be deleted not found'],404);
    }
   }   
}