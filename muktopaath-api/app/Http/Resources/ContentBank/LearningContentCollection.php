<?php

namespace App\Http\Resources\ContentBank;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LearningContentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'content_type' => $this->content_type
        ];
    }
}
