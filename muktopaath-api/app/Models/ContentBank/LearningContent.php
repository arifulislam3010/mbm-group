<?php

namespace App\Models\ContentBank;

use Illuminate\Database\Eloquent\Model;
use App\Models\Filemanager\ContentBank;

class LearningContent extends Model
{
	protected $connection = 'content-bank';
	
    protected $guarded = ['created_at', 'updated_at'];

    
    public function files(){
        return $this->hasOne(ContentBank::class,'id','file_id')->select(['id','type','is_url','file_encode_path','file_main_path']);
    }
}
