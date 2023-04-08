<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Assessment\CourseEnrollment;
use App\Models\Filemanager\ContentBank;

class SyllabusStatus extends Model
{
    protected $table = 'syllabus_statuses';

    protected $guarded = ['created_at','updated_at'];

    public function enrollments(){
        return $this->hasMany(CourseEnrollment::class);
    }

    public function files(){
        return $this->hasOne(ContentBank::class,'id','file_id')->select(['id','type','file_encode_path','file_main_path']);
    }

}
