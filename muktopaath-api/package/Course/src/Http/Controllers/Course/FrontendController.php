<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Muktopaath\Course\Models\Course\Course;
use Yaml;

class FrontendController extends Controller
{
    public function course(){
        
        $role = Yaml::parse(file_get_contents(resource_path('yaml/role/assessments.yaml')));
 
        return $role['admin']['batch'];
        if(array_key_exists('restricted_user',$role['admin']['batch'])){
            return 'yes';
        }else{
            return 'not found';
        }

        $featured  = Course::orderBy('id','DESC')->take(10)->get();
        $popular   = Course::orderBy('id','DESC')->take(10)->get();
        $latest    = Course::orderBy('id','DESC')->take(10)->get();
        $running   = Course::orderBy('id','DESC')->take(10)->get();
        $completed = Course::orderBy('id','DESC')->take(10)->get();
        $upcoming  = Course::orderBy('id','DESC')->take(10)->get();

        $courses = array('featured' => $featured, 'popular' => $popular, 'latest' => $latest, 'running' => $running, 'completed' => $completed, 'upcoming' => $upcoming);
        return response()->json($courses,200);
    }

    public function details($id){

    	$course  = Course::find($id);
        if($course){
        return response()->json($course,200);
    }else{
        return response()->json(['message' => 'Content not found']);
    }

    }

    public function all(){

    	$course  = Course::paginate(10);
        return response()->json($course,200);

    }
    
}
