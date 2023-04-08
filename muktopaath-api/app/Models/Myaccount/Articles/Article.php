<?php

namespace App\Models\Myaccount\Articles;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    public function articleId()
    {
    	return $this->belongsTo('App\Models\Myaccount\Articles\ArticleCategory', 'article_category');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\Myaccount\User', 'created_by', 'id')->select(['id','name','photo_id'])->with('photo');
    }

    public function thumbnail(){
        return $this->belongsTo('App\Models\Filemanager\ContentBank', 'file_id', 'id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }
}
