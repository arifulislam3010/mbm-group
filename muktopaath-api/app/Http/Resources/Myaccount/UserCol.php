<?php

namespace App\Http\Resources\Myaccount;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCol extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
