<?php

namespace App\Models\Myaccount\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogPostComment extends Model
{
    protected $fillable = ['blog_post_id','user_id','body'];

    public function CreatedBy()
    {
        return $this->belongsTo('App\Models\Myaccount\User','user_id')->select('id','name','phone','email');
    }
    
}
