<?php

namespace Muktopaath\Dashboard\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Myaccount\User;

class Order extends Model
{
    protected $guarded = ['created_at','updated_at'];


    public function users()
    {
        return $this->hasOne(User::class);
    }
    
}
