<?php

namespace Subscription\Repositories;

use Subscription\Interfaces\PackageInterface;
use Subscription\Models\Package;
use DB;

class PackageRepository implements PackageInterface
{
    public function view(){

        $res = Package::with('thumbnail')->get();

        return response()->json($res);
    }

    public function store($request){

        $data = new Package;
        $data->title = $request['title'];
        $data->details = $request['details'];
        $data->summary = json_encode($request['summary']);
        $data->features = json_encode($request['features']);
        $data->product_id   =   $request['product_id'];
        $data->package_type   =   $request['package_type'];
        $data->user_limit     = $request['user_limit'];
        $data->product_limit     = $request['product_limit'];
        $data->file_id = $request['file_id'];
        $data->type = $request['type'];
        $data->price_type = $request['price_type'];
        $data->price = $request['price'];
        $data->save();


        return response()->json(['message' => 'New Package created successfully',
            'data' => $data->with('thumbnail')->first()]);
    }

    public function update($request){

        $data =  Package::find($request['id']);
        $data->title = $request['title'];
        $data->details = $request['details'];
        $data->summary = json_encode($request['summary']);
        $data->features = json_encode($request['features']);
        $data->product_id   =   $request['product_id'];
        $data->package_type   =   $request['package_type'];
        $data->user_limit     = $request['user_limit'];
        $data->product_limit     = $request['product_limit'];
        $data->file_id = $request['file_id'];
        $data->type = $request['type'];
        $data->price_type = $request['price_type'];
        $data->price = $request['price'];
        $data->update();


        return response()->json(['message' => 'Package updated successfully',
            'data' => $data->with('thumbnail')->first()]);
    }

    public function show($id){
        $data = Package::with('thumbnail')->where('id',$id)->first();

        return response()->json($data);
    }



    public function delete($id){
        $data = Package::find($id);

        if($data){
            $data->delete();
            return response()->json(['message' => 'Package deleted successfully',
            'data' => $data]);
        }
    }
    
}