<?php

namespace App\Http\Controllers\MyAccount\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Blog\BlogPost;
use Auth;
//  Models...
use App\Models\Blog\BlogPostLike;
use App\Lib\GamificationClass;
class BlogPostLikeController extends Controller
{
    public function index(Request $request)
    {
        //$data = $request->all();
        //return BlogPost::find($data['id']);

        $data = $request->all();
        $user_id =Auth::user()->id;
        if($data['type']==1){
            $find = BlogPostLike::where('blog_post_id',$data['id'])->where('user_id',$user_id)->first();
            if($find){
                if($find->likes==1){
                    $find->likes = 0;
                    $find->dislikes = 0;
                    $find->save();
                }else{
                    $find->likes = 1;
                    $find->dislikes = 0;
                    $find->save();
                }
                $gamification_class = new GamificationClass;
                $gamification_class->gamificationStore('bl',Auth::user()->id,$find->id,1);

                $blog_like_count = BlogPostLike::where('blog_post_id',$data['id'])->where('likes',1)->count();
                $blog_dislike_count = BlogPostLike::where('blog_post_id',$data['id'])->where('dislikes',1)->count();
                return response()->json([
                'message' => 'Blog Likes'
                ,'data'=>$find,'like_count'=>$blog_like_count,'dislike_count'=>$blog_dislike_count], 200);
            }else{
                $bpl = new BlogPostLike();
                $bpl->likes = 1;
                $bpl->dislikes = 0;
                $bpl->blog_post_id = $data['id'];
                $bpl->user_id = $user_id;
                $bpl->save();
                $gamification_class = new GamificationClass;
                $gamification_class->gamificationStore('bl',Auth::user()->id,$bpl->id,1);
                $blog_like_count = BlogPostLike::where('blog_post_id',$data['id'])->where('likes',1)->count();
                $blog_dislike_count = BlogPostLike::where('blog_post_id',$data['id'])->where('dislikes',1)->count();
                return response()->json([
                'message' => 'Blog post like'
                ,'data'=>$bpl,'like_count'=>$blog_like_count,'dislike_count'=>$blog_dislike_count], 200);
            }
        }elseif($data['type']==2){
            $find = BlogPostLike::where('blog_post_id',$data['id'])->where('user_id',$user_id)->first();
            if($find){
                if($find->dislikes==1){
                    $find->likes = 0;
                    $find->dislikes = 0;
                    $find->save();
                }else{
                    $find->likes = 0;
                    $find->dislikes = 1;
                    $find->save();
                }
                $gamification_class = new GamificationClass;
                $gamification_class->gamificationStore('bl',Auth::user()->id,$find->id,1);
                $blog_like_count = BlogPostLike::where('blog_post_id',$data['id'])->where('likes',1)->count();
                $blog_dislike_count = BlogPostLike::where('blog_post_id',$data['id'])->where('dislikes',1)->count();
                return response()->json([
                'message' => 'Blog post like'
                ,'data'=>$find,'like_count'=>$blog_like_count,'dislike_count'=>$blog_dislike_count], 200);
            }else{
                $bpl = new BlogPostLike();
                $bpl->likes = 0;
                $bpl->dislikes = 1;
                $bpl->blog_post_id = $data['id'];
                $bpl->user_id = $user_id;
                $bpl->save(); 
                $gamification_class = new GamificationClass;
                $gamification_class->gamificationStore('bl',Auth::user()->id,$bpl->id,1);
                $blog_like_count = BlogPostLike::where('blog_post_id',$data['id'])->where('likes',1)->count();
                $blog_dislike_count = BlogPostLike::where('blog_post_id',$data['id'])->where('dislikes',1)->count();
                return response()->json([
                'message' => 'Blog post like '
                ,'data'=>$bpl,'like_count'=>$blog_like_count,'dislike_count'=>$blog_dislike_count], 200);
            }
        }else{
           $find = BlogPostLike::where('blog_post_id',$data['id'])->where('user_id',$user_id)->first(); 
           $blog_like_count = BlogPostLike::where('blog_post_id',$data['id'])->where('likes',1)->count();
           $blog_dislike_count = BlogPostLike::where('blog_post_id',$data['id'])->where('dislikes',1)->count();
           return response()->json([
                'message' => 'Blog post like'
                ,'data'=>$find,'like_count'=>$blog_like_count,'dislike_count'=>$blog_dislike_count], 200);
        }
    }

    public function store(Request $request)
    {
        $blog_post_like = BlogPostLike::create($request->all());

        if($blog_post_like)
        {
            return response()->json([
                'message' => 'Blog post like created successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post like cannot be created.'
        ], 400);
    }

    public function show($id)
    {
        $blog_post_like = BlogPostLike::find($id);

        if(empty($blog_post_like))
        {
            return response()->json([
                'message' => 'Blog post like not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Blog post like retrived successfully.',
            'data' => $blog_post_like
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $blog_post_like = BlogPostLike::find($id);

        if(empty($blog_post_like))
        {
            return response()->json([
                'message' => 'Blog post like not found.'
            ], 404);
        }

        $blog_post_like = BlogPostLike::find($id)->update($request->all());

        if($blog_post_like)
        {
            return response()->json([
                'message' => 'Blog post like update successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post like cannot be updated.'
        ], 400);
    }

    public function destroy($id)
    {
        $blog_post_like = BlogPostLike::find($id);

        if(empty($blog_post_like))
        {
            return response()->json([
                'message' => 'Blog post like not found.'
            ], 404);
        }

        if($blog_post_like->delete())
        {
            return response()->json([
                'message' => 'Blog post like deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post like cannot be deleted.'
        ], 200);
    }
}
