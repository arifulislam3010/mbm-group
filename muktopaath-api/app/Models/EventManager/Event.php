<?php

namespace App\Models\EventManager;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventManager\EventUser;
use App\Models\Myaccount\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Event extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['created_at','updated_at'];
    
    public const TYPE_LIVE = 'live';
    public const TYPE_TRAINING = 'training';
    public const TYPE_COURSE = 'course';

    protected $typeLabel = [
        self::TYPE_LIVE => 'live class',
        self::TYPE_TRAINING => 'training',
        self::TYPE_COURSE => 'course'
    ];

    protected $dates = [
        'end_time',
        'start_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const RECURRENCE_RADIO = [
        'none'    => 'None',
        'daily'   => 'Daily',
        'weekly'  => 'Weekly',
        'monthly' => 'Monthly',
    ];

    public function getTypeLabelAttribute()
    {
        return $this->typeLabel[$this->status];
    }

    public function event_users(){
        return $this->hasMany(EventUser::class, 'event_id');
    }
    

    public function attendances(){
        return $this->hasMany('Attendance');
    }

    public function materials(){
        return $this->hasMany('Material');
    }
    
    public function eventDiscussion(){
        return $this->hasMany('EventDiscussion');
    }

}