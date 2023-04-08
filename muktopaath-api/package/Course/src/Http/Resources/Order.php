<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EnrollCourse as EnrollCourseResources;
use App\Http\Resources\Batch as ResourceBatch;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Http\Resources\BatchBasic as BatchBasicResource;
use Muktopaath\Course\Models\Course\CourseBatch;
class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $course_id =  CourseEnrollment::where('order_id',$this->id)->pluck('course_batch_id')->toArray();
        $courses = CourseBatch::whereIn('id',$course_id)->get();
         return [
            'id'              => $this->id,
            'amount'          => $this->amount,
            'discount'        => $this->discount,
            'order_number'    => $this->order_number,
            'course'          => BatchBasicResource::collection($courses),
            'User'            =>$this->User,
            'payment_method'  =>$this->payment_method,
            'payment_status'  =>$this->payment_status,
            'card_or_bank_number'  =>$this->card_or_bank_number,
            'bank_info'           =>$this->bank_info,
            'transaction_number' =>$this->transaction_number,
            'created_at'      => $this->created_at->format('d M Y'),
            // 'courseBatch'     => EnrollCourseResources::collection($this->courseBatch),
            // 'RunnigCourse'    => EnrollCourseResources::collection($this->RunnigCourse),
            // 'CompletedCourse' => EnrollCourseResources::collection($this->CompletedCourse),
            // 'IncompletedCourse' => EnrollCourseResources::collection($this->IncompletedCourse),
        ];
    }
}
