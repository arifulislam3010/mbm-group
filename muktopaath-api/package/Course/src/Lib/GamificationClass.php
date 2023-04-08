<?php

namespace Muktopaath\Course\Lib;
use App\Models\Gamification\GamificationPoint;
use App\Models\Gamification\Gamification;
use App\Models\Gamification\Badge;
use App\Models\Gamification\BadgeLevel;
use App\Models\Gamification\UserBadge;
use Auth;
use DB;
use App\Models\Course\CourseEnrollment;
class GamificationClass
{
    public function gamificationStore($type,$user_id,$table_id,$status){
        $GamificationPoint = GamificationPoint::where('slug_name',$type)->first();
        $gamification_status = 0;
        if($status==1){
           if($GamificationPoint['status']==1){
            if($GamificationPoint['type']==1){
                $Gamification = Gamification::where('user_id',$user_id)->where('slug_name',$type)->first();
                if(!$Gamification){
                    $gamification_status = 1;
                    $StoreGamification = new Gamification();
                    $StoreGamification->points = $GamificationPoint['points'];
                    $StoreGamification->user_id = $user_id;
                    $StoreGamification->gamification_point_id = $GamificationPoint['id'];
                    $StoreGamification->slug_name = $GamificationPoint['slug_name'];
                    $StoreGamification->table_id = $table_id;
                    $StoreGamification->save();
                }else{
                    $gamification_status = 2;
                }
            }else{
                 $data = Gamification::where('user_id',$user_id)->where('slug_name',$type)->where('table_id',$table_id)->first();
                if(!$data){
                    $gamification_status = 1;
                    $StoreGamification = new Gamification();
                    $StoreGamification->points = $GamificationPoint['points'];
                    $StoreGamification->user_id = $user_id;
                    $StoreGamification->gamification_point_id = $GamificationPoint['id'];
                    $StoreGamification->slug_name = $GamificationPoint['slug_name'];
                    $StoreGamification->table_id = $table_id;
                    $StoreGamification->save();
                }else{
                    $gamification_status = 2;
                }
                
            }
        } 
        }else{
            $data = Gamification::where('user_id',$user_id)->where('slug_name',$type)->where('table_id',$table_id)->first();
            if($data){
                $gamification_status = 4;
                $data->delete();
            }else{
                $gamification_status = 5;
            }
        }
       $badges = Badge::all();
       $this->badgesCheck($badges,$user_id);

    }
    public function gamificationStoreCourse($type,$user_id,$table_id,$status,$unit_id,$lesson_id){
        $courseEntrollment = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->where('orders.user_id',$user_id)->where('course_enrollments.course_batch_id',$table_id)->first();
        $ignore = 0;
        if($courseEntrollment){
            if($courseEntrollment['course_completeness']>=100){
                $ignore = 1;
            }else{
                $ignore = 0;
            }
        }else{
            $ignore = 1;
        }
        $GamificationPoint = GamificationPoint::where('slug_name',$type)->first();

        $gamification_status = 0;
        if($ignore==0){
           if($status==1){
                if($GamificationPoint['status']==1){
                    if($GamificationPoint['type']==1){
                       $data = Gamification::where('user_id',$user_id)->where('unit_id',$unit_id)->where('lesson_id',$lesson_id)->where('slug_name',$type)->where('table_id',$table_id)->first();
                        if(!$data){
                            
                            $StoreGamification = new Gamification();
                            $StoreGamification->points = $GamificationPoint['points'];
                            $StoreGamification->user_id = $user_id;
                            $StoreGamification->gamification_point_id = $GamificationPoint['id'];
                            $StoreGamification->slug_name = $GamificationPoint['slug_name'];
                            $StoreGamification->table_id = $table_id;
                            $StoreGamification->unit_id = $unit_id;
                            $StoreGamification->lesson_id = $lesson_id;
                            $StoreGamification->save();
                            $gamification_status = 1;
                        }else{
                            $gamification_status = 2;
                        }
                    }else{
                        $data = Gamification::where('user_id',$user_id)->where('unit_id',$unit_id)->where('lesson_id',$lesson_id)->where('slug_name',$type)->where('table_id',$table_id)->first();
                        if(!$data){
                            $gamification_status = 1;
                            $StoreGamification = new Gamification();
                            $StoreGamification->points = $GamificationPoint['points'];
                            $StoreGamification->user_id = $user_id;
                            $StoreGamification->gamification_point_id = $GamificationPoint['id'];
                            $StoreGamification->slug_name = $GamificationPoint['slug_name'];
                            $StoreGamification->table_id = $table_id;
                            $StoreGamification->unit_id = $unit_id;
                            $StoreGamification->lesson_id = $lesson_id;
                            $StoreGamification->save();
                            //$data = $StoreGamification;
                        }else{
                            $gamification_status = 2;
                        }
                        
                    }
                } 
            }else{
                $data = Gamification::where('user_id',$user_id)->where('unit_id',$unit_id)->where('lesson_id',$lesson_id)->where('slug_name',$type)->where('table_id',$table_id)->first();
                if($data){
                    $data->delete();
                    $gamification_status = 4;
                }else{
                    $gamification_status = 5;
                }
            } 
        }else{
            $gamification_status = 2;
        }
        
        $badges = Badge::all();
        $this->badgesCheck($badges,$user_id);
        $returnData = [
            'activity'=> $GamificationPoint->activity,
            'id'=>$GamificationPoint->id,
            'points'=>$GamificationPoint->points,
            'slug_name'=>$GamificationPoint->slug_name,
            'status'=> $GamificationPoint->status,
            'type'=> $GamificationPoint->type,
            'gamification_status'=>$gamification_status,
        ];
        return $returnData;
    }

    public function badgesCheck($badges,$id){
    	return true;
        $Gamification = Gamification::where('user_id',$id)->groupBy('slug_name')->select('slug_name', DB::raw('SUM(points) as total_point'),DB::raw('count(*) as count'))->get();
        $badgesData = [];
        $total_badge=0;
        foreach($badges as $key => $badge) {
            $badgesStatus = false;

            foreach ($badge->BadgeLevels as $keybl => $Level) {
                $levelStatus = false; 
                $levelCount = 0;
                $levelCountA = 0;
                foreach (json_decode($Level->activity_list) as $keya => $activity_list) {
                

                    if($this->CheckActivity($activity_list,$Gamification)){
                        $levelStatus = true;
                        $levelCount = $levelCount+1;
                    }else{
                        $levelStatus = false;
                    }
                    $levelCountA = $keya;
                }
                if($levelCountA==$levelCount){
                    $data = UserBadge::where('user_id',$id)->where('badge_level_id',$Level->id)->first();
                    if(!$data){
                        $store = new UserBadge();
                        $store->user_id = $id;
                        $store->badge_level_id = $Level->id;
                        $store->badge_id = $Level->badge_id;
                        $store->save();
                    }
                    
                }else{
                    $data = UserBadge::where('user_id',$id)->where('badge_level_id',$Level->id)->first();
                    if($data){
                        $data->delete();
                    }
                }
            }
        }
    }
    public function CheckActivity($activity_list,$Gamification){
        $status = false;
        foreach ($Gamification as $key => $value) {
            if($activity_list->activity_check){
                if($value->slug_name==$activity_list->slug_name && $value->count>=$activity_list->no_of_activity){
                    $status= true;
                    break;
                }else{
                    if($activity_list->no_of_activity==0){
                        $status=true;
                    }else{
                        $status=false;
                    }
                }
            }else{
                $status=true;
            }
            
        }
        return $status;
    }
}