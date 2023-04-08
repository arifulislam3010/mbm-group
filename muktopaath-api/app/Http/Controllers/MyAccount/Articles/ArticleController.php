<?php

namespace App\Http\Controllers\MyAccount\Articles;
use App\Models\Myaccount\InstitutionInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Myaccount\Articles\ArticleCategory;
use App\Models\Myaccount\Articles\Article;

class ArticleController extends Controller
{
    public $successStatus = 200;

    public function index()
    { 
        $article = Article::when(Request()->search, function($q) {return $q->where('title','like','%'.Request()->search.'%');})->when(Request()->category_id, function($q) {return $q->where('article_category',Request()->category_id);})->when(config()->get('global.user_id'), function($q) {return $q->where('created_by',config()->get('global.user_id'));})->orderBy('id','DESC')->with('thumbnail')->paginate(10);
        // $data = ArticleCategory::where('status', 1)->paginate(3);
        if($article){
            return response()->json($article);
        }
        else{
            return response()->json(['errors' =>['message'=>'No Data']], 401);
        }
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

        $res = Article::find($request['id']);
        if($res->status == 1){
            $res->status = 0;
        }else{
            $res->status = 1;
        }
        $res->update();

        return response()->json(['message' => 'Article published','data' => $res]);
    }
    
    public function view_categories(){
        $res = ArticleCategory::all();
        return response()->json($res);
    }

    public function store(Request $request)
    {

        $messsages = array(
            'required'=>'required',
            'min'     => 'min_8',
            'email'   => 'email',
            'same'    => 'same',
            'unique'  => 'unique',
            'image'   => 'image',
            'mines'   =>'mines',
            'max'     =>'max',
           );

            $rules = array(
                'title'             => 'required',
                'title_bn'             => 'required',
                // 'slug_name'             => 'required',
                // 'summary'             => 'required',
                // 'summary_bn'             => 'required',
                'article_category'             => 'required',
            );

        $validator = Validator::make($request->all(),$rules,$messsages);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }

        $RouteUserName = config('global.username');
        $owner_id = config()->get('global.owner_id');
        $user_id = config()->get('global.user_id');
        
        $data = $request->all();

        $insert  = new Article;

        // $result = substr($data['image_url'],0,10);
        // if ($result=='data:image') {
        //     $imageData = $data['image_url'];
        //     $fileName = uniqid().'.'.explode('/',explode(':',substr($imageData,0,strpos($imageData, ';')))[1])[1];
        //     Image::make($data['image_url'])->save(public_path('images/currentactivities/').$fileName);
        //     $insert->thumbnail = '/images/currentactivities/'.$fileName;
        // }
        $insert->file_id                = $data['file_id'];
        $insert->popup_status           = $data['popup_status'];
        $insert->title                  = $data['title'];
        $insert->slug_name              = $data['slug_name'];
        $insert->title_bn               = $data['title_bn'];
        $insert->slug_name              = isset($data['slug_name'])?$data['slug_name']:null;
        $insert->link                   = $data['link'];
        $insert->type                   = 2;
        if(isset($data['summary'])){
            $insert->summary                = $data['summary'];
        }
        if(isset($data['summary_bn'])){
            $insert->summary_bn             = $data['summary_bn'];
        }
       
        $insert->description            = $data['description'];
        $insert->description_bn         = $data['description_bn'];
        $insert->owner_id               = $owner_id;
        $insert->article_category       = $data['article_category'];
        $insert->created_by             = $user_id;
        $insert->updated_by             = $user_id;
        if($insert->save()){

            return Article::where('id',$insert->id)->with('thumbnail')->first();
            $data = [
                'status'    => 'Success',
                'code'      => '200',
                'data'      => Article::where('id',$insert->id)->with('thumbnail')->first(),
            ];

            return response()->json($data,200);

        }else{
            $data = [
                'status'  => 'error',
                'code'    => '404',
                'message' => 'Error occurred.',
            ];

            return response()->json($data, 404);
        }
    }


    public function update(Request $request,$id)
    {

        $messsages = array(
            'required'=>'required',
            'min'     => 'min_8',
            'email'   => 'email',
            'same'    => 'same',
            'unique'  => 'unique',
            'image'   => 'image',
            'mines'   =>'mines',
            'max'     =>'max',
        );

            $rules = array(
                'title'             => 'required',
                'title_bn'             => 'required',
                'article_category'             => 'required',
            );

        $validator = Validator::make($request->all(),$rules,$messsages);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }

        $RouteUserName = config('global.username');
        $owner_id = config()->get('global.owner_id');
        $user_id = config()->get('global.user_id');

        $data = $request->all();
        $update  =  Article::find($id);

        // $result = substr($data['thumbnail'],0,10);
        // if ($result=='data:image') {
        //     if($update->thumbnail!=''){
        //       $image_path = 'images/currentactivities/'.substr($update->thumbnail,16,1000);
        //         if (File::exists($image_path)){
        //             unlink($image_path);
        //         }  
        //     }
            
        //     $imageData = $data['thumbnail'];
        //     $fileName = uniqid().'.'.explode('/',explode(':',substr($imageData,0,strpos($imageData, ';')))[1])[1];
        //     Image::make($data['thumbnail'])->save(public_path('images/currentactivities/').$fileName);
        //     $update->thumbnail = '/images/currentactivities/'.$fileName;
        // }else{
        //     $update->thumbnail = $data['thumbnail'];
        // }
        $update->file_id                = $data['file_id'];
        $update->popup_status           = $data['popup_status'];
        $update->title                  = $data['title'];
        $update->slug_name              = $data['slug_name'];
        $update->title_bn               = $data['title_bn'];
        $update->slug_name              = $data['slug_name'];
        $update->link                   = $data['link'];
        $update->type                   = 2;
        $update->status                 = 1;

        if(isset($data['summary']) && $data['summary']!=null){
            $update->summary                = $data['summary'];
        }
        if(isset($data['summary_bn']) && isset($data['summary_bn'])!=null){
            $update->summary_bn             = $data['summary_bn'];
        }
        $update->description            = $data['description'];
        $update->description_bn            = $data['description_bn'];
        $update->article_category       = $data['article_category'];
        $update->updated_by             = $user_id;
        if($update->update()){
            $data = [
                'status'    => 'Success',
                'code'      => '200',
                'data'      => Article::where('id',$id)->with('thumbnail')->first(),
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

    public function popup_and_news(){

        $news = Article::where('article_category',8)
                ->where('status',1)
                ->orderby('created_at','DESC')
                ->first();

        $popup = Article::where('article_category',9)
                ->where('status',1)
                ->orderby('created_at','DESC')
                ->first();


        return response()->json(['news' => $news, 'popup' => $popup]);
    }

    


    public function show(Request $request,$id)
    {        
        $owner_id = config()->get('global.owner_id');

        $article = Article::where('article_category',$id)
        ->when($owner_id, function($q) use($owner_id){return $q->where('owner_id' ,$owner_id);})
        ->with('articleId','thumbnail')->orderBy('created_at', 'desc')->paginate($request->limit);

        if($article){
            return response()->json([
                'api_info'    => 'Article details',
                'data'        => $article,
            ] , $this->successStatus);
        }
        else{
            return response()->json(['errors' =>['message'=>'No Data']], 401);
        }
    }

    public function details($id){

        $article = Article::with('creator')->where('id',$id)->first();

        if($article){
            return response()->json([
                'api_info'    => 'Article',
                'data'        => $article,
            ] , $this->successStatus);
        }
        else{
            return response()->json(['errors' =>['message'=>'No Data']], 401);
        }
    }

    public function delete($id){
        $article = Article::find($id);

        if($article){
            $article->delete();
            return response()->json([
                'api_info'    => 'Article deleted',
                'data'        => $article,
            ] , $this->successStatus);
        }
        else{
            return response()->json(['errors' =>['message'=>'No Data']], 401);
        }
    }
    
    public function popup()
    {        
        $article = Article::where('article_category',9)->where('status',1)->first();

        if($article){
            return response()->json([
                'api_info'    => 'Popup details',
                'data'        => $article,
            ] , $this->successStatus);
        }
        else{
            return response()->json(['errors' =>['message'=>'No Data']], 401);
        }
    }
}
