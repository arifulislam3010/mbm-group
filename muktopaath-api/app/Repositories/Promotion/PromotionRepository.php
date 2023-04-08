<?php

namespace App\Repositories\Promotion;

use Illuminate\Http\Request;
use App\Repositories\Validation;
use App\Interfaces\Promotion\PromotionInterface;
use App\Models\Promotion\Advertisement;
use DB;
use Validator;

class PromotionRepository implements PromotionInterface
{

	public function list(){
		if(config()->get('global.owner_id')==1){
			$res = Advertisement::paginate(10);
		}else{
			$res = Advertisement::where('status',1)->paginate(10);
		}

		return response()->json($res);

	}

	public function showsingle($id){

		$res = Advertisement::where('id',$id)
			->with('categories','profession')
			->first();

		return response()->json($res);
	}

	public function show($request){
		
		$res = Advertisement::where('add_for',$request['ad_for'])
						->where('table_id',$request['table_id'])
						->first();

		return response()->json($res);
	}

	public function store($request){

		DB::beginTransaction();
			
		try {
			$res = new Advertisement;
			$res->title = $request['title'];
			$res->thumbnail = isset($request['thumbnail'])?$request['thumbnail']:null;
			$res->file_id = isset($request['file_id'])?$request['file_id']:null;
			$res->type = $request['type'];
			$res->description = $request['description'];
			$res->start_date = $request['start_date'];
			$res->end_date 	= $request['end_date'];
			$res->add_for 	= isset($request['add_for'])?$request['add_for']:null;
			$res->table_id 	= isset($request['table_id'])?$request['table_id']:null;
			$res->link 	= isset($request['link'])?$request['link']:null;
			$res->uuid = isset($request['uuid'])?$request['uuid']:null;
			$res->age_start = isset($request['age_start'])?$request['age_start']:null;
			$res->age_end 	= isset($request['age_end'])?$request['age_end']:null;
			$res->owner_id	= config()->get('global.owner_id');
			$res->user_id	= config()->get('global.user_id');
			$res->save();

			if($request['target_category']){
				$res->categories()->sync(($request['target_category']));
			}

			if($request['target_profession']){
				$res->professions()->sync(($request['target_profession']));
			}

			DB::commit();

			return response()->json(['message' => 'Advertise created successfully. wait for approval',
									'data' => $res]);

		}catch (\Exception $e) {
			DB::rollback();
			return response()->json(['message' => $e]);
			// something went wrong
		}

	}


	public function update($request){
		DB::beginTransaction();

		try {
			$res =  Advertisement::find($request['id']);
			$res->title = $request['title'];
			$res->thumbnail = isset($request['thumbnail'])?$request['thumbnail']:null;
			$res->file_id = isset($request['file_id'])?$request['file_id']:null;
			$res->type = $request['type'];
			$res->description = $request['description'];
			$res->start_date = $request['start_date'];
			$res->end_date 	= $request['end_date'];
			$res->add_for 	= isset($request['add_for'])?$request['add_for']:null;
			$res->table_id 	= isset($request['table_id'])?$request['table_id']:null;
			$res->link 		= isset($request['link'])?$request['link']:null;
			$res->uuid 		= isset($request['uuid'])?$request['uuid']:null;
			$res->age_start = isset($request['age_start'])?$request['age_start']:null;
			$res->age_end 	= isset($request['age_end'])?$request['age_end']:null;
			$res->owner_id	= config()->get('global.owner_id');
			$res->user_id	= config()->get('global.user_id');
			$res->update();

			if($request['target_category']){
	             $res->categories()->sync(json_decode($request['target_category']));
	        }

	        if($request['target_profession']){
	             $res->professions()->sync(json_decode($request['target_profession']));
	        }

	        DB::commit();

	        return response()->json(['message' => 'Advertise updated successfully.',
							'data' => $res]);
	        
	    }catch (\Exception $e) {
			DB::rollback();
			return response()->json(['message' => $e]);
		   
		    // something went wrong
		}

	}

	public function approve($id){
		$res = Advertisement::find($id);
		$res->status = 1;
		$res->update();

		return response()->json(['message' => 'approved','data' => $res]);
	}

	public function delete($id){
		$res = Advertisement::find($id);
		if($res){
			$res->delete();

			return response()->json(['message' => 'deleted successfully', 'data' => $res]);
		}
	}
}