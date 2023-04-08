<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;
use App\Models\Myaccount\User;

class Order extends Model
{
    protected $guarded = ['created_at','updated_at'];

    protected $connection = 'course';
    public function users()
    {
        return $this->hasOne(User::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function courseBatch(){
        return $this->hasMany('Muktopaath\Course\Models\Course\CourseEnrollment','order_id');
    }

    public function enrollment(){
        return $this->hasOne('Muktopaath\Course\Models\Course\CourseEnrollment','order_id');
    }
    
}
