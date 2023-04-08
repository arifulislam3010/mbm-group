<?php

namespace App\Http\Controllers\AdminSettings;

use App\Repositories\Validation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSettings\Tag;

class TagsController extends Controller
{
    public function search(Request $request){
        $res = Tag::Select('id','title')
        ->where('title','like','%'.$request->title.'%')
        ->get();

        return response()->json($res);
    }

}