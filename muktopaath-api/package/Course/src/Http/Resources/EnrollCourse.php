<?php

namespace Muktopaath\Course\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Muktopaath\Course\Http\Resources\BatchLogin as ResourceBatchLogin;
use Muktopaath\Course\Http\Resources\EnrolledBatchInfo as ResourceEnrolledBatchInfo;
use Muktopaath\Course\Http\Resources\CourseRating as ResourceCourseRating;
use Muktopaath\Course\Http\Resources\Exam as ResourceExam;
use App\Http\Resources\Quiz as ResourceQuiz;
use App\Http\Resources\Assignment as ResourseAssignment;

class EnrollCourse extends JsonResource
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
            'Course'                 => new ResourceBatchLogin($this->courseBatch),
            'course_completeness'    => $this->course_completeness,
            'journey_status'         => json_decode($this->journey_status),
            'rating'                 => new ResourceCourseRating($this->courseRating),
            'extra_ass_attempt'      => json_decode($this->extra_assessment_attempt),
            'attachments'            => $this->Attachment,
            'status'                 => $this->status,
            // 'order'                => $this->orderId,
            'activity_update_time'   => $this->activity_update_time,
        ];
    }
}
