<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BlogComment as BlogCommentResource;
use App\Http\Resources\User as UserResources;
use App\Http\Resources\UserbasicInfo as UserbasicInfoResources;
class PostBlogComment extends JsonResource
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
            'Replies'          => BlogComment::collection($this->Replies),
            'blog_post_id'     =>$this->blog_post_id,
            'user_name'        =>$this->CreatedBy->name,
            'username'         =>$this->CreatedBy->username,
            'user_photo'       =>$this->CreatedBy->UserInfo->photo_name,
            'user_id'          =>$this->user_id,
        ];
    }
}
