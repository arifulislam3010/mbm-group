<?php

namespace Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Myaccount\User;

class Product extends Model
{

    protected $guarded = ['created_at','updated_at'];

    public function thumbnail()
    {
        return $this->belongsTo('App\Models\Filemanager\ContentBank', 'file_id', 'id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }

}