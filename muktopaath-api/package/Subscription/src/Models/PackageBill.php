<?php

namespace Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Subscription\Models\Package;
use App\Models\Myaccount\User;

class PackageBill extends Model
{
    
    protected $guarded = ['created_at','updated_at'];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id')->select(['id','title','price']);
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\Myaccount\InstitutionInfo', 'owner_id', 'id')->select(['id','institution_name','email','logo_id'])->with('logo');
    }

}