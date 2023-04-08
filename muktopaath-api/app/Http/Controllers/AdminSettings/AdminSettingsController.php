<?php

namespace App\Http\Controllers\AdminSettings;

use App\Http\Controllers\Controller;
use App\Models\AdminSettings\Language;
use App\Models\AdminSettings\Category;
use App\Models\AdminSettings\ParticipantType;
use App\Models\Myaccount\User;
use App\Models\Myaccount\InstitutionInfo;
use Muktopaath\Course\Models\Course\Course;
use Muktopaath\Course\Models\Course\CourseBatch;
use Illuminate\Http\Request;
use DB;


class AdminSettingsController extends Controller
{
    public function language(){

    	$res = Language::all();
    	return response()->json($res);
    }

    public function participant_types(){

        
        $res = ParticipantType::all();


        return response()->json($res);

    }

    public function stats(){

        $users = User::select(DB::raw('COUNT(id) as total'))->value('total');
        $partners = InstitutionInfo::select(DB::raw('COUNT(id) as total'))
        ->where('status',1)
        ->value('total');
        
        $course = CourseBatch::where('published',1)->where('published_status',1)->value(DB::raw('COUNT(id)'));

        $date = date("Y-m-d H:i:s",strtotime('+6 Hours'));

        $current_course = CourseBatch::where('published',1)->where('published_status',1)->distinct('course_id')->count('id');
        // $current_course = CourseBatch::where('published_status', 1)
        //   ->where(function($q) use($date){
        //       $q->where('end_date','>=',$date)->orWhereNull('end_date');
        //     })->distinct('course_id')->select('id')->count();
  

        $data = [
            'users' => round(($users/1000000),1),
            'partners' => $partners,
            'course' => $course,
            'current_course' => $current_course
        ];

        return response()->json($data);
    }

    public function upazilaInfo($upazila_id){
        $data = DB::table('geo_upazilas')
                ->select('geo_division_id','geo_district_id')
                ->where('id',$upazila_id)
                ->first();

        return response()->json($data);
    }

    public function category()
    {
    	$res = Category::all();
    	return response()->json($res);
    }

    public function divisions()
    {
        $res = DB::table('divisions')->get();
        return response()->json($res);
    }

    public function districts($division_id)
    {
        $res = DB::table('districts')->where('division_id',$division_id)->get();
        return response()->json($res);
    }

    public function upazilas($district_id)
    {
        $res = DB::table('geo_upazilas')->where('geo_district_id',$district_id)->get();
        return response()->json($res);
    }


}