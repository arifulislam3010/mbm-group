<?php

namespace App\Models\ContentBank;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'content-bank';
	
    protected $guarded = ['created_at', 'updated_at'];
}
