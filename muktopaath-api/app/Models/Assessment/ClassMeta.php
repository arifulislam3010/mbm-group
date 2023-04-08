<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;

class ClassMeta extends Model
{
    protected $connection = 'assessment';

    protected $guarded = ['created_at','updated_at'];
}
