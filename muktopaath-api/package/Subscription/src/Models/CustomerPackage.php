<?php

namespace Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Myaccount\User;

class CustomerPackage extends Model
{
    protected $guarded = ['created_at','updated_at'];

}