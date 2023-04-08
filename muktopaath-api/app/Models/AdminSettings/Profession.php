<?php

namespace App\Models\AdminSettings;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $connection = 'admin-settings';
    protected $guarded = ['created_at','updated_at'];
    
}
