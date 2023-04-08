<?php

namespace Muktopaath\Course\Http\Resources\Discuss;

use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Http\Resources\Discuss\Replies as RepliesResources;
class Discuss extends JsonResource
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
            'id'               =>$this->id,
            'comments'         =>$this->comments,
            'Replies'          => RepliesResources::collection($this->Replies),
            //'Replies'          =>$this->Replies,
            'user_name'        =>$this->getEnroll->orderInfo->getUser->name,
            'username'         =>$this->getEnroll->orderInfo->getUser->username,
            'photo_name'       =>$this->getEnroll->orderInfo->getUser->getUserInfo->photo_name,
            'photo'            =>$this->getEnroll->orderInfo->getUser->photo,
            'profile'          =>null,
             'created_at'      =>$this->created_at->diffForHumans(),
            'updated_at'       =>$this->updated_at->diffForHumans(),
        ];
    }
}
