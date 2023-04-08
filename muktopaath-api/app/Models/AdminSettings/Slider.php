<?php

namespace App\Models\AdminSettings;

use Illuminate\Database\Eloquent\Model;
use App\Models\Filemanager\ContentBank;


class Slider extends Model
{
    protected $connection = 'admin-settings';
    protected $guarded = ['created_at', 'updated_at'];



    public function photo(){
        return $this->hasOne(ContentBank::class,'id','content_id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }
}