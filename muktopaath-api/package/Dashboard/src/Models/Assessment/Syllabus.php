<?php

namespace Muktopaath\Dashboard\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use App\Models\ContentBank\LearningContent;
use Muktopaath\Dashboard\Models\Assessment\CourseBatch;
use Muktopaath\Dashboard\Models\Assessment\ConferenceToolsDetails;
use Muktopaath\Dashboard\Models\Assessment\SyllabusStatus;
use Illuminate\Database\Eloquent\SoftDeletes;

class Syllabus extends Model
{
  use SoftDeletes;

	protected $connection = 'assessment';
	protected $table = 'syllabuses';

    protected $guarded = ['created_at','updated_at'];


    public function contents(){
    	  return $this->belongsTo(LearningContent::class,'learning_content_id');
    }

    public function live_class_info(){
          return $this->hasOne(ConferenceToolsDetails::class,'syllabus_id')->with('credentials');
    }

    public function class(){
        return $this->belongsTo(Syllabus::class,'parent_id')->select('id','title');
    }

    public function batch(){
        return $this->belongsTo(CourseBatch::class,'course_batch_id');
    }

    public function participations(){
        return $this->hasMany(SyllabusStatus::class);
    }

    public function submissions(){
        return $this->hasMany(SyllabusStatus::class)->join('course_enrollments','course_enrollments.id','syllabus_statuses.course_enrollment_id')->join('orders','orders.id','course_enrollments.order_id')
      ->where('orders.user_id',config()->get('global.user_id'));
    }
    
}
