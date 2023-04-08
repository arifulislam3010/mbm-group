<?php

namespace App\Http\Controllers\ContentBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question\PartnerCategory;
use App\Models\Myaccount\Sharing;
use App\Http\Resources\ContentBank\Sharing as SharingResource;
use App\Http\Resources\ContentBank\Questions as QuestionsResource;
use Illuminate\Support\Facades\Config;
use Validator;
use App\Models\Question\Question;
use App\Lib\ContentBank;

class QuestionController extends Controller
{
    use ContentBank;
 
	public function index(Request $request){
        
        if($request->data!=true){
            if(is_numeric(config()->get('global.owner_id'))){
                $res = Question::when(Request()->type, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('type','=',$field);
                        });
                    })->when(Request()->category_id, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('partner_category','=',$field);
                        });
                    })->when(Request()->search, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('title','like','%'.$field.'%');
                        });
                    })->where('owner_id',Config('global.owner_id'))
                ->orderby('id','DESC')
                ->paginate(10);

            }else{
                $res = Question::when(Request()->type, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('type','=',$field);
                        });
                    })->when(Request()->category_id, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('category_id','=',$field);
                        });
                    })->where('created_by',Config('global.user_id'))
                ->orderby('id','DESC')
                ->paginate(10);
            }
		

           return QuestionsResource::collection($res);
        }else{
            $count = Sharing::where('table_name','questions')
                    ->where('user_id',config()->get('global.user_id'))
                    ->get();
                    return SharingResource::collection($count);
        }
		
    }
	

    public function create_category(Request $request){
        //return $request->all();
       
        $this->validate($request,[
	    	'title'             => 'required',
           ]);
        
        $PartnerCategory = $request->input('id')!=null ? PartnerCategory::findOrfail($request->id) : new PartnerCategory();
        
        $PartnerCategory->id = $request->input('id');
        $PartnerCategory->title = $request->input('title');
        $PartnerCategory->status = 1;
        
         if($request->parent_id==''){
            $PartnerCategory->parent_id = null;

         }else{
            $PartnerCategory->parent_id = $request->input('parent_id');
        }
        $PartnerCategory->owner_id =   config()->get('global.owner_id');
        $PartnerCategory->created_by = config()->get('global.user_id');
        $PartnerCategory->updated_by = config()->get('global.user_id');

        if($PartnerCategory->save())
        {
            return response()->json([
                'data'=> $PartnerCategory,
                'message'=>'Folder category created successfully']);
        }
    }

    public function show($id){

        $res = Question::with('folder')->find($id);
        if($res['feedback']==null){
            $temp['correct'] = '';
            $temp['incorrect'] = '';
            $res['feedback'] = json_encode($temp);
        }
        $res['feedback'] = json_decode($res['feedback']);
        $res['submission_criteria'] = json_decode($res['submission_criteria']);
        $res['rubric_criteria'] = json_decode($res['options']);
        $res['body'] = json_decode($res['answer']);
        
        if($res)

        return response()->json($res);
    }

    public function folder_category(){

            if(is_numeric(config()->get('global.owner_id'))){
                        if(Request()->no_pagination){
                            $res = PartnerCategory::where('owner_id',config()->get('global.owner_id'))->with('children')
                                ->where('parent_id',null)
                                ->orderby('id','DESC')
                                ->get();
                        }else{
                            $res = PartnerCategory::where('owner_id',config()->get('global.owner_id'))->with('children')
                                ->where('parent_id',null)
                                ->orderby('id','DESC')
                                ->paginate(10);
                        }

                }else{
                    if(Request()->no_pagination){
                        $res = PartnerCategory::where('created_by',config()->get('global.user_id'))->with('children')
                            ->where('parent_id',null)
                            ->orderby('id','DESC')
                            ->get();
                    }else{
                       $res = PartnerCategory::where('created_by',config()->get('global.user_id'))->with('children')
                           ->where('parent_id',null)
                           ->orderby('id','DESC')
                           ->paginate(10); 
                    }
                    
                }
                if(Request()->no_pagination){
                    $data['data'] = $res;
                    return response()->json($data);
                }
    	return response()->json($res);
    }

    public function store(Request $request){

        $messsages = array(
            'required'=>'e_required',
            'folder' => 'folder',
            'title' => 'title',
            'type' => 'type',
            'dif_level' => 'dif_level',
            'status' => 'status',
            'category_id' => 'category_id'
        );

        $rules = array(
                // 'folder'  =>'required',
                'title'   => 'required',
                'type'    => 'required',
            );

        $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }



        // owner section 
        $owner = Config('global.owner_id');

        // data store and update  
      

        if($this->questionAddUpdate($request->all()))
        {
            return response()->json(['data'=> 'question created successfully']);
        }

    }


    //     public function store(Request $request){

    //     $messsages = array(
    //         'required'=>'e_required',
    //         'folder' => 'folder',
    //         'title' => 'title',
    //         'type' => 'type',
    //         'dif_level' => 'dif_level',
    //         'status' => 'status',
    //         'category_id' => 'category_id'
    //     );

    //     $rules = array(
    //             // 'folder'  =>'required',
    //             'title'   => 'required',
    //             'type'    => 'required',
    //         );

    //     $validator = Validator::make($request->all(),$rules,$messsages);

    //    if($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
    //     }



    //     // owner section 
    //     $owner = Config('global.owner_id');

    //     // data store and update  
    //     if(empty($request->input('id')))
    //     {
    //         $Question = new Question();
    //     }else{
    //         $Question = Question::findOrfail($request->input('id'));
    //     }
       
        
    //     $Question->id                          = $request->input('id');
    //     // $Question->partner_category            = $request->input('folder');
    //     $Question->partner_category            = $request->input('folder');
    //     $Question->title                       = $request->input('title');
    //     $Question->category_id                 = $request->input('category_id');
    //     $Question->title_content_type          = $request->input('title_content_type');
    //     $Question->title_content_id            = $request->input('title_content_id');
    //     $Question->title_content_url           = $request->input('title_content_url');
    //     $Question->description                  = $request->input('description');
    //     $Question->type                        = $request->input('type');
    //     $Question->file_id                     = $request->input('file_id');
    //     $Question->mark                        = $request->marks?$request->marks:1;
    //     $Question->dif_level                   = $request->input('dif_level');
    //     if($request->input('type')=='sequence' || $request->input('type')=='matching'){
    //         $Question->options                     = json_encode($request->input('sequence'));
    //         $Question->answer                     = json_encode($request->input('body'));
    //     }
    //     else if($request->input('type')=='likert-scale'){
    //         $Question->options                     = json_encode($request->input('multiplebody'));
    //     }
    //     else if($request->input('type')=='essay'){
    //         $Question->options                     = json_encode($request->input('rubric_criteria'));
    //         if($request->rubric_grading=='true'){
    //             $Question->rubric_grading = 1;
    //         }else{
    //             $Question->rubric_grading = 0;
    //         }
    //     }
    //     else{
    //         $opt = $request->input('body');
    //         foreach ($opt as $key => $value) {
    //              $opt[$key]['answer'] = '';
    //         }
    //         $Question->options                     = json_encode($opt);
    //         $Question->answer                      = json_encode($request->input('body'));
    //     }
        
    //     $Question->submission_criteria         = json_encode($request->input('submission_criteria'));

    //     $Question->details                     = $request->input('details');
    //     $Question->feedback                    = json_encode($request->input('feedback'));
    //     $Question->time                        = $request->input('time');
    //     $Question->date                        = $request->input('date');
    //     $Question->status                      = $request->input('status');
       

    //     if($request->input('id')){
    //         $Question->updated_by              = config()->get('global.user_id');
    //     }else
    //     {
    //         $Question->created_by              = config()->get('global.user_id');
    //         $Question->updated_by              = config()->get('global.user_id');
    //     }
    //     $Question->status                      = $request->input('status');
    //     $Question->owner_id                    = $owner;
      

    //     if($Question->save())
    //     {
    //         return response()->json(['data'=> 'question created successfully']);
    //     }

    // }



    public function updatefolder(Request $request,$id){

        $folder = PartnerCategory::find($id);
        $folder->title = $request->title;
        $folder->update();

        return response()->json(['message' => 'Updated successfully','data' => $folder]);

    }

    public function deleteFolder(Request $request, $id){

        $res = PartnerCategory::find($id);
        if($res){
            $res->delete();

            return response()->json(['message' => 'Successfully deleted folder']);
        }
    }

    public function destroy($id){
        $res = Question::find($id);
        if($res){
            $res->delete();
            return response()->json(['message' => 'question deleted successfully']);
        }else{
            return response()->json(['message' => 'question not found']);
        }
    }

    public function questions(){
    	$res = Question::where('owner_id',7)->get();
        $this->validate($request,[
            'folder'                => 'required',
            'title'                 => 'required',
            'type'                  => 'required',
            'dif_level'             => 'required',
            'status'             => 'required',
        ]);
    	return response()->json($res);
    }

    public function folderCategorySearch(Request $request){
        $categories = PartnerCategory::where('title', 'like', '%' . $request->input('title') . '%')->get();

        foreach ($categories as $key => $category) {
           $parent_id = [];
           $parent_id = PartnerCategory::where('parent_id',$category->id)->pluck('id');
           $parent_id->push($category->id);
           $category->question = Question::whereIn('partner_category',$parent_id)->get();
        }
 
        return $categories;
    }

    public function folder(Request $request){

        $categories = PartnerCategory::where('parent_id',null)->paginate(10);

        foreach ($categories as $key => $category) {
           $category->children = PartnerCategory::where('parent_id',$category->id)->get();
        }
        
        return $categories;
    }

    public function folderQuestion(Request $request){
        $query = Question::orderBy('id','DESC');

        if($request->input('partner_category')){
           $parent_id = PartnerCategory::where('parent_id',$request->input('partner_category'))->pluck('id');
           $parent_id->push($request->input('partner_category'));
           $query->whereIn('partner_category',$parent_id);
        }

        if($request->input('title')){
           $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if($request->input('type')){
           $query->where('type',$request->input('type'));
        }

        if($request->input('level')){
           $query->where('dif_level',$request->input('level'));
        }

        return $query->paginate(50);

    }
    
}