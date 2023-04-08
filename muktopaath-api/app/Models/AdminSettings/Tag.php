<?php

namespace App\Models\AdminSettings;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $connection = 'admin-settings';
    protected $table = 'tags';
    protected $fillable = ['title','type'];
}
