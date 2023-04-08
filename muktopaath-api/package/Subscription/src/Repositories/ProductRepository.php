<?php

namespace Subscription\Repositories;

use Subscription\Interfaces\ProductInterface;
use Subscription\Models\Product;
use DB;

class ProductRepository implements ProductInterface
{
    public function view(){

        $res = Product::with('thumbnail')->get();

        return response()->json($res);
    }

    public function store($request){

        $data = new Product;
        $data->title = $request['title'];
        $data->summary = json_encode($request['summary']);
        $data->file_id = $request['file_id'];
        $data->save();


        return response()->json(['message' => 'New product created successfully',
            'data' => $data->with('thumbnail')->first()]);
    }


    public function update($request){

        $data = Product::find($request['id']);
        $data->title = $request['title'];
        $data->summary = json_encode($request['summary']);
        $data->file_id = $request['file_id'];
        $data->update();


        return response()->json(['message' => 'product updated successfully',
            'data' => $data->with('thumbnail')->first()]);
    }

    public function delete($id){
        $data = Product::find($id);

        if($data){
            $data->delete();
            return response()->json(['message' => 'Product deleted successfully',
            'data' => $data]);
        }
    }
    
}