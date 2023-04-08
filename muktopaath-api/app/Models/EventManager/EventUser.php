<?php

namespace App\Models\EventManager;

use Illuminate\Database\Eloquent\Model;
use App\Models\Myaccount\User;
class EventUser extends Model
{
    protected $guarded = ['created_at','updated_at'];

    public function event(){
        return $this->belongsTo('Event');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}