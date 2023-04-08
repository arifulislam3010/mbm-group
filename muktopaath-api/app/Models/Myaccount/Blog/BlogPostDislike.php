<?php

namespace App\Models\Myaccount\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogPostDislike extends Model
{
    protected $fillable = ['blog_post_id','user_id'];
}
