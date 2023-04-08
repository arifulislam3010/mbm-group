<?php

namespace App\Http\Controllers\ContentBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContentBank\LearningContent;

class FrontendController extends Controller
{ 
    public function content(){

        $featured  = LearningContent::orderBy('id','DESC')->take(10)->get();
        $popular   = LearningContent::orderBy('id','DESC')->take(10)->get();
        $latest    = LearningContent::orderBy('id','DESC')->take(10)->get();
        $running   = LearningContent::orderBy('id','DESC')->take(10)->get();
        $completed = LearningContent::orderBy('id','DESC')->take(10)->get();
        $upcoming  = LearningContent::orderBy('id','DESC')->take(10)->get();

        $contents = array('featured' => $featured, 'popular' => $popular, 'latest' => $latest, 'running' => $running, 'completed' => $completed, 'upcoming' => $upcoming);
        return response()->json($contents,200);
    }

    public function details($id){
    	$contents  = LearningContent::find($id);
          if($contents){
        return response()->json($contents,200);
    }else{
        return response()->json(['message' => 'No content found']);
    }

    }

    public function all(){

        $contents  = LearningContent::paginate(10);
        return response()->json($contents,200);


    }
    
}



