<?php

namespace Muktopaath\Course\Models\Course;
use App\Models\Myaccount\User;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'course_enrollment_rating';

    public function User()
    {
        $db = config()->get('database.connections.my-account.database');

        return $this->belongsTo('Muktopaath\Course\Models\Course\CourseEnrollment','enrollement_id')
        ->join('orders','course_enrollments.order_id', '=', 'orders.id')
        ->join($db.'.users','orders.user_id', '=','users.id')
        ->join($db.'.user_infos','users.id', '=','user_infos.user_id')
        ->select('users.name','user_infos.photo_name','user_infos.designation');
    }

    public function creator(){
        return $this->hasOne(User::class,'id','user_id')->select(['id','name','username','photo_id'])->with('photo','user_other');

    }


}
