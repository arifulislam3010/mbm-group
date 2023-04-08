<?php

namespace Muktopaath\Course\Http\Resources\Discuss;

use Illuminate\Http\Resources\Json\JsonResource;

class Replies extends JsonResource
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
            'id'                 =>$this->id,
            'comments'           =>$this->comments,
            'user_name'          =>$this->getUser->name,
            'username'           =>$this->getUser->username,
            'photo_name'         =>$this->getUser->getUserInfo->photo_name,
            'photo'              =>$this->getUser->photo,
            'created_at'         =>$this->created_at->diffForHumans(),
            'updated_at'         =>$this->updated_at->diffForHumans(),
        ];
    }
}
