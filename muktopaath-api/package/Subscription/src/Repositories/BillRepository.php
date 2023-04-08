<?php

namespace Subscription\Repositories;

use Subscription\Interfaces\BillInterface;
use Subscription\Models\PackageBill;
use Carbon\Carbon;
use DB;

class BillRepository implements BillInterface
{
    public function view(){

        $res = PackageBill::with('thumbnail')->get();

        return response()->json($res);
    }

    public function store($request){

        $data = new PackageBill;
        $data->package_id = $request['package_id'];
        $data->month   =   $request['month'];
        $data->amount   =   $request['amount'];
        $data->owner_id = config()->get('global.owner_id');
        if($data->save()){
            $update = PackageBill::find($data->id);
            $update->invoice_no = str_pad($data->id, 8, '0', STR_PAD_LEFT);
            $update->update();
        }

        return response()->json(['message' => 'Bill created',
            'data' => $data]);
    }

    public function approve($request){

        $data = PackageBill::find($request['id']);
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