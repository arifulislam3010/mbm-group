<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;

class PartnerCategory extends Model
{
    protected $guarded = ['created_at','updated_at'];
    
    protected $connection = 'content-bank';
    //abcd

    public function question(){
    	return $this->hasMany('App\Models\Question\Question','partner_category');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Question\PartnerCategory', 'parent_id')->with('children');
    }
}
