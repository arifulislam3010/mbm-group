<?php

namespace App\Http\Resources\AdminSettings;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Muktopaath\Course\Models\Course\CourseBatch;
use DB;

class CategoryInfo extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'bn_title' => $this->bn_title,
            'total_course' => $this->total_course_count,
            'total_enrollment'  =>  CourseBatch::Select(DB::raw('SUM(total_enrollment) as total_enrollment'))
            ->join('courses','courses.id','course_batches.course_id')
            ->where('courses.cat_id',$this->id)
            ->value('total_enrollment')
        ];
    }

}
