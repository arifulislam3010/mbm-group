<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollmentDiscuss extends Model
{
    protected $table = 'course_enrollment_discusses';
    
    public function getReplies(){
        return $this->hasMany('Muktopaath\Course\Models\Course\CourseEnrollmentDiscussReply', 'discuss_id')->with(['getUser' => function($q){
            $q->select('id','name','username');
        }]);
    }
    public function getEnroll(){
        return $this->belongsTo('Muktopaath\Course\Models\Course\CourseEnrollment', 'enrollment_id')->with(['orderInfo' => function($q){
            $q->select('id','user_id');
        }])->select('id','order_id');
    }
    
    public function enrollmentCourse(){
        return $this->hasMany('Muktopaath\Course\Models\Course\CourseEnrollment','course_batch_id');
    }
    
    public function Replies(){
        return $this->hasMany('Muktopaath\Course\Models\Course\CourseEnrollmentDiscussReply', 'discuss_id');
    }
}
