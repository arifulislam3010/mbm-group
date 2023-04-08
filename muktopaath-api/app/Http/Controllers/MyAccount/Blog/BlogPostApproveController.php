<?php

namespace App\Http\Controllers\MyAccount\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

//  Models...
use App\Models\Blog\BlogPostApprove;

class BlogPostApproveController extends Controller
{
    public function index()
    {
        $blog_post_approve = BlogPostApprove::all();

        return $blog_post_approve;
    }

    public function store(Request $request)
    {
        $blog_post_approve = BlogPostApprove::create($request->all());

        if($blog_post_approve)
        {
            return response()->json([
                'message' => 'Blog post approve created successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post approve cannot be created.'
        ], 400);
    }

    public function show($id)
    {
        $blog_post_approve = BlogPostApprove::find($id);

        if(empty($blog_post_approve))
        {
            return response()->json([
                'message' => 'Blog post approve not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Blog post approve retrived successfully.',
            'data' => $blog_post_approve
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $blog_post_approve = BlogPostApprove::find($id);

        if(empty($blog_post_approve))
        {
            return response()->json([
                'message' => 'Blog post approve not found.'
            ], 404);
        }

        $blog_post_approve = BlogPostApprove::find($id)->update($request->all());

        if($blog_post_approve)
        {
            return response()->json([
                'message' => 'Blog post approve update successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post approve cannot be updated.'
        ], 400);
    }

    public function destroy($id)
    {
        $blog_post_approve = BlogPostApprove::find($id);

        if(empty($blog_post_approve))
        {
            return response()->json([
                'message' => 'Blog post approve not found.'
            ], 404);
        }

        if($blog_post_approve->delete())
        {
            return response()->json([
                'message' => 'Blog post approve deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Blog post approve cannot be deleted.'
        ], 200);
    }
}
