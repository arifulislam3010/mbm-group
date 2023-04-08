<?php

namespace Muktopaath\Dashboard\Models\Myaccount;

use Illuminate\Database\Eloquent\Model;
use App\Models\Myaccount\InstitutionInfo;

class SystemRole extends Model
{
    protected $connection = 'my-account';
    
    protected $guarded = ['created_at','updated_at'];
    
    
    public function owner()
    {
        return $this->belongsTo(InstitutionInfo::class,'owner_id')->select('institution_name','id');
    }


}
