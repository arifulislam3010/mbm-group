<?php

namespace App\Models\AdminSettings;

use Illuminate\Database\Eloquent\Model;

class ParticipantType extends Model
{
    protected $connection = 'admin-settings';
    protected $table = 'training_participant_types';
    protected $guarded = ['created_at','updated_at'];
    
}
