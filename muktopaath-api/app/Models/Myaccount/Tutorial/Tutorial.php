<?php

namespace App\Models\Myaccount\Tutorial;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $table = 'tutorial_upload';
    protected $fillable = [
        'title',
        'description',
        'video',
        'status',
        'created_by',
        'updated_by',
    ];

    public function CreatedBy()
    {
        return $this->belongsTo('App\Models\Myaccount\User','created_by')->select('name','bn_name','username')->with('photo');
    }
    
    public function UpdatedBy()
    {
        return $this->belongsTo('App\Models\Myaccount\User','updated_by')->select('name','bn_name','username');
    }

    public function video_file(){
        return $this->belongsTo('App\Models\Filemanager\ContentBank', 'file_id', 'id')->select(['id','title','type','file_encode_path','file_main_path','is_url']);
    }

    public function thumbnail(){
        return $this->belongsTo('App\Models\Filemanager\ContentBank', 'thumbnail_id', 'id')->select(['id','title','type','file_encode_path','file_main_path','is_url']);
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\Myaccount\User','owner_id');
    }
}
