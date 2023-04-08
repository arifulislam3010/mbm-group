<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseTag extends Model
{
    protected $connection = 'course';

    protected $table  = 'course_tags';

    protected $guarded = ['created_at','updated_at'];

    public function taginfo()
    {
        return $this->belongsTo('App\Models\AdminSettings\Tag', 'course_tag_id', 'id')->select(['id','title']);
    }
}
