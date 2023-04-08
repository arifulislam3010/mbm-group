<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\SyllabusStatus;

class CourseEnrollment extends Model
{
    protected $connection = 'course';
    
    protected $guarded = ['created_at','updated_at'];
    

    public function submissions(){
      return $this->hasOneThrough(Syllabus::class,SyllabusStatus::class,'syllabus_status_id');
    }

    public function manual_submissions(){
      return $this->hasOne(SyllabusStatus::class)
                ->where('syllabus_id',config()->get('global.syllabus_id'));
    }

    public function orderId(){
    	return $this->belongsTo('Muktopaath\Course\Models\Account\Order' , 'order_id');
    }

    public function orderInfo(){
    	return $this->belongsTo('Muktopaath\Course\Models\Account\Order' , 'order_id')->with(['getUser' => function($q){
            $q->select('id','name','username');
        }]);
    }

    public function courseBatch(){
    	return $this->belongsTo('Muktopaath\Course\Models\Course\CourseBatch' , 'course_batch_id')->with('owner','lessons');
    }

    public function courseBatchAllInfo(){
    	return $this->belongsTo('Muktopaath\Course\Models\Course\CourseBatch' , 'course_batch_id')
    	    ->with('owner')
    	    ->with('CoordinatorInfo')
    	    ->with('FacilitatorInfo');
    }

    public function courseRating(){
    	return $this->hasOne('Muktopaath\Course\Models\Course\Rating' , 'enrollement_id');
    }

    public function attendance(){
        return $this->hasOne('Muktopaath\Course\Models\Course\Attendance' ,'course_enrollment_id');
    }

    public function attachments()
    {
        return $this->hasMany('Muktopaath\Course\Models\Course\EnrolledAttachment','enrollment_id')->orderBy('index_key','ASC')->with('files');
    }

    public function Attachment()
    {
        return $this->hasMany('Muktopaath\Course\Models\Course\EnrolledAttachment' , 'enrollment_id')->orderBy('index_key','ASC')->with('files');
    }
}
