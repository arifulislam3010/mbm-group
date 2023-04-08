<?php

namespace App\Models\Myaccount\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogPostLike extends Model
{
    protected $fillable = ['blog_post_id','user_id'];

    public function CreatedBy()
    {
        return $this->belongsTo('App\User','user_id')->select('id','name','phone','email');
    }
    
}
