<?php

namespace Subscription\Repositories;

use Subscription\Interfaces\CustomerPackageInterface;
use Subscription\Models\CustomerPackage;
use Carbon\Carbon;
use DB;

class CustomerPackageRepository implements CustomerPackageInterface
{
    public function view(){

        $res = CustomerPackage::all();
        return response()->json($res);

        return response()->json($res);
    }

    public function store($request){

        $data = new CustomerPackage;
        $data->package_id = $request['package_id'];
        $data->user_id   =   config()->get('global.user_id');
        $data->owner_id  =   config()->get('global.owner_id');
        if($data->save()){

            return response()->json(['message' => 'Customer package created',
            'data' => $data]);
        }

    }

    public function approve($request){

        $data = CustomerPackage::find($request['id']);
        $data->payment_status = 1;
        $data->payment_date =   Carbon::now();
        $data->update();

        return response()->json(['message' => 'Bill approved.',
        'data' => $data]);

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