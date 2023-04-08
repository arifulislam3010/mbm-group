<?php

namespace App\Http\Resources\Assessment;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Myaccount\User;

class Reviews extends JsonResource
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

            'review' => $this->review,
            'rating' => $this->rating,
            'user_id' => $this->user_id,
            'course_enrollment_id' => $this->course_enrollment_id,
            'username' => User::where('id',$this->user_id)->value('name'),
            'ago' => $this->created_at->diffForHumans()
        ];
    }
}
