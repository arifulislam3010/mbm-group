<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseContentUserFeedback extends Model
{
    
    protected $connection = 'course';
    protected $table = 'course_content_user_feedbacks';
    protected $fillable = ['course_batch_id','unit_id','lesson_id','user_id','liked','disliked','flagged','flag_report'];
}
