<?php

namespace App\Models\Myaccount\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $connection = 'my-account';
    protected $fillable = ['title','body','thumbnail','published','approved'];

    public function Tags()
    {
        return $this->hasManyThrough('App\Models\AdminSettings\Tag','App\Models\Myaccount\Blog\BlogTag','blog_post_id','id','id','tag_id');
    }
    public function CreatedBy()
    {
        return $this->belongsTo('App\Models\Myaccount\User','created_by')->select('id','name','phone','email');
    }
    public function UpdatedBy()
    {
        return $this->belongsTo('App\Models\Myaccount\User','updated_by')->select('id','name','phone','email');
    }
    public function Comments()
    {
        return $this->hasMany('App\Models\Myaccount\Blog\BlogPostComment','blog_post_id')->where('blog_post_comment_parent_id','=',null);
    }

    public function CommentsC()
    {
        return $this->hasMany('App\Models\Blog\Myaccount\BlogPostComment','blog_post_id');
    }
    public function BlogPostLikeC()
    {
        return $this->hasMany('App\Models\Blog\Myaccount\BlogPostLike','blog_post_id')->where('likes',1);
    }
    public function BlogPostDislikesC()
    {
        return $this->hasMany('App\Models\Blog\Myaccount\BlogPostLike','blog_post_id')->where('dislikes',1);
    }
}
