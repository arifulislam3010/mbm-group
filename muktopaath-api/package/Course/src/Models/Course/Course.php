<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $connection = 'course';


    protected $guarded = ['created_at','updated_at'];

    public function category(){
    	return $this->belongsTo('Muktopaath\Course\Models\Course\CourseCategory','cat_id');
    }

    public function batch(){
    	return $this->hasMany('Muktopaath\Course\Models\Course\CourseBatch','course_id')->withCount('sessions','reviewals');
    }


    public function contentBankId(){
        return $this->belongsTo('App\Models\Filemanager\ContentBank', 'course_cb_id', 'id');
    }

    public function tags(){
        return $this->belongsToMany('Muktopaath\Course\Models\Course\CourseTag','course_tags')->with('taginfo');
    }

    public function coursetags(){
        return $this->hasMany('Muktopaath\Course\Models\Course\CourseTag')->with('taginfo');
    }

    public function thumbnail()
    {
        return $this->belongsTo('App\Models\Filemanager\ContentBank', 'course_cb_id', 'id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }

    public function promovideo(){
        return $this->belongsTo('App\Models\Filemanager\ContentBank', 'course_video_id', 'id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }

    public function courseCategory(){
        return $this->belongsTo('App\Models\AdminSettings\Category','cat_id')->select('id','title','bn_title','image');
    }

    public function batchInfo(){
        return $this->hasMany('Muktopaath\Course\Models\Course\CourseBatch' , 'course_id' , 'id')->where('published_status',1)
            ->where('courses_for_status',0);
    }
    public function batchTotal(){
        return $this->hasMany('Muktopaath\Course\Models\Course\CourseBatch' , 'course_id' , 'id');
    }

    //  public function Tags()
    // {
    //     return $this->hasManyThrough('App\Models\AdminSettings\Tag','Muktopaath\Course\Models\Course\CourseTag','course_id','id','id','tag_id');
    // }



    public function languageId(){
        return $this->belongsTo('App\Models\AdminSettings\Language','language_id')->select('id','title','prefix');
    }
}
