<?php

namespace App\Http\Resources\ContentBank;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Question\Question;
use App\Models\Question\PartnerCategory;


class LearningContentShow extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function folderData($folder_id){
        $categories = PartnerCategory::whereIn('id',$folder_id)->get();

        foreach ($categories as $key => $category) {
           $parent_id = [];
           $parent_id = PartnerCategory::where('parent_id',$category->id)->pluck('id');
           $parent_id->push($category->id);
           $category->question = Question::whereIn('partner_category',$parent_id)->get();
        }

        return $categories;
    }
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'content_type'   => $this->content_type,
            'content_url'    => $this->content_url,
            'content_or_url' => $this->content_or_url,
            'more_data_info' => json_decode($this->more_data_info),
            'pick_questions' => is_array(json_decode($this->quiz_data))? Question::whereIn('id',json_decode($this->quiz_data))->get():[],
            'quiz_marks'     => $this->quiz_marks ? json_decode($this->quiz_marks):[],
            'folder_marks'   => $this->folder_marks ? json_decode($this->folder_marks):[],
            'folder_id'      => $this->folder_id ? json_decode($this->folder_id):[],
            'folder_data'    => $this->folder_id!=null ? $this->folderData(json_decode($this->folder_id)):[],
            'duration'       => $this->duration,
            'description'    => $this->description,
            'cat_id'         => $this->cat_id ? $this->cat_id.'@'.$this->cat_title_en.'@'.$this->cat_title_bn : '',

        ];
    }
}

