<?php

namespace App\Models\Myaccount\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    protected $connection = 'my-account';
    protected $fillable = ['title','body' ];
}
