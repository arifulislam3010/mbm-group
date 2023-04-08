<?php

namespace App\Http\Controllers\MyAccount;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Myaccount\Gamification\GamificationPoint;
use App\Models\Myaccount\Gamification\Gamification;
use App\Models\Myaccount\Gamification\Badge;
use App\Models\Myaccount\Gamification\BadgeLevel;
use App\Models\Myaccount\Gamification\UserBadge;
use Auth;
use DB;
use App\Lib\GamificationClass;
use App\Http\Resources\Myaccount\GamificationPoint as GamificationPointResource;
use App\Http\Resources\Myaccount\Gamification as GamificationResource;
class GamificationController extends Controller
{
    public function badges(){
        $badges = UserBadge::where('user_id',Auth::user()->id)->groupBy('badge_id')->select('badge_id',DB::raw('count(*) as level'))->with('badge')->get();
        
        return $badges;
    }

    public function allBadges(){
        $badges = UserBadge::where('user_id',Auth::user()->id)->groupBy('badge_id')->select('badge_id',DB::raw('count(*) as level'))->with('badge')->get();
        
        return $badges;
    }
    
    public function courses(Request $request){        
        $user_id = Auth::user()->id;
        $unit_id = $request['unit_id'];
        $lesson_id = $request['lesson_id'];
        $table_id = $request['course_batch_id'];
        $slug = $request['slug'];
        $status = $request['status'];
        $gamification_class = new GamificationClass;
        return $gamification_class->gamificationStoreCourse($slug,$user_id,$table_id,$status,$unit_id,$lesson_id);
    }
    
    public function leaderboard(Request $request){
        $data = [
            'point_this_week'=>[],
            'point_last_week'=>[],
            'point_this_month'=>[],
            'point_last_month'=>[],
            'badge_this_week'=>[],
            'badge_last_week'=>[],
            'badge_this_month'=>[],
            'badge_last_month'=>[],
        ];

       return $data;

        $day = date('w');
        $week_start = date('Y-m-d 00:00:00', strtotime('-'.$day.' days'));
        $week_end = date('Y-m-d 23:59:59', strtotime('+'.(6-$day).' days'));
        $last_week_start = date('Y-m-d 00:00:00', strtotime('-'.(7+$day).' days'));
        $last_week_end = date('Y-m-d 23:59:59', strtotime('-'.(1+$day).' days'));
        $week_start.'-'.$week_end.'-'.'-'.$last_week_start.'-'.$last_week_end;
        $month = date('m');
        $month_start =  date('Y-m-01 00:00:00', strtotime(date('Y-m-d')));
        $month_end = date('Y-m-t 23:59:59', strtotime(date('Y-m-d')));
        $last_month_start = date("Y-m-d 00:00:00", strtotime("first day of previous month"));
        $last_month_end = date("Y-m-d 23:59:59", strtotime("last day of previous month"));
        if($request['course_batch_id']>0){
            $course_batch_id = $request['course_batch_id'];
            return Gamification::groupBy('user_id')->select('user_id', DB::raw('SUM(points) as total_point'))->orderBy('total_point','DESC')->take(5)->with('UserInfo')->where('table_id',$course_batch_id)->where('unit_id','<>',null)->where('lesson_id','<>',null)->get();
        }
        //return $request->all();
        //return  $point_this_week = UserBadge::groupBy('user_id')->select('user_id',DB::raw('count(badge_id) as badge'),DB::raw('count(badge_level_id) as level'))->orderBy('badge','DESC')->orderBy('level','DESC')->take(10)->whereBetween('updated_at', [$week_start,$week_end])->with('User')->get();


        $point_this_week = Gamification::groupBy('user_id')->select('user_id', DB::raw('SUM(points) as total_point'))->orderBy('total_point','DESC')->take(10)->with('UserInfo')->whereBetween('updated_at', [$week_start, $week_end])->get();
        $point_last_week = Gamification::groupBy('user_id')->select('user_id', DB::raw('SUM(points) as total_point'))->orderBy('total_point','DESC')->take(10)->with('UserInfo')->whereBetween('updated_at', [$last_week_start, $last_week_end])->get();
        $point_this_month = Gamification::groupBy('user_id')->select('user_id', DB::raw('SUM(points) as total_point'))->orderBy('total_point','DESC')->take(10)->with('UserInfo')->whereBetween('updated_at', [$month_start, $month_end])->get();
        $point_last_month = Gamification::groupBy('user_id')->select('user_id', DB::raw('SUM(points) as total_point'))->orderBy('total_point','DESC')->take(10)->with('UserInfo')->whereBetween('updated_at', [$last_month_start, $last_month_end])->get();
        
        
        $badge_this_week = UserBadge::groupBy('user_id')->select('user_id', DB::raw('COUNT(badge_id) AS total_badges'), DB::raw('SUM(badge_level_id) AS total_levels'), DB::raw('COUNT(badge_id)*SUM(badge_level_id) AS total_values'))->orderBy('total_badges','DESC')->orderBy('total_values','DESC')->take(10)->with('User')->whereBetween('updated_at', [$week_start, $week_end])->get();
        $badge_last_week = UserBadge::groupBy('user_id')->select('user_id', DB::raw('COUNT(badge_id) AS total_badges'), DB::raw('SUM(badge_level_id) AS total_levels'), DB::raw('COUNT(badge_id)*SUM(badge_level_id) AS total_values'))->orderBy('total_badges','DESC')->orderBy('total_values','DESC')->take(10)->with('User')->whereBetween('updated_at', [$last_week_start, $last_week_end])->get();
        $badge_this_month = UserBadge::groupBy('user_id')->select('user_id', DB::raw('COUNT(badge_id) AS total_badges'), DB::raw('SUM(badge_level_id) AS total_levels'), DB::raw('COUNT(badge_id)*SUM(badge_level_id) AS total_values'))->orderBy('total_badges','DESC')->orderBy('total_values','DESC')->take(10)->with('User')->whereBetween('updated_at', [$month_start, $month_end])->get();
        $badge_last_month = UserBadge::groupBy('user_id')->select('user_id', DB::raw('COUNT(badge_id) AS total_badges'), DB::raw('SUM(badge_level_id) AS total_levels'), DB::raw('COUNT(badge_id)*SUM(badge_level_id) AS total_values'))->orderBy('total_badges','DESC')->orderBy('total_values','DESC')->take(10)->with('User')->whereBetween('updated_at', [$last_month_start, $last_month_end])->get();

        // $badges = Badge::all();
        // $badge_this_week = Gamification::with('UserInfo')->whereBetween('updated_at', [$week_start, $week_end])->get();
        // $badge_last_week = Gamification::with('UserInfo')->whereBetween('updated_at', [$last_week_start, $last_week_end])->get();
        // $badge_this_month = Gamification::with('UserInfo')->whereBetween('updated_at', [$month_start, $month_end])->get();
        // $badge_last_month = Gamification::with('UserInfo')->whereBetween('updated_at', [$last_month_start, $last_month_end])->get();
        // $badge_this_week_List = [];
        // foreach ($badge_this_week as $key => $value) {
        //     $badge_this_week_List[$key] = [
        //          'badge'=> $this->badgesCheck($badges,$value->user_id),
        //          'user' => $value->UserInfo,
        //       ];
        // }
        // $badge_last_week_List = [];
        // foreach ($badge_last_week as $key => $value) {
        //     $badge_last_week_List[$key] = [
        //          'badge'=> $this->badgesCheck($badges,$value->user_id),
        //          'user' => $value->UserInfo,
        //       ];
        // }
        // $badge_this_month_List = [];
        // foreach ($badge_this_month as $key => $value) {
        //     $badge_this_month_List[$key] = [
        //          'badge'=> $this->badgesCheck($badges,$value->user_id),
        //          'user' => $value->UserInfo,
        //       ];
        // }
        // $badge_last_month_List = [];
        // foreach ($badge_last_month as $key => $value) {
        //     $badge_last_month_List[$key] = [
        //          'badge'=> $this->badgesCheck($badges,$value->user_id),
        //          'user' => $value->UserInfo,
        //       ];
        // }

        $data = [
            'point_this_week'=>$point_this_week,
            'point_last_week'=>$point_last_week,
            'point_this_month'=>$point_this_month,
            'point_last_month'=>$point_last_month,
            'badge_this_week'=>$badge_this_week,
            'badge_last_week'=>$badge_last_week,
            'badge_this_month'=>$badge_this_month,
            'badge_last_month'=>$badge_last_month,
        ];

       return $data;
    }

    public function points(){
        $gamification_activity = GamificationPoint::where('status',1)->get();
        return GamificationPointResource::collection($gamification_activity);
    }

    public function pointDetails($id){
        $gamification = Gamification::where('user_id',Auth::user()->id)->where('gamification_point_id',$id)->paginate(1);
        return GamificationResource::collection($gamification);
    }
}
