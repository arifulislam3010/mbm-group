<?php

namespace App\Models\Myaccount;

use Illuminate\Database\Eloquent\Model;
use App\Models\Myaccount\User;

class RatingFeedback extends Model
{
	protected $table = 'rating_feedbacks';
	protected $connection = 'my-account';

    protected $guarded = ['created_at','updated_at'];

    public function creator(){
        return $this->hasOne(User::class,'id','user_id')->select(['id','name','username','photo_id'])->with('photo');
    }
}
