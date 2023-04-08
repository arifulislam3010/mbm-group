<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class LearningContent extends Model
{
    protected $table= "learning_contents";

    protected $fillable = [
        'id', 'content_type', 'title', 'description', 'more_data_info', 'content_id', 'created_by', 'updated_by'
    ];

    public function contentBankData(){
        return $this->belongsTo('App\Models\Filemanager\ContentBank' , 'content_id');
    }
}
