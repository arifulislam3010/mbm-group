<?php

namespace App\Http\Resources\Myaccount;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Myaccount\Gamification\Gamification;
use Auth;
class GamificationPoint extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        $pointTotal = Gamification::where('user_id',Auth::user()->id)->where('gamification_point_id',$this->id)->sum('points');
        $pointCount = Gamification::where('user_id',Auth::user()->id)->where('gamification_point_id',$this->id)->count();
        return [
            'id'                 => $this->id,
            'activity'           => $this->activity,
            'points'             => $this->points,
            'type'               => $this->type,
            'total_point'        => $pointTotal,
            'count_point'        => $pointCount,
        ];
    }
}
