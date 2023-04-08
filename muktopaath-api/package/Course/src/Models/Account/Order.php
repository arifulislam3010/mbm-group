<?php

namespace Muktopaath\Course\Models\Account;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    
    public function courseBatch(){
    	return $this->hasMany('Muktopaath\Models\Course\CourseEnrollment','order_id');
    }
    public function Enrollement(){
    	return $this->hasMany('Muktopaath\Models\Course\CourseEnrollment','order_id')->with('courseBatch');
    }
    public function EnrollementOne(){
    	return $this->hasOne('Muktopaath\Models\Course\CourseEnrollment','order_id');
    }
    public function RunnigCourse(){
    	return $this->hasMany('Muktopaath\Models\Course\CourseEnrollment','order_id')->where('course_completeness','<', 100);
    }
    public function CompletedCourse(){
    	return $this->hasMany('Muktopaath\Models\Course\CourseEnrollment','order_id')->where('course_completeness','>=', 100);
    }
    public function IncompletedCourse(){
    	return $this->hasMany('Muktopaath\Models\Course\CourseEnrollment','order_id')->where('course_completeness','<', 0);
    }
    public function courseBatchInfo(){
    	return $this->hasManyThrough('Muktopaath\Models\Course\CourseBatch', 'App\Models\Course\CourseEnrollment', 'order_id', 'id', 'id', 'course_batch_id');
    }
    public function getUser(){
        return $this->belongsTo('App\Models\Myaccount\User', 'user_id')->with(['getUserInfo' => function($q){
            $q->select('id','photo_name','user_id');
        },'photo']);
    }
 
    
    public function User()
    {
        return $this->belongsTo('App\Models\Myaccount\User','user_id')->with('UserInfo');
    }
}
