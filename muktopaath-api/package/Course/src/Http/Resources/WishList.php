<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Batch as ResourceBatch;
use App\Http\Resources\BatchBasic as BatchBasicResource;
class WishList extends JsonResource
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
            'id'                     => $this->id,
            'Course'                 => new BatchBasic($this->courseBatch),
        ];
    }
}
