<?php

namespace App\Http\Controllers\ContentBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ContentBank\LearningContent as LearningResource;
use App\Http\Resources\ContentBank\LearningContentShow as LearningResourceShow;
use App\Models\ContentBank\LearningContent;
use App\Models\Myaccount\Sharing;
use App\Models\Question\Question;
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
        })->whereIn('content_type',['video','audio','document'])->orderby('id','DESC')->paginate(5);
        }
        else{
           $data = LearningContent::when($self==1, function ($query, $id) {
            $query->where('created_by',config()->get('global.user_id'));
        })->when(Config('global.owner_id'), function ($query, $id) {
            $query->where('owner_id',$id);
        })->orderby('id','DESC')->paginate(5); 
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
            $data = LearningContent::with('files')->where('updated_by',$user_id)
            ->when($content_type, function($q) use($content_type){return $q->where('content_type' , $content_type);})
            ->when($title, function($q) use($title){return $q->where('title','like', '%' . $title . '%');})
            ->orderby('id','DESC')->paginate(8);
        }
        if($type=="other_content"){
             $data = LearningContent::with('files')->whereNotIn('updated_by',[$user_id])
             ->when($content_type, function($q) use($content_type){return $q->where('content_type' , $content_type);})
             ->when($title, function($q) use($title){return $q->where('title','like', '%' . $title . '%');})
             ->orderby('id','DESC')->paginate(8);
        }
        if($type=="purchase_content"){
            $data = LearningContent::with('files')->where('updated_by',100001)
             ->when($content_type, function($q) use($content_type){return $q->where('content_type' , $content_type);})
            ->when($title, function($q) use($title){return $q->where('title','like', '%' . $title . '%');})
            ->orderby('id','DESC')->paginate(8);
        }   
        if($type=="shared_content"){
            $check = Sharing::where('table_name','learning_contents')
                        ->where('user_id',config()->get('global.user_id'))
                            ->pluck('table_id');
            $data = LearningContent::with('files')->wherein('id',$check)
            ->when($content_type, function($q) use($content_type){return $q->where('content_type' , $content_type);})
            ->when($title, function($q) use($title){return $q->where('title','like', '%' . $title . '%');})
            ->orderby('id','DESC')->paginate(8);
        }

        return LearningResource::collection($data);

        return response()->json($data,200);
    }

    

    public function show($id){
        return new LearningResource(LearningContent::findorfail($id));
    }

    public function store(Request $request){
        
        $this->validate($request,[
            'content_type'         => 'required',
            'title'                => 'required',
        ]);

        $edit = 1;
        $quiz_data = [];
        if(count($request->questions)>0){
                    
            foreach ($request->questions as $key => $qd) {
              $qi = $this->questionEditUpdate($qd);
               array_push($quiz_data,$qi);
            }
        }else{
            $quiz_data = null;
        }

        $user_id = config()->get('global.user_id');

        $content = new LearningContent;
        $content->cat_id         = $request->cat_id;
        $content->more_data_info = json_encode($request->more_data_info);
        $content->content_type   = $request->content_type;
        $content->file_id        = $request->file_id;
        $content->level          = $request->level;
        $content->language_id    = $request->language_id;
        $content->title          = $request->title;
        $content->description    = $request->description;
        $content->instruction    = $request->instruction;
        $content->duration       = $request->duration;
        $content->quiz           = $request->quiz;
        $content->question_setup =  $request->question_setup;
        $content->owner_id       = Config('global.owner_id');
        $content->created_by     = $user_id;
        $content->updated_by     = $user_id;

        if(isset($request->question_setup) && $request->question_setup == 1){
            $content->folder_id      = $request->folder_id?json_encode($request->folder_id):null;
            $content->folder_marks   = isset($request->folder_marks)?json_encode($request->folder_marks):null;
        }
        elseif(isset($request->question_setup) && $request->question_setup == 0){
            if($quiz_data!=null){
                $content->quiz_data  = json_encode($quiz_data);
            }else{
                $content->quiz_data = null;
            }
            
            if(isset($request->quiz_marks)){
                if(!empty($request->quiz_marks)){
                    $content->quiz_marks      = json_encode($request->quiz_marks);
                }else{
                    $content->quiz_marks = null;
                }
            }
        
        }
        
        if($content->save()){
            return response()->json([
                'message' => 'Content created successfully',
                'data'  => new LearningResource($content)
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
        
        $edit = 1;
        $quiz_data = [];
        if(count($request->questions)>0){
                    
            foreach ($request->questions as $key => $qd) {
              $qi = $this->questionEditUpdate($qd);
               array_push($quiz_data,$qi);
            }
        }else{
            $quiz_data = null;
        }
        $content->cat_id         = $request->cat_id;
        $content->more_data_info = json_encode($request->more_data_info);
        $content->content_type   = $request->content_type;
        $content->file_id        = $request->file_id;
        $content->level          = $request->level;
        $content->language_id    = $request->language_id;
        $content->title          = $request->title;
        $content->description    = $request->description;
        $content->instruction    = $request->instruction;
        $content->duration       = $request->duration;
        $content->quiz           = $request->quiz;
        $content->question_setup =  $request->question_setup;
        $content->owner_id       = Config('global.owner_id');
        $content->created_by     = $user_id;
        $content->updated_by     = $user_id;

        if(isset($request->question_setup) && $request->question_setup == 1){
            $content->folder_id      = $request->folder_id?json_encode($request->folder_id):null;
            $content->folder_marks   = isset($request->folder_marks)?json_encode($request->folder_marks):null;
        }
        elseif(isset($request->question_setup) && $request->question_setup == 0){
            if($quiz_data!=null){
                $content->quiz_data  = json_encode($quiz_data);
            }else{
                $content->quiz_data = null;
            }
            
            if(isset($request->quiz_marks)){
                if(!empty($request->quiz_marks)){
                    $content->quiz_marks      = json_encode($request->quiz_marks);
                }else{
                    $content->quiz_marks = null;
                }
            }
        
        }
        if($content->update()){
            return response()->json([
                'message' => 'Content updated successfully',
                'data'  => new LearningResource($content)
            ]);
        }
    }

    public function questionEditUpdate($data){
        if(isset($data['id']))
        {
            $Question = Question::findOrfail($data['id']);
            
        }else{
            $Question = new Question();
        }
       
        
        $Question->id                          = isset($data['id'])?$data['id']:'';
        // $Question->partner_category            = $request->input('folder');
        if(isset($data['folder'])){
            $Question->partner_category            = $data['folder'];
        }
        $Question->title                       = $data['title'];
        $Question->category_id                 = isset($data['category_id'])?$data['category_id']:null;
        // $Question->title_content_type          = $data['title_content_type'];
        // $Question->title_content_id            = $data['title_content_id'];
        // $Question->title_content_url           = $data['title_content_url'];
        $Question->description                  = $data['description'];
        $Question->type                        = $data['type'];
        $Question->file_id                     = $data['file_id'];
        $Question->mark                        = isset($data['marks'])?$data['marks']:1;
        $Question->dif_level                   = $data['dif_level'];
        if($data['type']=='sequence' || $data['type']=='matching'){
            $Question->options                     = json_encode($data['sequence']);
            $Question->answer                     = json_encode($data['body']);
        }
        else if($data['type']=='likert-scale'){
            $Question->options                     = json_encode($data['multiplebody']);
        }else{
            $opt = $data['body'];
            foreach ($opt as $key => $value) {
                 $opt[$key]['answer'] = '';
            }
            $Question->options                     = json_encode($opt);
            $Question->answer                      = json_encode($data['body']);
        }
        
        $Question->submission_criteria         = json_encode($data['submission_criteria']);
        $Question->details                     = isset($data['details'])?$data['details']:null;
        $Question->feedback                    = json_encode($data['feedback']);
        // $Question->time                        = $data['time'];
        // $Question->date                        = $data['date'];
        // $Question->status                      = $data['status'];
       

        if(isset($data['id'])){
            $Question->updated_by              = config()->get('global.user_id');
        }else
        {
            $Question->created_by              = config()->get('global.user_id');
            $Question->updated_by              = config()->get('global.user_id');
        }
        $Question->owner_id                    = config()->get('global.owner_id');
      
        if(isset($data['id'])){
            if($Question->update())
            {
                return $Question->id;
            }
        }else{
            if($Question->save())
            {
                return $Question->id;
            }
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