<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class Completeness extends Model
{
    protected $connection = 'course';
    protected $table = 'completeness';
    protected $guarded = ['created_at','updated_at'];
}
