<?php

namespace Muktopaath\Dashboard\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Muktopaath\Dashboard\Models\Assessment\PartyConferenceIntegration;

class ConferenceToolsDetails extends Model
{
    protected $table = 'conference_tools_details';

    protected $guarded = ['created_at','updated_at'];


    public function credentials(){
        return $this->belongsTo(PartyConferenceIntegration::class,'created_by','user_id')->select('user_id','credential_info');
    }
}
