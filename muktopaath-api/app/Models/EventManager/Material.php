<?php

namespace App\Models\EventManager;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $guarded = ['created_at','updated_at'];

    public function event(){
        return $this->belongsTo('Event');
    }
}