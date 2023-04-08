<?php

namespace App\Http\Controllers\MyAccount\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

//  Models...
use App\Models\Blog\BlogTag;

class BlogTagController extends Controller
{
    public function index()
    {
        $blog_tags = BlogTag::all();

        return $blog_tags;
    }

    public function store(Request $request)
    {
        $blog_tag = BlogTag::create($request->all());

        if($blog_tag)
        {
            return response()->json([
                'message' => 'Blog tag created successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog tag cannot be created.'
        ], 400);
    }

    public function show($id)
    {
        $blog_tag = BlogTag::find($id);

        if(empty($blog_tag))
        {
            return response()->json([
                'message' => 'Blog tag not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Blog tag retrived successfully.',
            'data' => $blog_tag
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $blog_tag = BlogTag::find($id);

        if(empty($blog_tag))
        {
            return response()->json([
                'message' => 'Blog tag not found.'
            ], 404);
        }

        $blog_tag = BlogTag::find($id)->update($request->all());

        if($blog_tag)
        {
            return response()->json([
                'message' => 'Blog tag update successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog tag cannot be updated.'
        ], 400);
    }

    public function destroy($id)
    {
        $blog_tag = BlogTag::find($id);

        if(empty($blog_tag))
        {
            return response()->json([
                'message' => 'Blog tag not found.'
            ], 404);
        }

        if($blog_tag->delete())
        {
            return response()->json([
                'message' => 'Blog tag deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog tag cannot be deleted.'
        ], 200);
    }
}
