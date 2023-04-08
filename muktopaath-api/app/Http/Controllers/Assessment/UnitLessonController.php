<?php

namespace App\Http\Controllers\Assessment;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Assessment\UnitAndLessons;

class UnitLessonController extends Controller
{
    public function units($batch_id){
    	$res = UnitAndLessons::where('parent_id',null)
    			->where('course_batch_id',$batch_id)->get();
    	return response()->json($res);
    }

    public function create_unit(Request $request){
    	$data = UnitAndLessons::create($request->all());
    	return response()->json(['data'=> 'unit created successfully']);

    }

    public function create_lessons(Request $request){
    	$data = UnitAndLessons::create($request->all());
    	return response()->json(['data'=> 'Lesson created successfully']);

    }

    public function lesson_of_unit($batch_id, $unit_id){
    	$res = UnitAndLessons::where('parent_id',$unit_id)
    			->where('course_batch_id',$batch_id)->get();
    	return response()->json($res);
    }
}
