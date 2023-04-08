<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Myaccount\InstitutionInfo;
use DB;

class CourseBatch extends Model
{
    use SoftDeletes;

    protected $connection = 'course';


    
    public function course(){
    	return $this->belongsTo('Muktopaath\Course\Models\Course\Course','course_id')->with(['thumbnail','promovideo','batchInfo'])->with('coursetags');
    }

    public function lessons(){
        return $this->hasMany('Muktopaath\Course\Models\Course\Syllabus')->with('lessons','Completeness','UserFeedback')->where('parent_id',null)->orderBy('order_number','ASC');
    }

    public function passed(){
        return $this->hasMany('Muktopaath\Course\Models\Course\SyllabusStatus')
        ->whereRaw('syllabus_statuses.mark > syllabus_statuses.pass_mark');
    }

    public function sessions(){
        return $this->hasMany('Muktopaath\Course\Models\Course\Syllabus')->with('lessons')->where('parent_id',null)->orderBy('order_number','ASC');
    }

    public function units(){
        return $this->hasMany('Muktopaath\Course\Models\Course\Syllabus')->with('lessons')->where('parent_id',null)->orderBy('order_number','ASC');
    }

    public function completion_date(){
        return $this->hasOne('Muktopaath\Course\Models\Course\Syllabus','course_batch_id','id','completeness.id')
        ->join('completeness','completeness.syllabus_id','syllabuses.id')->orderBy('syllabuses.id','DESC')
        ->select(['syllabuses.id','completeness.id as completeness_id','syllabuses.course_batch_id','completeness.created_at']);
    }

    public function payment(){
        return $this->hasMany('Muktopaath\Course\Models\Course\Payment','course_batch_id');
    }

    public function total_payment(){
        return $this->hasOne('Muktopaath\Course\Models\Course\CourseEnrollment','course_batch_id')
        ->join('orders','orders.id','course_enrollments.order_id')
        ->select([DB::raw('SUM(orders.amount) as amount'),'course_batch_id'])
        ->groupBy('course_batch_id');;
    }

    public function progress(){
        
        return $this->hasManyThrough('Muktopaath\Course\Models\Course\Completeness','Muktopaath\Course\Models\Course\Syllabus');
    }

    public function reviewals(){
        return $this->hasMany('Muktopaath\Course\Models\Course\SyllabusStatus')->where('mark',null);
    }

    public function certificate(){
        return $this->hasOne('Muktopaath\Course\Models\Course\CertificateTemplate','course_id','id')->with('background');
    }


    public function certificates(){
        return $this->hasManyThrough('Muktopaath\Course\Models\Course\CertificateSubmit','Muktopaath\Course\Models\Course\CourseEnrollment');
    }

    //accounts related relationships

    public function pending(){
        return $this->hasOne('Muktopaath\Course\Models\Course\Payment')
        ->where('payments.status',0)
        ->select(DB::raw('SUM(amount) as amount'),'course_batch_id')
        ->groupBy('course_batch_id');
    }

    public function withdrawed(){
        return $this->hasOne('Muktopaath\Course\Models\Course\Payment')
        ->where('payments.status',1)
        ->select(DB::raw('SUM(amount) as amount'),'course_batch_id')
        ->groupBy('course_batch_id');
    }


    // public function Language(){
    //     return $this->belongsTo('App\Models\AdminAppSetting\Language','language_id');
    // }
    public function owner(){
        return $this->hasOne(InstitutionInfo::class,'id','owner_id')->with('logo');
    }
    
    public function UpdatedBy()
    {
        return $this->belongsTo('App\Models\Myaccount\User','created_by');
    }
    public function CreatedBy()
    {
        return $this->belongsTo('App\Models\Myaccount\User','created_by');
    }
    public function Rating()
    {
        $db = config()->get('database.connections.my-account.database');
        
        return $this->hasManyThrough('Muktopaath\Course\Models\Course\Rating','Muktopaath\Course\Models\Course\CourseEnrollment','course_batch_id','enrollement_id','id','id')->join('orders','course_enrollments.order_id', '=', 'orders.id')
        ->join($db.'.users','orders.user_id', '=','users.id')
        ->join($db.'.user_infos','users.id', '=','user_infos.user_id')
        ->select('users.name','users.username','user_infos.photo_name','course_enrollment_rating.rating_point','course_enrollment_rating.feedback_comments')->take(10);
    }
    // public function Coordinator()
    // {
    //     return $this->hasMany('App\Models\UserManagement\BatchAssign','course_batch_id')->where('status','=',1)->where('role_id',6);
    // }

    // public function CoordinatorInfo()
    // {
    //     return $this->hasMany('App\Models\UserManagement\BatchAssign','course_batch_id')
    //         ->with('user')
    //         ->where('status','=',1)->where('role_id',6);
    // }
    // public function Facilitator()
    // {
    //     return $this->hasMany('App\Models\UserManagement\BatchAssign','course_batch_id')->where('status','=', 1)->where('role_id',7);
    // }
    // public function FacilitatorInfo()
    // {
    //     return $this->hasMany('App\Models\UserManagement\BatchAssign','course_batch_id')
    //         ->with('user')
    //         ->where('status','=', 1)->where('role_id',7);
    // }
    // public function Moderator()
    // {
    //     return $this->hasMany('App\Models\UserManagement\BatchAssign','course_batch_id')->where('status','=', 1)->where('role_id',8);
    // }
    // public function allUnits()
    // {
    //     return $this->hasMany('Muktopaath\Course\Models\Course\LearningCbUnit', 'course_batch_id', 'id')->orderBy('order_number', 'asc');
    // }
}
