<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Model;

class AdTargetPeopleProfession extends Model
{
	protected $connection = 'my-account';

	public $timestamps = false;

	protected $table = 'ad_target_people_profession';
    protected $guarded = ['created_at','updated_at'];
}
