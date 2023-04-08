<?php

namespace App\Http\Resources\ContentBank;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Question\Question;

class Sharing extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = Question::where('id',$this->table_id)->first();

        return [

            'id' => $data->id,
            'title' => $data->title,
            'dif_level' => $data->dif_level,
            'type' => $data->type,
            'answer' => $data->answer,
            'options' => $data->options,
            'partner_category' => $data->partner_category,
            'owner_id' => $data->owner_id,
            'activity' => $this->activity,
        ];
    }
}
