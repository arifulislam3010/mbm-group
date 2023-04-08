<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogComment extends JsonResource
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
            'body'             =>$this->body,
            'blog_post_id'     =>$this->blog_post_id,
            'user_name'        =>$this->CreatedBy->name,
            'username'         =>$this->CreatedBy->username,
            'user_photo'       =>$this->CreatedBy->UserInfo->photo_name,
            'user_id'          =>$this->user_id,
        ];
    }
}
