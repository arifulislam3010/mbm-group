<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\RoleAssign as RoleAssignResources;
use App\Http\Resources\Order as OrderResources;
use App\Http\Resources\EnrollCourse as EnrollCourseResources;
use App\Http\Resources\AssignCourse as AssignCourseResources;
use App\Http\Resources\UserbasicInfo as UserbasicInfoResources;
use App\Http\Resources\UserInfo as UserInfoResources;
use App\Models\Course\CourseEnrollment;
use App\Models\Account\Order;
use App\Models\UserManagement\UserRoleAs;
use App\Models\UserManagement\RoleAssign;
use App\Models\UserManagement\BatchAssign;
use App\Models\Gamification\Gamification;
use App\Models\AdminAppSetting\FavoriteUserCatList;
use App\Http\Resources\Batch as ResourceBatch;
use Carbon\Carbon;
use App\Lib\ManualEncodeDecode;
class UserbasicInfo extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $today_date = Carbon::now()->format('Y-m-d');
        //$gamification = Gamification::where('user_id',$this->id)->sum('points');
        $gamification = 0;
          $FavoriteCategoryList = FavoriteUserCatList::select('category_id_list')->where('user_id',$this->id)->first();
        //$RoleAssignData = RoleAssign::leftJoin('institution_infos','institution_infos.id','role_assigns.owner_id')->where('institution_infos.status',1)->where('role_assigns.user_id',$this->id)->get();
        $TotalEnrollment = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('orders.user_id',$this->id)->count(); 
        $CourseCompleted = Order::join('course_enrollments','course_enrollments.order_id','orders.id')->where('orders.user_id',$this->id)->where('course_enrollments.course_completeness','>=',100)->count();
        $RunningT = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_enrollments.status',1)->where('course_batches.published_status', 1)
            ->where(function($q){
                $q->where('course_batches.end_date','>=',date('Y-m-d'))->orWhereNull('course_batches.end_date');
            })
        ->select('course_enrollments.*')->where('orders.user_id',$this->id)->whereNull('course_batches.deleted_at')->where('course_enrollments.course_completeness','<',100)->count();
        
         $IncompletedT = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_enrollments.status', 1)->where('course_batches.published_status', 1)->where('course_batches.end_date','<',$today_date)->select('course_enrollments.*')->where('orders.user_id',$this->id)->where('course_enrollments.course_completeness','<', 100)->count(); 
         $PendingT = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_enrollments.status', 0)->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('orders.user_id',$this->id)->count(); 
         
        $contributorData = UserRoleAs::where('user_id',$this->id)->get();
        $coordinator = 0;
        $facilitator = 0;
        $moderator = 0;

        $ManualEncodeDecode = new ManualEncodeDecode;
        $id_encode = $ManualEncodeDecode->encode($this->id.'-shibly','mukto123');

        foreach ($contributorData as $key => $value) {
            if($value->role_id==6 && $value->status==1){
                $coordinator = 1;
            }
            if($value->role_id==7 && $value->status==1){
                $facilitator = 1;
            }
            if($value->role_id==8 && $value->status==1){
                $moderator = 1;
            }
        }
        return [
            'coordinator'           =>$coordinator, 
            'facilitator'           =>$facilitator, 
            'moderator'             =>$moderator,
            'TotalEnrollment'       =>$TotalEnrollment, 
            'CourseCompleted'       =>$CourseCompleted,
            'RunningT'              =>$RunningT,
            'IncompletedT'          =>$IncompletedT,
            'PendingT'              =>$PendingT,
            'id'                    =>$this->id,
            'name'                  =>$this->name,
            'bn_name'               =>$this->bn_name,
            'certificate_name'      =>$this->certificate_name,
            'profilecompleteness'   =>$this->completeness,
            'attachments'           =>$this->Attachment,
            'username'              =>$this->username,
            'id_encode'             =>$id_encode,
            'phone'                 =>$this->phone,
            'email'                 =>$this->email,
            'phone2'                =>$this->phone2,
            'email2'                =>$this->email2,
            'token'                 =>$this->token,
            'session_id'            =>$this->session_id,
            'old_user'              =>$this->old_user,
            'old_user_pk'           =>$this->old_user_pk,
            'old_user_status'       =>$this->old_user_status,
            'old_user_points'       =>$this->old_user_points,
            'UserInfo'              =>new UserInfoResources($this->UserInfo),
            'gamification'          =>$gamification,
            'education_status'      =>$this->education_status,
//            'FavoriteCategoryList'  => $FavoriteCategoryList['category_id_list']?unserialize($FavoriteCategoryList['category_id_list']):[],
        ];
    }
}
