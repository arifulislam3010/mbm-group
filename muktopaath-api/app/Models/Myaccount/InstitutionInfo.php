<?php

namespace App\Models\Myaccount;

use Illuminate\Database\Eloquent\Model;
use App\Models\Filemanager\ContentBank;
use Muktopaath\Course\Models\Course\Course;
use App\Models\AdminSettings\Slider;
use App\Models\Myaccount\InstitutionType;



class InstitutionInfo extends Model
{
    
    protected $connection = 'my-account';
    
    protected $guarded = ['created_at','updated_at'];


    public function logo(){
        return $this->hasOne(ContentBank::class,'id','logo_id')->select(['id','type','is_url','file_encode_path','file_main_path']);
    }

    public function institution_type(){
        return $this->hasOne(InstitutionType::class,'id','institution_type_id')->select(['id','instype_ban','instype_eng']);
    }

    public function cover(){
        return $this->hasOne(ContentBank::class,'id','cover_id')->select(['id','type','is_url','file_encode_path','file_main_path']);
    }

    public function total_course(){
        return $this->hasMany(Course::class,'owner_id');
    }
    public function sliders(){
        return $this->hasMany(Slider::class,'owner_id');
    }

}
