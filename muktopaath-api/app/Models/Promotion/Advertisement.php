<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Model;
use App\Models\Promotion\AdvertisementCategory;

class Advertisement extends Model
{
	protected $connection = 'my-account';

    protected $guarded = ['created_at','updated_at'];



    // public function categories(){
    //     return $this->hasMany('App\Models\Promotion\AdvertisementCategory','advertisement_category_id')->pluck('advertisement_category_id');
    // }
    
    public function categories(){
        return $this->belongsToMany('App\Models\Promotion\AdvertisementCategory','advertisement_categories');
    }


    public function profession(){
        return $this->hasMany('App\Models\Promotion\AdTargetPeopleProfession');
    }

    public function target_category(){
        return $this->belongsToMany( AdvertisementCategory::class);
    }

    public function professions(){
        return $this->belongsToMany('App\Models\Promotion\AdTargetPeopleProfession','ad_target_people_profession');
    }
}
