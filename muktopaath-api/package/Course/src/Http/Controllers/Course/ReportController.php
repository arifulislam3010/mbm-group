<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Interfaces\ReportInterface;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Auth;
 
class ReportController extends Controller
{

    private  $reportRepository;

    public function __construct(ReportInterface $reportRepository) 
    {
        $this->reportRepository = $reportRepository;
    }

    public function learners(){
        return  $this->reportRepository->total_learners();
    }

    public function learner_courses($user_id){
        return  $this->reportRepository->learner_courses($user_id);
    }

    public function learner_stats(){
        return  $this->reportRepository->learner_stats();
    }

    public function marksheet($batch_id,$enrollment_id){
        return  $this->reportRepository->marksheet($batch_id,$enrollment_id);
    }

    public function learner_stat($user_id){
        return  $this->reportRepository->learner_stat($user_id);
    }

    public function courses(){
        return  $this->reportRepository->total_courses();
    }

    public function course_users($batch_id){
        return  $this->reportRepository->course_users($batch_id);
    }

    public function course_users_report($batch_id){
        return  $this->reportRepository->course_users_report($batch_id);
    }

    public function course_stats(){
        return  $this->reportRepository->course_stats();
    }

    public function course_stat($batch_id){
        return  $this->reportRepository->course_stat($batch_id);
    }

    public function learners_report(){
        return  $this->reportRepository->learners_report();
    }
    public function courses_report(){
        return  $this->reportRepository->courses_report();
    }

    public function learner_course_report($user_id){
        return  $this->reportRepository->learner_course_report($user_id);
    }

}