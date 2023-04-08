<?php

namespace App\Http\Resources\ContentBank;

use Illuminate\Http\Resources\Json\JsonResource;
class Questions extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        if($this->feedback==null){
            $temp['correct'] = '';
            $temp['incorrect'] = '';
            $feedback = json_encode($temp);
        }else{
            $feedback = $this->feedback;
        }
       
        return [
            'id' => $this->id,
            'title_content_type' => $this->title_content_type,
            'title' => html_entity_decode(strip_tags($this->title)),
            'title_content_id' => $this->title_content_id,
            'title_content_url' => $this->title_content_url,
            'dif_level' => $this->dif_level,
            'description' => $this->description,
            'type' => $this->type,
            'details' => $this->details,
            'feedback' => $feedback,
            'options' => json_decode($this->options),
            'rubric_criteria' => json_decode($this->options),
            'submission_criteria' => json_decode($this->submission_criteria),
            'rubric_grading'    => $this->rubric_grading,
            'body' => json_decode($this->answer),
            'time' => $this->time,
            'date' => $this->date,
            'file_id' => $this->file_id,
            'status'     => $this->status,
            'file_id'     => $this->file_id,
            'mark' => $this->mark,
            'partner_category' => $this->partner_category,
            'category_id' => $this->category_id,
            'created_by'=>$this->created_by,
            'updated_by' => $this->updated_by,
            'owner_id'      =>$this->owner_id,

        ];
    }
}
