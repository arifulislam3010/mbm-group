<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Assessment\TimelineComment;
use App\Models\Filemanager\ContentBank;

class Timeline extends Model
{
    protected $guarded = ['created_at','updated_at'];


    public function comments(){
        return $this->hasMany(TimelineComment::class)->with('user');
    }

    public function files(){
        return $this->hasOne(ContentBank::class,'id','file_id')->select(['id','type','is_url','file_encode_path','title','file_main_path']);
    }

    public function photo(){
        return $this->hasOne(ContentBank::class,'id','photo_id')->select(['id','type','is_url','file_encode_path','file_main_path']);
    }
}
