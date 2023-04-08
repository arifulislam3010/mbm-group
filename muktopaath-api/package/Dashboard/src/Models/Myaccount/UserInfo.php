<?php

namespace Muktopaath\Dashboard\Models\Myaccount;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $connection = 'my-account';
    
    protected $casts = [
        'attachments' => 'array',
    ];
}
