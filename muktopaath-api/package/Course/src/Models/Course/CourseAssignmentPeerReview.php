<?php

namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseAssignmentPeerReview extends Model
{
    protected $connection = 'course';
    protected $table = "course_assignment_peer_review";
}