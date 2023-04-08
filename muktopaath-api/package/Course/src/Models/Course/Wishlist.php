<?php

namespace Muktopaath\Course\Models\Course;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public function courseBatch(){
    	return $this->belongsTo('Muktopaath\Course\Models\Course\CourseBatch','course_batch_id');
    }
}
