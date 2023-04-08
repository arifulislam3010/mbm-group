<?php

namespace Muktopaath\Course\Models\Course;


use Illuminate\Database\Eloquent\Model;

class LearningCbUnit extends Model
{
    protected $connection = 'course';
    
    protected $table= "learning_cb_units";

    protected $fillable = [
        'id', 'title', 'description', 'status', 'tags', 'course_batch_id', 'order_number', 'created_by', 'updated_by'
    ];
}
