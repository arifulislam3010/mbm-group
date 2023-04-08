<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;
use App\Models\Question\PartnerCategory;

class Question extends Model
{
    protected $connection = 'content-bank';


    public function folder(){
        return $this->belongsTo(PartnerCategory::class,'partner_category');
    }
}
