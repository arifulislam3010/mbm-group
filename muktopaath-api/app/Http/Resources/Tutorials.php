<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Myaccount\Tutorial\Tutorial;

class Tutorials extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
    
        return[
            'id'                      =>$this->id,
            'title'                   =>$this->title,
            'description'             =>$this->description,
            'video'                   =>$this->video,
            'video_file'              =>$this->video_file,
            'thumbnail'               =>$this->thumbnail,
            'duration'                =>$this->duration,
            'type'                    =>$this->type,
            'category_id'             =>$this->category_id,
            'status'                  =>$this->status,
            'created_at'              =>$this->created_at->format('d M Y'),
            'updated_by'              =>$this->updated_by,
            'creator'                 =>$this->createdBy
                
        ];
    }
}
