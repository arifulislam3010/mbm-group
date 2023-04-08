<?php

namespace Muktopaath\Dashboard\Http\Controllers;

use Muktopaath\Dashboard\Models\Myaccount\User;
use Muktopaath\Dashboard\Models\Assessment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{



    public function totalLearner(Request $request)
    {
        $myaccount = config()->get('database.connections.my-account.database');
        $assessment = config()->get('database.connections.assessment.database');
        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $ownerID = config()->get('global.owner_id');

        $last_year_end_date = date("Y-m-d",strtotime("-1 year"));

        if($ownerID == 1 )
        {
//                $current_year_learners = DB::table($assessment.'.orders as lerner')
//                    ->whereBetween('created_at',[$start_date , $end_date])
//                    ->distinct()
//                    ->count('user_id');
//
//                $previous_year_learners = DB::table($assessment.'.orders as lerner')
//                    ->whereBetween('created_at',[$start_date , $last_year_end_date])
//                    ->distinct()
//                    ->count('user_id');

            $current_year_learners = DB::table($myaccount.'.users')
                ->select(DB::raw('(COUNT(CASE WHEN verify_status=1
            or verify_status_phone=1 THEN 1
            END)) as total_verified_user'))
                ->whereBetween('created_at',[$start_date , $end_date])
                ->whereNotNull('created_at')
                ->value('total_verified_user');

            $previous_year_learners = DB::table($myaccount.'.users')
                ->select(DB::raw('(COUNT(CASE WHEN verify_status=1
            or verify_status_phone=1 THEN 1
            END)) as total_verified_user'))
                ->whereBetween('created_at',[$start_date , $last_year_end_date])
                ->whereNotNull('created_at')
                ->value('total_verified_user');

        }
        else {
            $current_year_learners = DB::table($myaccount.'.users as u')
                ->join($assessment.'.orders as o','u.id','=','o.user_id')
                ->join($assessment.'.course_enrollments as ce','o.id','=','ce.order_id')
                ->join($assessment.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('o.created_at',[$start_date , $end_date])
                ->distinct()
                ->count('o.user_id');

            $previous_year_learners = DB::table($myaccount.'.users as u')
                ->join($assessment.'.orders as o','u.id','=','o.user_id')
                ->join($assessment.'.course_enrollments as ce','o.id','=','ce.order_id')
                ->join($assessment.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('o.created_at',[$start_date , $last_year_end_date])
                ->distinct()
                ->count('o.user_id');

        }

        $diff = $current_year_learners -  $previous_year_learners;

        if ($current_year_learners == 0){
            $compare = 0;
        }
        else{
            $compare = ($diff * 100) / $current_year_learners;
        }

        $arr = array(
            'current_year_total' =>  $current_year_learners,
            'previous_year_total' =>  $previous_year_learners,
            'compare_to_last_year' =>  round($compare,2));

        return Response()->json($arr);
    }

    public function learntrack(Request $request){

        $course = config()->get('database.connections.course.database');

        config()->set('database.default', 'course');

        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $not_super = config()->get('global.owner_id')==1?0:1;

        //return $not_super;

        $pass_rate = DB::table('syllabus_statuses')
                        ->select(DB::raw('SUM(CASE WHEN syllabus_statuses.mark>syllabus_statuses.pass_mark then 1 END) as passed'),DB::raw('COUNT(syllabus_statuses.id) as total'))
                        ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
                        ->when($not_super, function ($q) {
                            return $q->where('cb.owner_id',config()->get('global.owner_id'));
                        })
                        ->whereNotNull('syllabus_statuses.mark')
                        ->whereBetween('syllabus_statuses.created_at',[$start_date , $end_date])
                        ->first();


        $completion = DB::table('course_enrollments')
                        ->select(DB::raw('count(course_enrollments.id) as total'),DB::raw('SUM(CASE WHEN course_completeness=100.00 then 1 END) as completed'))
                        ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                        ->when($not_super, function ($q) {
                            return $q->where('cb.owner_id',config()->get('global.owner_id'));
                        })
                        ->whereBetween('course_enrollments.created_at',[$start_date , $end_date])
                        ->first();

        return response()->json([
            'pass_rate' => $pass_rate,
            'completion' => $completion
        ]);

    }

    public function totalVerifiedUsers()
    {
        $myaccount = config()->get('database.connections.my-account.database');
        $assessment = config()->get('database.connections.assessment.database');
        $users = DB::table($myaccount.'.users')
            ->select(DB::raw('(COUNT(CASE WHEN verify_status=1
            or verify_status_phone=1 THEN 1
            END)) as total_verified_user'))
            ->whereNotNull('created_at')
            ->get();
        if(config()->get('global.owner_id')){
            return $users;
        }
        else{
//            $lerners = ('SELECT count(DISTINCT orders.user_id) FROM orders
//JOIN course_enrollments ce ON orders.id=ce.order_id
//JOIN course_batches cb ON ce.course_batch_id = cb.id
//WHERE cb.id = 75');

            $lerners = DB::table($assessment.'.orders as o')
                ->join(DB::raw($assessment.'.course_enrollments ce'),'o.id','=','ce.order_id')
                ->join(DB::raw($assessment.'.course_batches cb'),'ce.course_batch_id','=','cb.id')
                ->distinct()
                ->count('o.user_id');
            return $lerners;
        }
    }

    public function totalCourse(Request $request)
    {
        $myaccount = config()->get('database.connections.my-account.database');
        $course= config()->get('database.connections.course.database');
        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $ownerID = config()->get('global.owner_id');

        $last_year_end_date = date("Y-m-d",strtotime("-1 year"));

        if($ownerID == 1 ){
            $current_year_courses = DB::table($course.'.course_batches')
                ->where('published_status','=',1)
                ->whereBetween('created_at',[$start_date , $end_date])
                ->count();

            $previous_year_courses = DB::table($course.'.course_batches')
                ->where('published_status','=',1)
                ->whereBetween('created_at',[$start_date , $last_year_end_date])
                ->count();
        }
        else{
            $current_year_courses = DB::table($course.'.course_batches as cb')
                ->where('cb.owner_id','=', $ownerID)
                ->where('cb.published_status','=',1)
                ->whereBetween('cb.created_at',[$start_date , $end_date])
                ->count();

            $previous_year_courses = DB::table($course.'.course_batches as cb')
                ->where('cb.owner_id','=', $ownerID)
                ->where('cb.published_status','=',1)
                ->whereBetween('cb.created_at',[$start_date , $last_year_end_date])
                ->count();
        }

        $diff = $current_year_courses -  $previous_year_courses;

        if ($current_year_courses == 0){
            $compare = 0;
        }
        else{
            $compare = ($diff * 100) / $current_year_courses;
        }

        $arr = array(
            'current_year_total' =>  $current_year_courses,
            'previous_year_total' =>  $previous_year_courses,
            'compare_to_last_year' =>  round($compare,2));

        return Response()->json($arr);
    }

    public function totalExam()
    {
        $arr = array(
            'current_year_total' =>  0,
            'previous_year_total'=> 0,
            'compare_to_last_year' =>  round(0,2));

        return Response()->json($arr);
    }

    public function totalCertificate()
    {
        $course = config()->get('database.connections.course.database');
        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $ownerID = config()->get('global.owner_id');

        $last_year_end_date = date("Y-m-d",strtotime("-1 year"));

        if($ownerID == 1 )
        {
            $current_year_certificates = DB::table($course.'.certificate_submit  as cs')
                ->where('status','=',1)
                ->whereNotNull('file_name')
                ->whereBetween('created_at',[$start_date , $end_date])
                ->count();

            $previous_year_certificates= DB::table($course.'.certificate_submit  as cs')
                ->where('status','=',1)
                ->whereNotNull('file_name')
                ->whereBetween('created_at',[$start_date , $last_year_end_date])
                ->count();

        }
        else {
            $current_year_certificates = DB::table($course.'.certificate_submit as cs')
                ->join($course.'.course_enrollments as ce','cs.course_enrollment_id','=','ce.id')
                ->join($course.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('cs.created_at',[$start_date , $end_date])
                ->where('cs.status','=',1)
                ->whereNotNull('cs.file_name')
                ->count();

            $previous_year_certificates = DB::table($course.'.certificate_submit as cs')
                ->join($course.'.course_enrollments as ce','cs.course_enrollment_id','=','ce.id')
                ->join($course.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('cs.created_at',[$start_date , $last_year_end_date])
                ->where('cs.status','=',1)
                ->whereNotNull('cs.file_name')
                ->count();

        }

        $diff = $current_year_certificates -  $previous_year_certificates;

        if ($current_year_certificates == 0){
            $compare = 0;
        }
        else{
            $compare = ($diff * 100) / $current_year_certificates;
        }


        $arr = array(
            'current_year_total' =>  $current_year_certificates,
            'previous_year_total' =>  $previous_year_certificates,
            'compare_to_last_year' =>  round($compare,2));

        return Response()->json($arr);
    }

    public function upcomingCourse()
    {
        $myaccount = config()->get('database.connections.my-account.database');
        $assessment = config()->get('database.connections.assessment.database');
        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $ownerID = config()->get('global.owner_id');

        $last_year_end_date = date("Y-m-d",strtotime("-1 year"));

        if($ownerID == 1 )
        {
                $current_year_learners = DB::table($assessment.'.orders as lerner')
                    ->whereBetween('created_at',[$start_date , $end_date])
                    ->distinct()
                    ->count('user_id');

                $previous_year_learners = DB::table($assessment.'.orders as lerner')
                    ->whereBetween('created_at',[$start_date , $last_year_end_date])
                    ->distinct()
                    ->count('user_id');

        }
        else {
            $current_year_learners = DB::table($myaccount.'.users as u')
                ->join($assessment.'.orders as o','u.id','=','o.user_id')
                ->join($assessment.'.course_enrollments as ce','o.id','=','ce.order_id')
                ->join($assessment.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('o.created_at',[$start_date , $end_date])
                ->distinct()
                ->count('o.user_id');

            $previous_year_learners = DB::table($myaccount.'.users as u')
                ->join($assessment.'.orders as o','u.id','=','o.user_id')
                ->join($assessment.'.course_enrollments as ce','o.id','=','ce.order_id')
                ->join($assessment.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('o.created_at',[$start_date , $last_year_end_date])
                ->distinct()
                ->count('o.user_id');

        }

        $diff = $current_year_learners -  $previous_year_learners;

        if ($current_year_learners == 0){
            $compare = 0;
        }
        else{
            $compare = ($diff * 100) / $current_year_learners;
        }

        $arr = array(
            'current_year_total' =>  $current_year_learners,
            'previous_year_total' =>  $previous_year_learners,
            'compare_to_last_year' =>  round($compare,2));

        return Response()->json($arr);
    }

    public function pendingClassWork()
    {
        $classroom = config()->get('database.connections.classroom.database');
        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $ownerID = config()->get('global.owner_id');

        $last_year_end_date = date("Y-m-d",strtotime("-1 year"));

        if($ownerID == 1 )
        {
            $current_year_classwork = DB::table($classroom.'.syllabus_statuses as ss')
                ->join($classroom.'.course_batches as cb','ss.course_batch_id','=','cb.id')
                ->where('ss.marking','=',0)
                ->whereBetween('ss.created_at',[$start_date , $end_date])
                ->count();

            $previous_year_classwork = DB::table($classroom.'.syllabus_statuses as ss')
                ->join($classroom.'.course_batches as cb','ss.course_batch_id','=','cb.id')
                ->where('ss.marking','=',0)
                ->whereBetween('ss.created_at',[$start_date , $last_year_end_date])
                ->count();

        }
        else {
            $current_year_classwork = DB::table($classroom.'.syllabus_statuses as ss')
                ->join($classroom.'.course_batches as cb','ss.course_batch_id','=','cb.id')
                ->where('ss.marking','=',0)
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('ss.created_at',[$start_date , $end_date])
                ->count();

            $previous_year_classwork = DB::table($classroom.'.syllabus_statuses as ss')
                ->join($classroom.'.course_batches as cb','ss.course_batch_id','=','cb.id')
                ->where('ss.marking','=',0)
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('ss.created_at',[$start_date , $last_year_end_date])
                ->count();

        }

        $diff = $current_year_classwork-  $previous_year_classwork;

        if ($current_year_classwork == 0){
            $compare = 0;
        }
        else{
            $compare = ($diff * 100) / $current_year_classwork;
        }


        $arr = array(
            'current_year_total' =>  $current_year_classwork,
            'previous_year_total' =>  $previous_year_classwork,
            'compare_to_last_year' =>  round($compare,2));

        return Response()->json($arr);
    }

    public function upcoming()
    {
        $course = config()->get('database.connections.course.database');
        $assessment = config()->get('database.connections.assessment.database');
        $ownerID = config()->get('global.owner_id');

        if($ownerID == 1 ){
            $res = DB::table($course.'.syllabuses')
                ->select('content_type','start_date')
                ->whereNotNull('content_type')
                ->whereRaw('start_date > NOW()')
                ->union(DB::table($assessment.'.syllabuses')
                ->select('content_type','start_date')
                ->whereNotNull('content_type')
                ->whereRaw('start_date > NOW()'))
                ->get();
        }
        else
        {
            $res = DB::table($course.'.syllabuses as sy')
                ->select('sy.content_type','sy.start_date')
                ->join($course.'.course_batches as cb','sy.course_batch_id','=','cb.id')
                ->whereNotNull('sy.content_type')
                ->where('cb.owner_id','=', $ownerID)
                ->whereRaw('sy.start_date > NOW()')
                ->union(DB::table($assessment.'.syllabuses as sy')
                    ->select('sy.content_type','sy.start_date')
                    ->join($assessment.'.course_batches as cb','sy.course_batch_id','=','cb.id')
                    ->whereNotNull('sy.content_type')
                    ->where('cb.owner_id','=', $ownerID)
                    ->whereRaw('sy.start_date > NOW()'))
                ->get();
        }

        return $res;
    }


    public  function pendingTask(Request $request){

        $course = config()->get('database.connections.course.database');
        $assessment = config()->get('database.connections.assessment.database');
        $myaccount = config()->get('database.connections.my-account.database');
        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $ownerID = config()->get('global.owner_id');

        if($ownerID == 1 ){
            $res = DB::table(DB::raw('(SELECT ce.course_batch_id,ss.syllabus_id,s.parent_id,ss.status as task_type, ss.created_at as submission_date, u.name as name, u.username as username
            FROM '.$course.'.syllabus_statuses as ss
            JOIN '.$course.'.syllabuses as s on s.id = ss.syllabus_id
            JOIN '.$course.'.course_enrollments as ce ON ss.course_enrollment_id=ce.id
            JOIN '.$course.'.orders as o ON ce.order_id=o.id
            JOIN '.$myaccount.'.users as u ON o.user_id=u.id
            where ss.mark IS NULL
            ORDER BY ss.created_at DESC) A'))
            ->whereBetween('ss.created_at',[$start_date , $end_date])
            ->take(10)
            ->get();
        }else {
            $res = DB::table(DB::raw('(SELECT ce.course_batch_id,ss.syllabus_id,s.parent_id,ss.status as task_type, ss.created_at as submission_date, u.name as name, u.username as username
            FROM '.$course.'.syllabus_statuses as ss
            JOIN '.$course.'.syllabuses as s on s.id = ss.syllabus_id
            JOIN '.$course.'.course_enrollments as ce ON ss.course_enrollment_id=ce.id JOIN  '.$course.'.course_batches as cb ON ce.course_batch_id=cb.id
            JOIN '.$course.'.orders as o ON ce.order_id=o.id
            JOIN '.$myaccount.'.users as u ON o.user_id=u.id
            where ss.mark IS NULL AND cb.owner_id='.$ownerID.'
            ORDER BY ss.created_at DESC) A'))
            ->whereBetween('ss.created_at',[$start_date , $end_date])
            ->take(10)
            ->get();
        }

        return $res;
    }

    public  function  learnerByGender(Request $request){
        $myaccount = config()->get('database.connections.my-account.database');
        $assessment = config()->get('database.connections.assessment.database');
        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $ownerID = config()->get('global.owner_id');

        if($ownerID == 1 ){
            $learners =  DB::table($myaccount.'.user_infos')
                                ->select(DB::raw('(CASE gender
                      WHEN \'1\' THEN \'Male\'
                      WHEN \'2\' THEN \'Female\'
                      WHEN \'3\' THEN \'Others\'
                      END) AS Gender'),DB::raw('count(gender) AS  total'))
                                ->where('gender','=',1)
                                ->orWhere('gender','=',2)
                                ->orWhere('gender','=',3)
                                ->whereBetween('created_at',[$start_date , $end_date])
                                ->groupBy('gender')
                                ->get();
        }
        else{
            $learners = DB::table($myaccount.'.user_infos as ui')
                ->select(DB::raw('(CASE ui.gender
              WHEN \'1\' THEN \'Male\'
              WHEN \'2\' THEN \'Female\'
              WHEN \'3\' THEN \'Others\'
              END) AS Gender'),DB::raw('count(ui.gender) AS  total'))
                        ->join($assessment.'.orders as o','ui.user_id','=','o.user_id')
                        ->join($assessment.'.course_enrollments as ce','o.id','=','ce.order_id')
                        ->join($assessment.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                        ->where('cb.owner_id','=', $ownerID)
                        ->where('ui.gender','=',1)
                        ->orWhere('ui.gender','=',2)
                        ->orWhere('ui.gender','=',3)
                        ->whereBetween('o.created_at',[$start_date , $end_date])
                        ->groupBy('ui.gender')
                        ->get();
                }

        return  $learners;

    }

    public  function  passRate()
    {
        $course = config()->get('database.connections.course.database');
        $ownerID = config()->get('global.owner_id');

        if($ownerID == 1 ) {
            $res = DB::table(DB::raw('(SELECT COUNT(CASE WHEN mark>=pass_mark THEN 1 END) as total_passed,
                    COUNT(*) as total_enroll FROM ' . $course . '.syllabus_statuses)'))
                ->select(DB::raw('(total_passed/NULLIF(total_enroll, 0)) * 100 as pass_rate'), 'total_enroll', 'total_passed')
                ->get();
        }
        else
        {
            $res = DB::table(DB::raw('(SELECT COUNT(CASE WHEN ss.mark >= ss.pass_mark THEN 1 END) as total_passed,
                    COUNT(*) as total_enroll
                    FROM
                    '.$course.'.syllabus_statuses as ss
                    JOIN course_batches as cb ON ss.course_batch_id=cb.id
                    where cb.owner_id='.$ownerID.') t'))
                ->select(DB::raw('(total_passed/NULLIF(total_enroll, 0)) * 100 as pass_rate'),'total_enroll','total_passed')
                ->get();
        }
        return $res;
    }

    public  function  courseCompletionRate()
    {
        $course = config()->get('database.connections.course.database');
        $ownerID = config()->get('global.owner_id');

        if($ownerID == 1 ) {
            $res = DB::table(DB::raw('(SELECT COUNT(CASE WHEN course_completeness=100 THEN 1 END) as total_complete,
                COUNT(*) as total_enroll 
                FROM '.$course.'.course_enrollments) t'))
                ->select('total_complete','total_enroll',DB::raw('(total_complete/NULLIF(total_enroll, 0)) * 100 as course_completion_rate'))
                ->get();
        }
        else
        {
            $res = DB::table(DB::raw('(SELECT COUNT(CASE WHEN course_completeness=100 THEN 1 END) as total_complete,
                COUNT(*) as total_enroll
                FROM '.$course.'.course_enrollments as ss
                JOIN '.$course.'.course_batches as cb ON ss.course_batch_id=cb.id
                where cb.owner_id='.$ownerID.') t'))
                ->select('total_complete','total_enroll',DB::raw('(total_complete/NULLIF(total_enroll, 0)) * 100 as course_completion_rate'))
                ->get();
        }
        return $res;
    }

    public  function enrollment(Request $request)
    {
        $myaccount = config()->get('database.connections.my-account.database');
        $assessment = config()->get('database.connections.assessment.database');
        $course = config()->get('database.connections.course.database');
        $date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $start_date = (isset($request['start_date']))?$request['start_date']:'2016-1-01';
        $end_date = (isset($request['end_date']))?$request['end_date']:date("Y-m-d");
        $ownerID = config()->get('global.owner_id');

        $last_year_end_date = date("Y-m-d",strtotime("-1 year"));

        if($ownerID == 1 )
        {

            $current_year_learners = DB::table($myaccount.'.users')
                ->select(DB::raw('(COUNT(CASE WHEN verify_status=1
                or verify_status_phone=1 THEN 1
                END)) as learners'),DB::raw('MONTH(created_at) as month'))
                ->whereNotNull('created_at')
                ->whereBetween('created_at',[$start_date , $end_date])
                ->whereRaw('YEAR(created_at) = YEAR(CURDATE())')
                ->groupByRaw('MONTH(created_at)')
                ->orderByRaw('MONTH(created_at) ASC')
                ->get();

            $current_year_certificates = DB::table($course.'.certificate_submit  as cs')
                ->select(DB::raw('COUNT(*) as certificates'),DB::raw('MONTH(created_at) as month'))
                ->where('status','=',1)
                ->whereNotNull('file_name')
                ->whereBetween('created_at',[$start_date , $end_date])
                ->whereRaw('YEAR(created_at) = YEAR(CURDATE())')
                ->groupByRaw('MONTH(created_at)')
                ->orderByRaw('MONTH(created_at) ASC')
                ->get();

        }
        else {
            $current_year_learners = DB::table($myaccount.'.users as u')
                ->select(DB::raw('COUNT(DISTINCT o.user_id) as learners'),DB::raw('MONTH(o.created_at) as month'))
                ->join($course.'.orders as o','u.id','=','o.user_id')
                ->join($course.'.course_enrollments as ce','o.id','=','ce.order_id')
                ->join($course.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                ->where('cb.owner_id','=',1)
                ->whereNotNull('o.created_at')
                ->whereBetween('o.created_at',[$start_date , $end_date])
                ->whereRaw('YEAR(o.created_at) = YEAR(CURDATE())')
                ->groupByRaw('MONTH(o.created_at)')
                ->orderByRaw('MONTH(o.created_at) ASC')
                ->get();

            $current_year_certificates = DB::table($course.'.certificate_submit as cs')
                ->select(DB::raw('COUNT(*) as certificates'),DB::raw('MONTH(cs.created_at) as month'))
                ->join($course.'.course_enrollments as ce','cs.course_enrollment_id','=','ce.id')
                ->join($course.'.course_batches as cb','ce.course_batch_id','=','cb.id')
                ->where('cb.owner_id','=', $ownerID)
                ->whereBetween('cs.created_at',[$start_date , $end_date])
                ->whereRaw('YEAR(cs.created_at) = YEAR(CURDATE())')
                ->where('cs.status','=',1)
                ->whereNotNull('cs.file_name')
                ->groupByRaw('MONTH(cs.created_at)')
                ->orderByRaw('MONTH(cs.created_at) ASC')
                ->get();

        }



        $learners =  [0,0,0,0,0,0,0,0,0,0,0,0];
        $certificates =  [0,0,0,0,0,0,0,0,0,0,0,0];
        for($i=0;  $i <= count($current_year_learners)-1; $i++){
            $learners[$current_year_learners[$i]->month-1] = $current_year_learners[$i]->learners;
        }

        for($i=0;  $i <= count($current_year_certificates)-1; $i++){
            $certificates[$current_year_certificates[$i]->month-1] = $current_year_certificates[$i]->certificates;
        }

        $arr = array(
            'learners' => $learners,
            'certificates' => $certificates
        );

        return $arr;
    }


}