<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Model;

class AdvertisementCategory extends Model
{
	public $timestamps = false;

	protected $connection = 'my-account';

    protected $guarded = ['created_at','updated_at'];
}
