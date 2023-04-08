<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class ClassMeta extends Model
{
    protected $connection = 'course';

    protected $guarded = ['created_at','updated_at'];
}
