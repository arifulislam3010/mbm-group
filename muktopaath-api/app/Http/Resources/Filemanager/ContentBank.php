<?php

namespace App\Http\Resources\Filemanager;

use Illuminate\Http\Resources\Json\JsonResource;

class ContentBank extends JsonResource
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
