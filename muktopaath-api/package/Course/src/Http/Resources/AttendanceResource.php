<?php

namespace Muktopaath\Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'start_time' =>  date('Y-m-d H:i:s',strtotime(Carbon::parse($this->start_time)->addHours(6))),
            'end_time' =>  date('Y-m-d H:i:s',strtotime(Carbon::parse($this->end_time)->addHours(6))),
        ];
    }
}
