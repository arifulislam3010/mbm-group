<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;
use Muktopaath\Course\Models\Course\TimelineComment;
use App\Models\Filemanager\ContentBank;

class Timeline extends Model
{
    protected $guarded = ['created_at','updated_at'];


    public function comments(){
        return $this->hasMany(TimelineComment::class)->with('user');
    }

    public function files(){
        return $this->hasOne(ContentBank::class,'id','file_id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }
}
