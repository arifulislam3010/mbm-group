<?php

namespace App\Repositories\AdminSettings;
use App\Models\AdminSettings\Category;
use App\Models\AdminSettings\DisabilityType;

use App\Interfaces\AdminSettings\CategoryRepositoryInterface;
use App\Http\Resources\AdminSettings\CategoryInfo;


class CategoryRepository implements CategoryRepositoryInterface 
{
    public function allCategories()
    {

        $category = Category::when(Request()->search, function ($query, $field) {
            return $query->where(function($q) use($field){
                $q->where('categories.title','like','%'.$field.'%')
                ->orWhere('categories.bn_title','like','%'.$field.'%');
            });
        })->withCount('total_course')
        ->orderBy('id','DESC')->customPaginate();

        if(Request()->with_data){
            return CategoryInfo::collection($category);
        }


        return response()->json($category);
    }

    public function disabilities(){
        
        $res = DisabilityType::all();
        return response()->json($res);
    }
    
    public function addCategory(array $request)
    {
        $category = new Category;
        
        $category->title = $request['title'];
        $category->bn_title = $request['bn_title'];
        $category->image = $request['image'];
        $category->status = $request['status'];
        $category->favourite = $request['favourite'];
        $category->order_number = $request['order_number'];
        $category->created_by = config()->get('global.user_id');
        //$category->updated_by = config()->get('global.user_id');
        
        if($category->save()){
            return response()->json([
                'message' => 'Category added successfully',
                'data'  => $category
            ]);

        }
    }
    
    public function updateCategory(array $request)
    {
        $category = Category::find($request['id']);

       
        $category->title = $request['title'];
        $category->bn_title = $request['bn_title'];
        $category->image = $request['image'];
        $category->status = $request['status'];
        $category->favourite = $request['favourite'];
        $category->order_number = $request['order_number'];
        //$category->created_by = config()->get('global.user_id');
        $category->updated_by = config()->get('global.user_id');


        if($category->update()){
            return response()->json([
                'message' => 'Successfully updated',
                'data'   => $category
            ],201);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

    

    public function deleteCategory(int $id)
    {
        $del = Category::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted category'],200);
        }else{
            return response()->json(['message' => 'Category to be deleted not found'],404);
        }
    }

}