<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class LearningCbLesson extends Model
{
    protected $connection = 'course';
    
    protected $table= "learning_cb_lessons";

    protected $fillable = [
        'id', 'title', 'description', 'unit_id', 'learning_content_id', 'quiz', 'quiz_data', 'order_number', 'tags', 'status', 'created_by', 'updated_by'
    ];

    public function unitData(){
        return $this->belongsTo('Muktopaath\Course\Models\Course\LearningCbUnit', 'unit_id');
    }

    public function contentData(){
        return $this->belongsTo('Muktopaath\Course\Models\Course\LearningContent' , 'learning_content_id')->with('contentBankData');
    }
}
