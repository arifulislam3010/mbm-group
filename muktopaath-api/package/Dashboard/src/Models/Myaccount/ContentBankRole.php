<?php

namespace Muktopaath\Dashboard\Models\Myaccount;

use Illuminate\Database\Eloquent\Model;

class ContentBankRole extends Model
{
	protected $connection = 'my-account';

    protected $guarded = ['created_at','updated_at'];
}
