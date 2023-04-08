<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;
use App\Models\ContentBank\LearningContent;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Models\Course\SyllabusStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use Muktopaath\Course\Models\Course\Completeness;
use Muktopaath\Course\Models\Course\CourseContentUserFeedback;

class Syllabus extends Model
{
  use SoftDeletes;
 
	protected $connection = 'course';
	protected $table = 'syllabuses';

    protected $guarded = ['created_at','updated_at'];


    public function contents(){
    	  return $this->belongsTo(LearningContent::class,'learning_content_id')->with('files');
    }
   

    public function lessons(){
        return $this->hasMany(Syllabus::class,'parent_id')->with('contents','submissions')->orderBy('order_number','ASC');
    }
    
    // public function lessonIds(){
    //   return $this->belongsTo(Syllabus::class,'parent_id')->orderBy('order_number','ASC')->>pluck('id')->toArray();
    // }

    
    public function batch(){
        return $this->belongsTo(CourseBatch::class,'course_batch_id');
    }

    public function submissions(){
        return $this->hasMany(SyllabusStatus::class)->join('course_enrollments','course_enrollments.id','syllabus_statuses.course_enrollment_id')->join('orders','orders.id','course_enrollments.order_id')->where('orders.user_id',config()->get('global.user_id'));
    }

    public function Completeness(){
        return $this->hasOne(Completeness::class)->where('user_id',config()->get('global.user_id'))->orderBy('completeness','DESC');
    }

    public function participations(){
        return $this->hasMany(SyllabusStatus::class);
    }
    
    public function CourseContentUserFeedback(){
      return $this->hasOne(CourseContentUserFeedback::class,'lesson_id');
    }
    public function UserFeedback(){
      return $this->hasOne(CourseContentUserFeedback::class,'lesson_id')->where('user_id',config()->get('global.user_id'));
    }
    
}
