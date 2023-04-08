<?php

namespace App\Http\Controllers\MyAccount\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

//  Models...
use App\Models\Blog\BlogPostDislike;

class BlogPostDislikeController extends Controller
{
    public function index()
    {
        $blog_post_dislike = BlogPostDislike::all();

        return $blog_post_dislike;
    }

    public function store(Request $request)
    {
        $blog_post_dislike = BlogPostDislike::create($request->all());

        if($blog_post_dislike)
        {
            return response()->json([
                'message' => 'Blog post dislike created successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post dislike cannot be created.'
        ], 400);
    }

    public function show($id)
    {
        $blog_post_dislike = BlogPostDislike::find($id);

        if(empty($blog_post_dislike))
        {
            return response()->json([
                'message' => 'Blog post dislike not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Blog post dislike retrived successfully.',
            'data' => $blog_post_dislike
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $blog_post_dislike = BlogPostDislike::find($id);

        if(empty($blog_post_dislike))
        {
            return response()->json([
                'message' => 'Blog post dislike not found.'
            ], 404);
        }

        $blog_post_dislike = BlogPostDislike::find($id)->update($request->all());

        if($blog_post_dislike)
        {
            return response()->json([
                'message' => 'Blog post dislike update successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post dislike cannot be updated.'
        ], 400);
    }

    public function destroy($id)
    {
        $blog_post_dislike = BlogPostDislike::find($id);

        if(empty($blog_post_dislike))
        {
            return response()->json([
                'message' => 'Blog post dislike not found.'
            ], 404);
        }

        if($blog_post_dislike->delete())
        {
            return response()->json([
                'message' => 'Blog post dislike deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post dislike cannot be deleted.'
        ], 200);
    }
}
