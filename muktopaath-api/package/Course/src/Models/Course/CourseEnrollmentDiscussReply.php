<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollmentDiscussReply extends Model
{
    protected $table = 'course_enrollment_discuss_replies';
    
    public function getUser(){
        return $this->belongsTo('App\Models\Myaccount\User', 'replier_id')->with(['getUserInfo' => function($q){
            $q->select('id','photo_name','user_id');
        }],'photo');
    }
    
    public function CreatedBy()
    {
        return $this->belongsTo('App\Models\Myaccount\User','replier_id');
    }
}
