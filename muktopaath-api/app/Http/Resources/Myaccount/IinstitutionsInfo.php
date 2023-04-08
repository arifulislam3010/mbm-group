<?php

namespace App\Http\Resources\Myaccount;

use Illuminate\Http\Resources\Json\JsonResource;

class IinstitutionsInfo extends JsonResource
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
