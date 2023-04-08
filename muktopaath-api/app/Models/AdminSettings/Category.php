<?php

namespace App\Models\AdminSettings;

use Illuminate\Database\Eloquent\Model;
use Muktopaath\Course\Models\Course\Course;
use DB;

class Category extends Model
{
	protected $connection = 'admin-settings';
    protected $guarded = ['created_at','updated_at'];


    public function total_course(){
    	return $this->hasMany(Course::class,'cat_id');
    }


    // public function enrollment(){
    //     return $this->hasMany(Course::class,'cat_id')->join('course_batches','course_batches.course_id','courses.id')->select(DB::raw('SUM(course_batches.total_enrollment)'),'course_batches.id as course_batch_id','courses.cat_id','courses.id as course_id')
    //         ->groupBy('course_batches.id');
    // }

    // public function enrollment(){
    //         return $this->hasMany(Course::class,'cat_id')->with('total_enrolled');
    // }
}