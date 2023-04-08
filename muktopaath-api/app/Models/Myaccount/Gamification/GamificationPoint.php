<?php

namespace App\Models\Myaccount\Gamification;

use Illuminate\Database\Eloquent\Model;

class GamificationPoint extends Model
{
    protected $connection = 'my-account';
    protected $table = 'gamification_points';
    // protected $guarded = [];

    // public function type()
    // {
    //     if ($this->type == 1)
    //     {
    //         return 'Admin';

    //     }elseif ($this->type == 2)
    //     {
    //         return 'Partner';

    //     }else
    //         return "not Found";
    // }
}
