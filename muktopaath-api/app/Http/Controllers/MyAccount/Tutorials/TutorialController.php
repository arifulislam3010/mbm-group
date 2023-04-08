<?php

namespace App\Http\Controllers\MyAccount\Tutorials;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Myaccount\Tutorial\Tutorial;
use App\Http\Resources\Tutorials as TutorialResource;
use Validator;
use Image;
use File;
use Auth;
use App\User;
use App\Models\Myaccount\InstitutionInfo;
use App\Lib\FileUpload;

class TutorialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function index(Request $request)
    {

        $search       = ($request->has('search'))?$request['search']:null;
        $limit       = ($request->has('limit'))?$request['limit']:9;
        $order       = ($request->has('filter'))?$request['filter']:'DESC';
        $type        = ($request->has('type'))?$request['type']:null;
    	$is_published = ($request->has('publish'))?$request['publish']:1;
        

        //type,cat_id,order,limit,search
        //popular-1,favourite-2
       if($request['type']==1 || $request['type']==2)
        {
            $data = Tutorial::when($search, function($q) use($search){
                return $q->where('title','like',"%$search%");
            })
            ->when($request->category_id, function($q) use($request){
                return $q->where('category_id',$request->category_id);
            })
            ->when($is_published, function($q) use($is_published){
                if($is_published=='1'){
                    return $q->where('status','=',1);
                }
                else{
                   return $q->where('status','=',0); 
                }
            })
            ->orderBy('tread', $order)
            ->paginate($limit);
        
            return TutorialResource::collection($data);
        }
        if(config()->get('global.owner_id')){
           $data = Tutorial::select('tutorial_upload.*')->when($request->category_id, function($q) use($request){return $q->where('category_id',$request->category_id);})->with('CreatedBy')->orderBy('created_at', 'desc')->paginate(8); 
        }else{
            $data = Tutorial::select('tutorial_upload.*')
            ->when($request->category_id, function($q) use($request){
                return $q->where('category_id',$request->category_id);
            })->when($search, function($q) use($search){
                return $q->where('title','like',"%$search%");
            })
            ->when($is_published, function($q) use($is_published){
                if($is_published=='1'){
                    return $q->where('status','=',1);
                }
                else{
                   return $q->where('status','=',0); 
                }
            })->with('CreatedBy')
                ->orderBy('created_at', $order)->paginate($limit); 

        }
        
        
        // return response()->json($data);

        return TutorialResource::collection($data);

        // $tutorial = Tutorial::where('created_by',Auth::user())->paginate(6);

        // return TutorialResource::collection($tutorial);
    
    }

    public function publish(Request $request){

        $messsages = array(
            'required'=>'required',
           );

            $rules = array(
               'id'          =>'required'
            );

            $validator = Validator::make($request->all(),$rules,$messsages);

            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
            }

        $res = Tutorial::find($request['id']);
        if($res->status == 1){
            $res->status = 0;
        }else{
            $res->status = 1;
        }
        $res->update();

        return response()->json(['message' => 'Tutorial published','data' => $res]);
    }

      public function tutorialget()
    {
      $tutorial_get = Tutorial::where('created_by',config()->get('global.user_id'))->paginate(6);

        return TutorialResource::collection($tutorial_get);
    }

     public function details($id){

        $tutorial_details = Tutorial::where('id',$id)->with('CreatedBy')->first();
        $tutorial_details->tread = $tutorial_details->tread+1;
        $tutorial_details->save();
        if($tutorial_details){
            return response()->json([
                'api_info'    => 'Tutorial',
                //'data'        => $tutorial_details,
                'data'        => new TutorialResource($tutorial_details),
            ] , 200);
        }
        else{
            return response()->json(['errors' =>['message'=>'No Data']], 401);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request)
    {   

        $data = $request->all();
        $user_id = config()->get('global.user_id');

        $messsages = array(
            'required'=>'required',
           );

            $rules = array(
                'title'             => 'required',
                'category_id'       => 'required'
            );

        $validator = Validator::make($request->all(),$rules,$messsages);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }

        if($data['id']!='' || $data['id']!=null){
            $insert  = Tutorial::find($data['id']);
            $insert->updated_by  = config()->get('global.user_id'); 
        }else{
            $insert  = new Tutorial();
            $insert->created_by  = config()->get('global.user_id');
            $insert->updated_by  = config()->get('global.user_id');
        }
        if ($request->hasFile('video'))
        {
            $file           = $request->file('video');
            $prefix         = 'tutorial';
            $path           = 'tutorial/';

            $file_upload = new FileUpload;
            $upload = $file_upload->upload($file, $prefix, $path);
            $video = $upload['file_name'];
            $type  = $upload['type'];
            
        }
        else
        {
            
        $video = null;
        }
        $insert->title = $data['title'];
        $insert->description = $data['description'];
        $insert->file_id = $data['file_id'];
        $insert->thumbnail_id = $data['thumbnail_id'];
        $insert->category_id = $data['category_id'];
        $insert->duration = $data['duration'];
        $insert->status = $data['status'];
        $insert->type = isset($type)?$type:null;
        if($data['id']!='' || $data['id']!=null){
            if($video!=null){
                 $insert->video = $video;
            }

        }else{
             $insert->video = $video;
        }
         
            if($insert->save()){

                return new TutorialResource($insert);
                $data = [
                    'status'    => 'Success',
                    'code'      => '200',
                    'data'      => $insert,
                ];
    
                return response()->json(TutorialResource($data), 200);
    
            }else{
                $data = [
                    'status'  => 'error',
                    'code'    => '404',
                    'message' => 'Error occurred.',
                ];
    
                return response()->json($data, 404);
            }

    }

    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {  
        $data = $request->all();
        $user_id = config()->get('global.user_id');

        $messsages = array(
            'required'=>'required',
           );

            $rules = array(
                'title'    => 'required',
            );

        $validator = Validator::make($request->all(),$rules,$messsages);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }
        if($data['id']!='' || $data['id']!=null){
            $update  = Tutorial::find($id);
            $update->updated_by  = config()->get('global.user_id'); 
        }else{
            $update  = new Tutorial();
            $update->created_by  = config()->get('global.user_id');
            $update->updated_by  = config()->get('global.user_id');
        }
        if ($request->hasFile('video'))
        {
            $file           = $request->file('video');
            $prefix         = 'tutorial';
            $path           = 'tutorial/';

            $file_upload = new FileUpload;
            $upload = $file_upload->upload($file, $prefix, $path);
            $video = $upload['file_name'];
            $type  = $upload['type'];
            
        }
        else
        {
            
            $video = null;
        }
        $update->title = $data['title'];
        $update->description = $data['description'];
        $update->file_id = $data['file_id'];
        $update->duration = $data['duration'];
        $update->thumbnail_id = $data['thumbnail_id'];
        $update->status = $data['status'];
        $update->type = isset($type)?$type:null;
        if($data['id']!='' || $data['id']!=null){
            if($video!=null){
                 $update->video = $video;
            }

        }else{
             $update->video = $video;
        }
         
            if($update->save()){

                return new TutorialResource($update);

                $data = [
                    'status'    => 'Success',
                    'code'      => '200',
                    'data'      => $update,
                ];
    
                return response()->json($data, 200);
    
            }else{
                $data = [
                    'status'  => 'error',
                    'code'    => '404',
                    'message' => 'Error occurred.',
                ];
    
                return response()->json($data, 404);
            }

    
    }
   public function delete($id)
    {
        $tutorials = Tutorial::find($id);
        if($tutorials->delete()){
            $data = [
                'status'    => 'Success',
                'code'      => '200',
                'data'      => $tutorials,
            ];
            return response()->json($data, 200);

        }else{
            $data = [
                'status'  => 'error',
                'code'    => '404',
                'message' => 'Error occurred.',
            ];

            return response()->json($data, 404);
        }
     
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
