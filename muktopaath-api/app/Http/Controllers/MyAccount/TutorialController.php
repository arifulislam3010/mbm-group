<?php

namespace App\Http\Controllers\MyAccount;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Tutorial\Tutorial;
use App\Http\Resources\Tutorials as TutorialResource;
use Validator;
use Image;
use File;
use Auth;
use App\User;
use App\Models\User\InstitutionInfo;
use App\Lib\FileUpload;

class TutorialController extends Controller
{
    
    public function index(Request $request)
    {
        $search       = ($request->has('search'))?$request['search']:null;
        $limit       = ($request->has('limit'))?$request['limit']:8;
        $order       = ($request->has('order'))?$request['order']:null;
        $type       = ($request->has('type'))?$request['type']:null;
        $search_type = ($request->has('search_type'))?$request['search_type']:null;
    	
        

       if($request['search_type']=='favourite')
        {
            $data = Tutorial::with('CreatedBy')->orderBy('tread', 'desc');
        
            return TutorialResource::collection($data);
        }
        if($request['search_type']=='search')
        {
           $data = Tutorial::when($search, function($q) use($search){return $q->where('title','like',"%$search%");})
           ->when($type, function($q) use($id){return $q->where('type',$search_type);})
           ->where('status',1)->orderBy('created_at', $order)->paginate($limit);;
        
           return TutorialResource::collection($data);
        }
        
        $data = Tutorial::select('tutorial_upload.*')->where('status',1)->with('CreatedBy')->orderBy('created_at', 'desc')->paginate($limit);
        // return response()->json($data);

        return TutorialResource::collection($data);

        // $tutorial = Tutorial::where('created_by',Auth::user())->paginate(6);

        // return TutorialResource::collection($tutorial);
    
    }
    
    public function tutorialget()
    {
      $tutorial_get = Tutorial::where('created_by',Auth::user()->id)->paginate(6);

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

        // $data = $request->all();
        // $user_id = Auth::id();
        // $validatedData = $request->
        // validate([
        //     'title'             => 'required',
        // ]);
        // if($data['id']!='' || $data['id']!=null){
        //     $insert  = Tutorial::find($data['id']);
        //     $insert->updated_by  = Auth::user()->id; 
        // }else{
        //     $insert  = new Tutorial();
        //     $insert->created_by  = Auth::user()->id;
        //     $insert->updated_by  = Auth::user()->id;
        // }
        // if ($request->hasFile('video'))
        // {
        //     $file           = $request->file('video');
        //     $prefix         = 'tutorial';
        //     $path           = 'tutorial/';

        //     $file_upload = new FileUpload;
        //     $upload = $file_upload->upload($file, $prefix, $path);
        //     $video = $upload['file_name'];
        //     $type  = $upload['type'];
            
        // }
        // else
        // {
            
        //     $video = null;
        // }
        
        $insert = new Tutorial;
        $insert->title = $request['title'];
        $insert->category_id = $request['category_id'];
        $insert->description = $request['description'];
        $insert->video = $request['file_name'];
        $insert->status = $request['status'];
        $insert->type = $request['type'];
        
        // if($request['id']!='' || $request['id']!=null){
        //     if($video!=null){
        //          $insert->video = $video;
        //     }

        // }else{
        //      $insert->video = $video;
        // }
         
            if($insert->save()){

                return $insert;
                $data = [
                    'status'    => 'Success',
                    'code'      => '200',
                    'data'      => $insert,
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
    public function update(Request $request)
    {  
        $data = $request->all();
        $user_id = Auth::id();
        $validatedData = $request->
        validate([
            'title'             => 'required',
        ]);
        if($data['id']!='' || $data['id']!=null){
            $update  = Tutorial::find($data['id']);
            $update->updated_by  = Auth::user()->id; 
        }else{
            $update  = new Tutorial();
            $update->created_by  = Auth::user()->id;
            $update->updated_by  = Auth::user()->id;
        }
        // if ($request->hasFile('video'))
        // {
        //     $file           = $request->file('video');
        //     $prefix         = 'tutorial';
        //     $path           = 'tutorial/';

        //     $file_upload = new FileUpload;
        //     $upload = $file_upload->upload($file, $prefix, $path);
        //     $video = $upload['file_name'];
        //     $type  = $upload['type'];
            
        // }
        // else
        // {
            
        //     $video = null;
        // }
        $update->title = $data['title'];
        $update->category_id = $request['category_id'];
        $update->description = $data['description'];
        $update->video = $data['video'];
        $update->status = $data['status'];
        $update->type = $data['type'];
        // if($data['id']!='' || $data['id']!=null){
        //     if($video!=null){
        //          $update->video = $video;
        //     }

        // }else{
        //      $update->video = $video;
        // }
         
            if($update->save()){

                return $update;
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
        $del = Tutorial::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted tutorial'],200);
        }else{
            return response()->json(['message' => 'Tutorial to be deleted not found'],404);
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
