<?php

namespace App\Http\Controllers\ContentBank\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ContentBank\LearningContent as LearningResource;
use App\Http\Resources\ContentBank\LearningContentShow as LearningResourceShow;
use App\Models\ContentBank\LearningContent;
use App\Models\Myaccount\Sharing;

use App\Models\ContentBank\Order;
use Illuminate\Support\Facades\Config;

use App\Models\ContentBank\OrderContent;
use Auth; 
use DB; 

class ContentBankController extends Controller
{  
    public function index(Request $request){

        if(!config()->get('global.owner_id')){
            $self = 1;
        }else{
            $self = 0;
        }


        if($request->type == 'assessment'){

            $query = LearningContent::query();

            if($request->title){
                $query->where('title', 'like', '%' . $request->title . '%');
            }

            if($request->cat_id){

                $query->where('cat_id',$request->cat_id);
            }

            else{

               $query->whereIn('content_type',['quiz','exam']); 
            }

            if(Config('global.owner_id')){
      
                $query->where('owner_id',Config('global.owner_id'));
            }
 
            return $query->paginate(10);

        }
        else if($request->type == 'content'){
        
            $data = LearningContent::when(Config('global.owner_id'), function ($query, $id) {
            $query->where('owner_id',$id);
        })->when($self==1, function ($query, $id) {
            $query->where('created_by',config()->get('global.user_id'));
        })->whereIn('content_type',['video','audio','document'])->orderby('id','DESC')->paginate(10);
        }
        else{
           $data = LearningContent::when($self==1, function ($query, $id) {
            $query->where('created_by',config()->get('global.user_id'));
        })->when(Config('global.owner_id'), function ($query, $id) {
            $query->where('owner_id',$id);
        })->orderby('id','DESC')->paginate(100); 
        }
        
        return LearningResource::collection($data);
    }


    public function myOtherPurchase(Request $request){



        $user_id = config()->get('global.user_id');

        $my_content = LearningContent::where('updated_by',$user_id)->paginate(100);
        $other_content = LearningContent::whereNotIn('updated_by',[$user_id])->paginate(100);
        $purchase_content = LearningContent::where('updated_by',100001)->paginate(100);
        $check = Sharing::where('table_name','learning_contents')
                        ->where('user_id',config()->get('global.user_id'))
                        ->pluck('table_id');

        $shared_content = LearningContent::wherein('id',$check)->paginate(100);


        
        return response()->json(['my_content' => $my_content, 'other_content' => $other_content,'purchase_content' => $purchase_content,'shared_content' => $shared_content],200);
    }

    public function list(Request $request){
         
        $content_type = $request->has('content_type')?$request->content_type:null;
        $type = $request->has('type')?$request->type:null;
        $title = $request->has('title')?$request->title:null;

        $user_id = config()->get('global.user_id');
        if($type=="my_content"){
            $data = LearningContent::where('updated_by',$user_id)
            ->when($content_type, function($q) use($content_type){return $q->where('content_type' , $content_type);})
            ->when($title, function($q) use($title){return $q->where('title','like', '%' . $title . '%');})
            ->paginate(10);
        }
        if($type=="other_content"){
             $data = LearningContent::whereNotIn('updated_by',[$user_id])
             ->when($content_type, function($q) use($content_type){return $q->where('content_type' , $content_type);})
             ->when($title, function($q) use($title){return $q->where('title','like', '%' . $title . '%');})
             ->paginate(10);
        }
        if($type=="purchase_content"){
            $data = LearningContent::where('updated_by',100001)
             ->when($content_type, function($q) use($content_type){return $q->where('content_type' , $content_type);})
            ->when($title, function($q) use($title){return $q->where('title','like', '%' . $title . '%');})
            ->paginate(10);
        }   
        if($type=="shared_content"){
            $check = Sharing::where('table_name','learning_contents')
                        ->where('user_id',config()->get('global.user_id'))
                            ->pluck('table_id');
            $data = LearningContent::wherein('id',$check)
            ->when($content_type, function($q) use($content_type){return $q->where('content_type' , $content_type);})
            ->when($title, function($q) use($title){return $q->where('title','like', '%' . $title . '%');})
            ->paginate(10);
        }

        return response()->json($data,200);
    }

    

    public function show($id){
        return new LearningResourceShow(LearningContent::findorfail($id));
    }

    public function store(Request $request){
        
        $this->validate($request,[
            'content_type'         => 'required',
            'title'                => 'required',
        ]);

        $user_id = config()->get('global.user_id');

        $content = new LearningContent;
        $content->cat_id         = (int) explode("@",$request->cat_id)[0];
        $content->cat_title_en   = explode("@",$request->cat_id)[1];
        $content->cat_title_bn   = explode("@",$request->cat_id)[2];
        $content->content_type   = $request->content_type;
        $content->file_id        = $request->file_id;
        $content->title          = $request->title;
        $content->description    = $request->description;
        $content->duration       = $request->duration;
        $content->owner_id       = Config('global.owner_id');
        $content->created_by     = $user_id;
        $content->updated_by     = $user_id;

        if($request->question_setup == 1){
            $content->folder_id      = $request->folder_id?json_encode($request->folder_id):null;
            $content->folder_marks   = $request->folder_marks?json_encode($request->folder_marks):null;
        }
        elseif($request->question_setup == 0){
            $content->quiz_data      = $request->quiz_data?json_encode($request->quiz_data):null;
            $content->quiz_marks     = $request->quiz_marks?json_encode($request->quiz_marks):null;
        }

        if($request->more_data_info){
            $content->more_data_info  = $request->more_data_info?json_encode($request->more_data_info):null;
        }
        
        if($content->save()){
            return response()->json([
                'message' => 'Content created successfully',
                'data'  => $content
            ]);

        }
    }

    public function update(Request $request){
        
        $this->validate($request,[
            'content_type'         => 'required',
            'title'                => 'required',
        ]);
        
        $user_id = config()->get('global.user_id');

        $content = LearningContent::findorfail($request->input('id'));
        
        $content->cat_id         = (int) explode("@",$request->cat_id)[0];
        $content->cat_title_en   = explode("@",$request->cat_id)[1];
        $content->cat_title_bn   = explode("@",$request->cat_id)[2];
        $content->content_type   = $request->content_type;
        $content->content_url    = $request->content_url;
        $content->title          = $request->title;
        $content->description    = $request->description;
        $content->duration       = $request->duration;
        $content->owner_id       = Config('global.owner_id');
        $content->created_by     = $user_id;
        $content->updated_by     = $user_id;

        if($request->question_setup == 1){
            $content->folder_id      = $request->folder_id?json_encode($request->folder_id):null;
            $content->folder_marks   = $request->folder_marks?json_encode($request->folder_marks):null;

            $content->quiz_data      = null;
            $content->quiz_marks     = null;
        }
        elseif($request->question_setup == 0){
            $content->quiz_data      = $request->quiz_data?json_encode($request->quiz_data):null;
            $content->quiz_marks     = $request->quiz_marks?json_encode($request->quiz_marks):null;
            $content->folder_id      = null;
            $content->folder_marks   = null;
        }

        if($request->more_data_info){
            $content->more_data_info  = $request->more_data_info?json_encode($request->more_data_info):null;
        }
        
        if($content->update()){
            return response()->json([
                'message' => 'Content updated successfully',
                'data'  => $content
            ]);
        }
    }

    public function delete($id){
        
        $content = LearningContent::find($id);
        
        if($content){
            $content->delete();
            return response()->json([
                'message' => 'Content deleted successfully'
            ]);
        }else{
            return response()->json([
                'message' => 'No Content found'
            ]);
        }
    }
}