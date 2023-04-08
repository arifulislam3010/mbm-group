<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;
use App\Models\Filemanager\ContentBank;

class EnrolledAttachment extends Model
{
    public function files(){
        return $this->hasOne(ContentBank::class,'id','file_id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }
}
