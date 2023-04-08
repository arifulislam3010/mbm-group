<?php

namespace Muktopaath\Dashboard\Models\Myaccount;

use Illuminate\Database\Eloquent\Model;
use App\Models\Filemanager\ContentBank;



class InstitutionInfo extends Model
{
    
    protected $connection = 'my-account';
    
    protected $guarded = ['created_at','updated_at'];


    public function logo(){
        return $this->hasOne(ContentBank::class,'id','logo_id')->select(['id','type','is_url','file_encode_path','file_main_path']);
    }

}
