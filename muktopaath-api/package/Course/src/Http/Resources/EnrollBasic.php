<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Http\Resources\Batch as ResourceBatch;
use Muktopaath\Course\Http\Resources\BatchBasic as BatchBasicResource;
use Muktopaath\Course\Http\Resources\CourseRating as ResourceCourseRating;
use Muktopaath\Course\Http\Resources\Exam as ResourceExam;
use App\Http\Resources\Quiz as ResourceQuiz;
use App\Http\Resources\Assignment as ResourseAssignment;
class EnrollBasic extends JsonResource
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
            'id'                     => $this->id,
            'uuid'                   => $this->uuid,
            'Course'                 => new BatchBasicResource($this->courseBatch),
            'course_completeness'    => $this->course_completeness,
            'rating'                 => new ResourceCourseRating($this->courseRating),
            'attachments'            => $this->Attachment,
            'status'                 => $this->status,
            'order'                  => $this->orderId,
            'activity_update_time'   => $this->activity_update_time,
        ];
    }
}
