<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserbasicInfo as UserbasicInfoResources;
use Auth;
use App\Models\Myaccount\Blog\BlogPostLike;
use App\Models\Myaccount\Blog\BlogPostComment;
use App\Http\Resources\PostBlogComment as PostBlogCommentResource;
class BlogPost extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $blog_like_count = BlogPostLike::where('blog_post_id',$this->id)->where('likes',1)->count();
        $blog_dislike_count = BlogPostLike::where('blog_post_id',$this->id)->where('dislikes',1)->count();
        
        return [
            'id'                        =>$this->id,
            'title'                     =>$this->title,
            'body'                      =>$this->body,
            'published'                 =>$this->published,
            //'tags'                      =>$this->Tags,
            'thumbnail'                 =>$this->thumbnail,
            'likeCount'                 =>$blog_like_count,
            'dislikeCount'              =>$blog_dislike_count,
            'CommentCount'              =>$this->CommentsCount,
            'likes'                     =>[],
            'Comments'                  =>$this->Comments,
            'category_id'               =>$this->category_id,
            'user_name'                 =>$this->CreatedBy->name,
            'username'                  =>$this->CreatedBy->username,
            //'user_photo'                =>$this->CreatedBy->UserInfo->photo_name,
            'user_id'                   =>$this->created_by,
            'user_name'                 =>$this->CreatedBy->name,
            'created_at'                =>$this->created_at->format('d M Y'),
            'updated_at'                =>$this->updated_at,
        ];
    }
}
