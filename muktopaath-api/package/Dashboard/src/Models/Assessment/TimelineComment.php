<?php

namespace Muktopaath\Dashboard\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Myaccount\User;

class TimelineComment extends Model
{
    protected $guarded = ['created_at','updated_at'];

    public function user(){
        return $this->belongsTo(User::class,'created_by')->select('id','name','photo');
    }

}
