<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;
use App\Models\Myaccount\User;

class Payment extends Model
{
    protected $connection = 'course';
    
    protected $guarded = ['created_at','updated_at'];
    
}
