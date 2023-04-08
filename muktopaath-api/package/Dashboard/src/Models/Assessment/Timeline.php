<?php

namespace Muktopaath\Dashboard\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Muktopaath\Dashboard\Models\Assessment\TimelineComment;
use App\Models\Filemanager\ContentBank;

class Timeline extends Model
{
    protected $guarded = ['created_at','updated_at'];


    public function comments(){
        return $this->hasMany(TimelineComment::class)->with('user');
    }

    public function files(){
        return $this->hasOne(ContentBank::class,'id','file_id')->select(['id','type','is_url','file_encode_path','file_main_path']);
    }
}
