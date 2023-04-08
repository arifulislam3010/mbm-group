<?php

namespace App\Http\Controllers\MyAccount;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Myaccount\Blog\BlogPost;
use App\Http\Resources\BlogPost as BlogPostResource;
use Validator;
use Image;
use File;
use Auth;
use App\User;
use App\Lib\FileUpload;
use App\Models\AdminSettings\Tag;

class BlogController extends Controller
{
   
    public function index(Request $request)
    {   
        //type,cat_id,order,limit,search
        //popular-1,favourite-2,featured-3
        $search       = ($request->has('search'))?$request['search']:null;
        $limit       = ($request->has('limit'))?$request['limit']:9;
        $order       = ($request->has('filter'))?$request['filter']:'DESC';
        $type        = ($request->has('type'))?$request['type']:null;
        $is_published = ($request->has('publish'))?$request['publish']:1;

        if($request['type']==1 && $request['type']==2){
           $blog = BlogPost::
            when($search, function($q) use($search){
                return $q->where('title','like',"%$search%");
            })
            ->when($request->category_id, function($q) use($request){
                return $q->where('category_id',$request->category_id);
            })
            ->when($is_published, function($q) use($is_published){
                if($is_published=='yes'){
                    return $q->where('published','=',1);
                }
                else{
                   return $q->where('published','=',0); 
                }
            })
            ->Join('blog_post_likes','blog_post_likes.blog_post_id','blog_posts.id')
            ->Join('blog_post_comments','blog_post_comments.blog_post_id','blog_posts.id')
            ->selectRaw('blog_posts.*,count(blog_post_likes.id) as likes,count(blog_post_comments.id) as comments')
            ->where('created_by',config()->get('global.user_id'))
            ->groupBy('blog_posts.id')
            ->orderBy('comments', $order)
            ->orderBy('likes', 'desc')->paginate($limit);
        
        return BlogPostResource::collection($blog);
        }
        if($request['type']==3)
        {
           $blog = BlogPost::where('featured',1)
                -> when($search, function($q) use($search){
                    return $q->where('title','like',"%$search%");
                })
                ->when($request->category_id, function($q) use($request){
                    return $q->where('category_id',$request->category_id);
                })
                ->when($is_published, function($q) use($is_published){
                    if($is_published=='yes'){
                        return $q->where('published','=',1);
                    }
                    else{
                       return $q->where('published','=',0); 
                    }
                })
                ->where('created_by',config()->get('global.user_id'))
            ->orderBy('featured_order',$order)->paginate($limit);
        
        return BlogPostResource::collection($blog);
        }

        $category = ($request->has('id'))?$request['id']:null;
        $search = ($request->has('search'))?$request['search']:null;
        $rating = ($request->has('rating'))?$request['rating']:null;
        $tag = Tag::where('title','like',"%$search%")->pluck('id')->toArray();
        
        if(empty($tag)){
            $tag=null;
        }
        //return $tag;
        if(config()->get('global.owner_id')){
            $blog = BlogPost::where('created_by',config()->get('global.user_id'))
        ->when($search, function($q) use($search){
            return $q->orWhere('title','like',"%$search%");
        })->paginate(10);
        }else{

            $blog = BlogPost::
            leftJoin('blog_tags','blog_tags.blog_post_id','blog_posts.id')
            ->select('blog_posts.*')
            ->when($search, function($q) use($search){
                return $q->orWhere('title','like',"%$search%");
            })
            ->when($request->category_id, function($q) use($request){
                return $q->where('category_id',$request->category_id);
            })
            ->when($is_published, function($q) use($is_published){
                if($is_published=='yes'){
                    return $q->where('published','=',1);
                }
                else{
                   return $q->where('published','=',0); 
                }
            })
            ->where('blog_posts.created_by',config()->get('global.user_id'))
            ->orderBy('id',$order)
            ->paginate($limit);
        }
        
        return BlogPostResource::collection($blog);
        
    }
   
    public function show($id)
    {
        $blog = BlogPost::find($id);
        if($blog){
            return new BlogPostResource($blog);
        }
    }
   
    public function details($id)
    {
        $blog_details = BlogPost::find($id);
        $blog_details->tread = $blog_details->tread+1;
        $blog_details->save();
        if($blog_details){
            return response()->json([
                'api_info'    => 'BlogPost',
                'data'        => new BlogPostResource($blog_details),
            ] , 200);
        }
        else{
            return response()->json(['errors' =>['message'=>'No Data']], 400);
        }
    }
}
