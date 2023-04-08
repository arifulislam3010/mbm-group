<?php

namespace Muktopaath\Course\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Myaccount\User;

class Course extends Model
{
    use SoftDeletes;

    protected $table = 'courses';

    protected $dates = ['deleted_at'];

    public function contantBank(){
    	return $this->belongsTo('Muktopaath\Course\Models\ContentBank','course_cb_id');
    }

}