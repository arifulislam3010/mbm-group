<?php

namespace App\Http\Controllers\MyAccount\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

//  Models...
use App\Models\Blog\BlogPostPublish;

class BlogPostPublishController extends Controller
{
    public function index()
    {
        $blog_post_publish = BlogPostPublish::all();

        return $blog_post_publish;
    }

    public function store(Request $request)
    {
        $blog_post_publish = BlogPostPublish::create($request->all());

        if($blog_post_publish)
        {
            return response()->json([
                'message' => 'Blog post publish created successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post publish cannot be created.'
        ], 400);
    }

    public function show($id)
    {
        $blog_post_publish = BlogPostPublish::find($id);

        if(empty($blog_post_publish))
        {
            return response()->json([
                'message' => 'Blog post publish not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Blog post publish retrived successfully.',
            'data' => $blog_post_publish
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $blog_post_publish = BlogPostPublish::find($id);

        if(empty($blog_post_publish))
        {
            return response()->json([
                'message' => 'Blog post publish not found.'
            ], 404);
        }

        $blog_post_publish = BlogPostPublish::find($id)->update($request->all());

        if($blog_post_publish)
        {
            return response()->json([
                'message' => 'Blog post publish update successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post publish cannot be updated.'
        ], 400);
    }

    public function destroy($id)
    {
        $blog_post_publish = BlogPostPublish::find($id);

        if(empty($blog_post_publish))
        {
            return response()->json([
                'message' => 'Blog post publish not found.'
            ], 404);
        }

        if($blog_post_publish->delete())
        {
            return response()->json([
                'message' => 'Blog post publish deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post publish cannot be deleted.'
        ], 200);
    }
}
