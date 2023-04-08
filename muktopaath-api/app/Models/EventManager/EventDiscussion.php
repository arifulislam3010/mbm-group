<?php

namespace App\Models\EventManager;

use Illuminate\Database\Eloquent\Model;

class EventDiscussion extends Model
{
    protected $guarded = ['created_at','updated_at'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}