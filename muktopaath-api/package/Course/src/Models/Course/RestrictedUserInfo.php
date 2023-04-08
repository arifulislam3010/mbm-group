<?php
namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class RestrictedUserInfo extends Model
{
    protected $table = 'restricted_users_info';
    protected $connection = 'course';
}
