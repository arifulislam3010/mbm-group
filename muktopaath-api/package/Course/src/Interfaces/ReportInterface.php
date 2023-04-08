<?php

namespace Muktopaath\Course\Interfaces;

interface ReportInterface 
{
    public function total_learners();
    public function learners_report();
    public function courses_report();
    public function marksheet($batch_id,$enrollment_id);
    public function total_courses();
    public function learner_course_report($user_id);
    public function learner_stats();
    public function learner_stat($user_id);
    public function course_users($batch_id);
    public function course_users_report($batch_id);
    public function course_stats();
    public function course_stat($batch_id);
    public function learner_courses($user_id);
}