<?php

namespace App\Http\Controllers\MyAccount\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

//  Models...
use App\Models\Blog\BlogPostComment;
use Auth;
use App\Http\Resources\PostBlogComment as PostBlogCommentResource;
use App\Lib\GamificationClass;
// $gamification_class = new GamificationClass;
// $gamification_class->gamificationStore(3,$user->id);
class BlogPostCommentController extends Controller
{
    public function index(Request $request)
    {
        $blog_post_comments = BlogPostComment::all();

        return $blog_post_comments;
    }

    public function store(Request $request)
    {
        if($request->input('delete')){
            BlogPostComment::find($request->id)->delete();
        }else{
            $comment = $request->id!=''? BlogPostComment::findOrFail($request->id) : new BlogPostComment;
            $comment->body = $request->input('body');
            $comment->blog_post_id = $request->input('blog_post_id');
            $comment->blog_post_comment_parent_id = $request->input('blog_post_comment_parent_id');
            $comment->user_id = Auth::user()->id;
            $comment->save();
            
        }
        $allComents = BlogPostComment::where('blog_post_id',$request->input('blog_post_id'))->where('blog_post_comment_parent_id','=',null)->get();
        $gamification_class = new GamificationClass;
        $gamification_class->gamificationStore('bc',Auth::user()->id,$request->input('blog_post_id'),1);
       
        
        return PostBlogCommentResource::collection($allComents);

    }

    public function show($id)
    {
        $blog_post_comment = BlogPostComment::find($id);

        if(empty($blog_post_comment))
        {
            return response()->json([
                'message' => 'Blog post comment not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Blog post comment retrived successfully.',
            'data' => $blog_post_comment
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $blog_post_comment = BlogPostComment::find($id);

        if(empty($blog_post_comment))
        {
            return response()->json([
                'message' => 'Blog post comment not found.'
            ], 404);
        }

        $blog_post_comment = BlogPostComment::find($id)->update($request->all());

        if($blog_post_comment)
        {
            return response()->json([
                'message' => 'Blog post comment update successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post comment cannot be updated.'
        ], 400);
    }

    public function destroy($id)
    {
        $blog_post_comment = BlogPostComment::find($id);

        if(empty($blog_post_comment))
        {
            return response()->json([
                'message' => 'Blog post comment not found.'
            ], 404);
        }

        if($blog_post_comment->delete())
        {
            return response()->json([
                'message' => 'Blog post comment deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post comment cannot be deleted.'
        ], 200);
    }
}
