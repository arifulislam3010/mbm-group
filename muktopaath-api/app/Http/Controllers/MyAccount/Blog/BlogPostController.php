<?php

namespace App\Http\Controllers\MyAccount\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Myaccount\Blog\BlogPost;
use App\Models\AdminSettings\Tag;
use App\Models\Myaccount\Blog\BlogTag;
use App\Http\Resources\BlogPost as BlogPostResource;
use Validator;
use Image;
use File;
use Auth;
use App\User;
use App\Lib\FileUpload;

class BlogPostController extends Controller
{
    public function index()
    {
        $blog_posts = BlogPost::where('created_by',Auth::user()->id)->paginate(10);

        return BlogPostResource::collection($blog_posts);
    }

    public function store(Request $request)
    {   

        $data = $request->all();
        //return $data['tags'][0]->id;
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
               'title'          =>'required',
               'category_id'    => 'required'
               // 'thumbnail'      => 'required|image',
            );

            $validator = Validator::make($request->all(),$rules,$messsages);

            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
            }
            
        if($data['id']!='' || $data['id']!=null){
            $insert  = BlogPost::find($data['id']);
            $insert->updated_by  = Auth::user()->id; 
        }else{
            $insert  = new BlogPost();
            $insert->created_by  = Auth::user()->id;
            $insert->updated_by  = Auth::user()->id;
        }
        if ($request->hasFile('thumbnail'))
        {
            $file           = $request->file('thumbnail');
            $prefix         = 'blogpost';
            $path           = 'blogpost/';

            $file_upload = new FileUpload;
            $upload = $file_upload->upload($file, $prefix, $path);
            $thumbnail = $upload['file_name'];
            
        }
        else
        {
            
            $thumbnail = null;
        }
        $insert->title = $data['title'];
        $insert->body = $data['body'];
        $insert->category_id = $data['category_id'];
        if($data['id']!='' || $data['id']!=null){
            if($thumbnail!=null){
                 $insert->thumbnail = $thumbnail;
            }

        }else{
             $insert->thumbnail = $thumbnail;
        }
       
       

        if($insert->save()){
         $BlogTagAll = BlogTag::where('blog_post_id',$insert->id)->delete();
          if(!empty($request->input('tags')))
            {
                foreach ($data['tags'] as $value) {
                    $tagFind = Tag::where('id',$value)->Orwhere('title',$value)->first();
                    $BlogTag = new BlogTag();
                    if(empty($tagFind))
                    {

                        $tagAdd = new Tag();
                       
                        $tagAdd->title = $value;
                        $tagAdd->type = 1;
                        $tagAdd->save();

                        $BlogTag->tag_id      = $tagAdd->id;
                        $BlogTag->blog_post_id = $insert->id;
                        $BlogTag->created_by  = Auth::user()->id;
                        $BlogTag->updated_by  = Auth::user()->id;
                        $BlogTag->save();

                 
                    }else
                    {
                        $BlogTag->tag_id      = $tagFind->id;
                        $BlogTag->blog_post_id = $insert->id;
                        $BlogTag->created_by  = Auth::user()->id;
                        $BlogTag->updated_by  = Auth::user()->id;
                        $BlogTag->save();
                  
                   
                    }
                }
            }  
                return new BlogPostResource($insert);
            }else{
                return response()->json(['errors' =>['message'=>'No Data']], 401);
            }
        }

    public function edit($id)
    {
        $blog_post = BlogPost::find($id);
        return new BlogPostResource($blog_post);
        // if(empty($blog_post))
        // {
        //     return new BlogPostResource($blog_post);
        // }

        // return response()->json([
        //     'message' => 'Blog post retrived successfully.',
        //     'data' => $blog_post
        // ], 200);
    }

    public function update(Request $request)
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
               'title'          =>'required',
               // 'thumbnail'      => 'required|image',
            );
            $validator = Validator::make($request->all(),$rules,$messsages);

            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
            }

         
        $data = $request->all();
        $update  = BlogPost::find($data['id']);
        
        if ($request->hasFile('thumbnail'))
        {
            $file           = $request->file('thumbnail');
            $prefix         = 'blogpost';
            $path           = 'blogpost/';

            $file_upload = new FileUpload;
            $upload = $file_upload->upload($file, $prefix, $path);
            $thumbnail = $upload['file_name'];
            $update->thumbnail = $thumbnail;
            
        }
        
        $update->title = $data['title'];
        $update->body = $data['body'];
        $update->category_id = $data['category_id'];
        
       
        if($update->update()){
          $BlogTagAll = BlogTag::where('blog_post_id',$update->id)->delete();
          if(!empty($request->input('tags')))
            {
                foreach ($data['tags'] as $value) {
                    $tagFind = Tag::find($value);
                     
                    $BlogTag = new BlogTag();
                    if(empty($tagFind))
                    {

                        $tagAdd = new Tag();
                       
                        $tagAdd->title = $value;
                        $tagAdd->type = 1;
                        $tagAdd->save();

                        $BlogTag->tag_id      = $tagAdd->id;
                        $BlogTag->blog_post_id = $insert->id;
                        $BlogTag->created_by  = config()->get('global.user_id');
                        $BlogTag->updated_by  = config()->get('global.user_id');
                        $BlogTag->save();

                 
                    }else
                    {
                        $BlogTag->tag_id      = $value;
                        $BlogTag->blog_post_id = $update->id;
                        $BlogTag->created_by  = config()->get('global.user_id');
                        $BlogTag->updated_by  = config()->get('global.user_id');
                        $BlogTag->save();
                  
                   
                    }
                }
            }

          return new BlogPostResource($update);
        }
        else{
                  
            return response()->json(['errors' =>['message'=>'Update Unsuccesfull']], 401);
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

        $res = BlogPost::find($request['id']);
        if($res->published == 1){
            $res->published = 0;
        }else{
            $res->published = 1;
        }
        $res->update();

        return response()->json(['message' => 'Blog published','data' => $res]);
    }


    public function feature(Request $request){

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

        $res = BlogPost::find($request['id']);
        if($res->featured == 1){
            $res->featured = 0;
        }else{
            $res->featured = 1;
        }
        $res->update();

        return response()->json(['message' => 'Blog is featured','data' => $res]);
    }

    public function get_tags()
    {
        $tags = Tag::get();
        if($tags){
            return response()->json([
                'api_info'    => 'Tags',
                'data'        => $tags,
            ] , 200);
        }
        else{
            return response()->json(['errors' =>['message'=>'No Data']], 401);
        }
    }

    public function delete($id)
    {
        $blog_post = BlogPost::find($id);
        if($blog_post->delete()){
            $data = [
                'status'    => 'Success',
                'code'      => '200',
                'data'      => $blog_post,
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

}
