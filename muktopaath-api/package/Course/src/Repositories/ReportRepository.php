<?php

namespace Muktopaath\Course\Repositories;

use Muktopaath\Course\Interfaces\ReportInterface;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Models\Course\SyllabusStatus;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Models\Course\Order;
use Maatwebsite\Excel\Facades\Excel;
use Muktopaath\Course\Exports\LearnerReport;
use Muktopaath\Course\Exports\CourseReport;
use Muktopaath\Course\Exports\LearnersCourse;
use Muktopaath\Course\Exports\CourseUserReport;
use App\Models\Myaccount\User;
use DB;

class ReportRepository implements ReportInterface
{

    public function total_learners(){

        $db = config()->get('database.connections.course.database');

        $res = User::select(DB::raw('DISTINCT(orders.user_id)'),'photo_id','users.name','users.email','users.id','orders.created_at as joining_date')
                ->join($db.'.orders','orders.user_id','users.id')
                ->join($db.'.course_enrollments as ce','ce.order_id','orders.id')
                ->join($db.'.course_batches as cb','cb.id','ce.course_batch_id')
                ->when(Request()->search, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('users.name','like','%'.$field.'%')
                            ->orWhere('users.email','like','%'.$field.'%');
                        });
                    })
                ->where('cb.owner_id',config()->get('global.owner_id'))
                ->with('photo')
                ->withCount('total_enrolled')
                ->withCount('total_certificate')
                ->groupBy('orders.user_id')
                ->paginate(10);
 
        return $res;
    }

    public function marksheet($batch_id,$enrollment_id){

        $db = config()->get('database.connections.content-bank.database');

        $res = SyllabusStatus::select('syllabus_statuses.*','s.title as syllabus_title','lc.title as content_title')
                ->join('syllabuses as s','s.id','syllabus_statuses.syllabus_id')
                ->join($db.'.learning_contents as lc','lc.id','s.learning_content_id')
                ->where('syllabus_statuses.course_batch_id',$batch_id)
                ->where('syllabus_statuses.course_enrollment_id',$enrollment_id)
                ->get();

        return response()->json($res);
    }

    public function learner_course_report($user_id){

        $db = config()->get('database.connections.my-account.database');

        $res = CourseBatch::select('ce.id as enrollment_id','ce.enrolled_by_admin','course_batches.id','course_batches.course_alias_name','ce.created_at as start_date','ce.course_completeness','course_batches.course_id')
            ->join('course_enrollments as ce','ce.course_batch_id','course_batches.id')
            ->join('orders as o','o.id','ce.order_id')
            ->join($db.'.users as u','u.id','o.user_id')
            ->with('course')
            ->when(Request()->search, function ($query, $field) {
                return $query->where(function($q) use($field){
                    $q->where('course_batches.course_alias_name','like','%'.$field.'%');
                });
            })
            ->withCount(['progress' => function($query) use($user_id)
                {
                    $query->where('completeness.user_id',$user_id);

                }])
            ->withCount('lessons')
            ->where('u.id',$user_id)
            ->get();

         return Excel::download(new LearnersCourse($res),'learners_course.xlsx');
    }

    public function courses_report(){

        $res = CourseBatch::select(DB::raw('course_batches.rating_sum/course_batches.rating_count as rating'),'course_batches.course_id','course_batches.id','course_alias_name','title','total_enrollment','course_batches.courses_for_status')
                    ->when(Request()->search, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('course_batches.course_alias_name','like','%'.$field.'%');
                        });
                    })
                    ->with('course','total_payment')
                    ->withCount('passed')
                    ->get();

        return Excel::download(new CourseReport($res),'course_report.xlsx');
    }

    public function total_courses(){

        $res = CourseBatch::select(DB::raw('course_batches.rating_sum/course_batches.rating_count as rating'),'course_batches.course_id','course_batches.id','course_alias_name','title','total_enrollment','course_batches.courses_for_status')
                    ->when(Request()->search, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('course_batches.course_alias_name','like','%'.$field.'%');
                        });
                    })
                    ->with('course','total_payment')
                    ->withCount('passed')
                    ->paginate(10);


        return response()->json($res);
    }

    public function learner_courses($user_id){

        $db = config()->get('database.connections.my-account.database');

        $res = CourseBatch::select('ce.id as enrollment_id','ce.enrolled_by_admin','course_batches.id','course_batches.course_alias_name','ce.created_at as start_date','ce.course_completeness','course_batches.course_id')
            ->join('course_enrollments as ce','ce.course_batch_id','course_batches.id')
            ->join('orders as o','o.id','ce.order_id')
            ->join($db.'.users as u','u.id','o.user_id')
            ->with('course')
            ->when(Request()->search, function ($query, $field) {
                return $query->where(function($q) use($field){
                    $q->where('course_batches.course_alias_name','like','%'.$field.'%');
                });
            })
            ->withCount(['progress' => function($query) use($user_id)
                {
                    $query->where('completeness.user_id',$user_id);

                }])
            ->with(['completion_date' => function($query) use($user_id)
                {
                    $query->where('completeness.user_id',$user_id);

                }])
            ->withCount('lessons')
            ->where('u.id',$user_id)
            ->paginate(10);

        return response()->json($res);

    }

    public function learner_stats(){

        $db = config()->get('database.connections.course.database');
        $db2 = config()->get('database.connections.my-account.database');

        $learner = User::select(DB::raw('COUNT(users.id) as total'))->join($db.'.orders','orders.user_id','users.id')
                        ->join($db.'.course_enrollments as ce','ce.order_id','orders.id')
                        ->join($db.'.course_batches as cb','cb.id','ce.course_batch_id')
                        ->where('cb.owner_id',config()->get('global.owner_id'))
                        ->value('total');

        $passed = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))
            ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','>=','syllabus_statuses.pass_mark')
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->groupBy('o.user_id')
            ->value('total');

        $failed = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))          ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->whereRaw('syllabus_statuses.mark < syllabus_statuses.pass_mark')
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->groupBy('o.user_id')
            ->value('total');

        $in_progress = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))     ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','=',null)
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->groupBy('o.user_id')
            ->value('total');


        $not_startted = User::select(DB::raw('COUNT(users.id) as total')) 
                        ->join($db.'.orders','orders.user_id','users.id')
                        ->join($db.'.course_enrollments as ce','ce.order_id','orders.id')
                        ->join($db.'.course_batches as cb','cb.id','ce.course_batch_id')
                        ->leftjoin($db.'.syllabus_statuses as s','s.course_enrollment_id','ce.id')
                        ->join($db.'.orders as o','o.id','ce.order_id')
                        ->whereNotIn('users.id',function($query) {

                           $query->select('orders.user_id');

                        })
                        ->where('cb.owner_id',config()->get('global.owner_id'))
                        ->groupBy('o.user_id')
                        ->value('total');

        $data = [
            'learner'   => $learner,
            'passed'    => $passed,
            'failed'    => $failed,
            'in_progress'   => $in_progress,
            'not_startted'  => $not_startted
        ];

        return response()->json($data);
    }

    public function learner_stat($user_id){

        $db = config()->get('database.connections.course.database');
        $db2 = config()->get('database.connections.my-account.database');
        
        
        $user = User::select('name')->where('id',$user_id)->value('name');

        $enrolled = CourseEnrollment::select(DB::raw('COUNT(course_enrollments.id) as total'))
            ->join('orders as o','o.id','course_enrollments.order_id')
                ->where('o.user_id',$user_id)
                ->value('total');

        $passed = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))
            ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','>=','syllabus_statuses.pass_mark')
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->where('o.user_id',$user_id)
            ->groupBy('o.user_id')
            ->value('total');

        $failed = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))          ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->whereRaw('syllabus_statuses.mark < syllabus_statuses.pass_mark')
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->where('o.user_id',$user_id)
            ->groupBy('o.user_id')
            ->value('total');

        $in_progress = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))     ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','=',null)
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->where('o.user_id',$user_id)
            ->groupBy('o.user_id')
            ->value('total');

        $not_started = User::select(DB::raw('COUNT(users.id) as total')) 
                    ->join($db.'.orders','orders.user_id','users.id')
                    ->join($db.'.course_enrollments as ce','ce.order_id','orders.id')
                    ->join($db.'.course_batches as cb','cb.id','ce.course_batch_id')
                    ->leftjoin($db.'.syllabus_statuses as s','s.course_enrollment_id','ce.id')
                    ->join($db.'.orders as o','o.id','ce.order_id')
                    ->whereNotIn('users.id',[$user_id])
                    ->where('cb.owner_id',config()->get('global.owner_id'))
                    ->groupBy('o.user_id')
                    ->value('total');
        $data = [
            'enrolled' => $enrolled,
            'passed'   => $passed,
            'failed'   => $failed,
            'in_progress'   => $in_progress,
            'not_started'  => $not_started,
            'user_name'   => $user
        ];

        return response()->json($data);

    }

    public function course_stats(){

        $db = config()->get('database.connections.course.database');
        $db2 = config()->get('database.connections.my-account.database');

        $course = CourseBatch::select(DB::raw('COUNT(id) as total_course'),DB::raw('SUM(total_enrollment) as total_enrolled'))
                        ->where('owner_id',config()->get('global.owner_id'))
                        ->first();

        $passed = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))
            ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','>=','syllabus_statuses.pass_mark')
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->groupBy('o.user_id')
            ->value('total');

        $failed = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))
            ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','<','syllabus_statuses.pass_mark')
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->groupBy('o.user_id')
            ->value('total');

        $in_progress = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))     ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','=',null)
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->groupBy('o.user_id')
            ->value('total');


        $not_startted = User::select(DB::raw('COUNT(users.id) as total')) 
                        ->join($db.'.orders','orders.user_id','users.id')
                        ->join($db.'.course_enrollments as ce','ce.order_id','orders.id')
                        ->join($db.'.course_batches as cb','cb.id','ce.course_batch_id')
                        ->leftjoin($db.'.syllabus_statuses as s','s.course_enrollment_id','ce.id')
                        ->join($db.'.orders as o','o.id','ce.order_id')
                        ->whereNotIn('users.id',function($query) {

                           $query->select('orders.user_id');

                        })
                        ->where('cb.owner_id',config()->get('global.owner_id'))
                        ->groupBy('o.user_id')
                        ->value('total');


        $data = [
            'total_courses' => $course->total_course,
            'total_enrolled'    => $course->total_enrolled,
            'passed'    => $passed,
            'in_progress'   => $in_progress,
            'failed'    => $failed,
            'not_startted'  => $not_startted
        ];

        return response()->json($data);
    }

    public function course_stat($batch_id){

        $db = config()->get('database.connections.course.database');
        $db2 = config()->get('database.connections.my-account.database');

        $course_name = CourseBatch::select('course_alias_name')->where('id',$batch_id)
                    ->value('course_alias_name');

        $learner = User::select(DB::raw('COUNT(users.id) as total'))->join($db.'.orders','orders.user_id','users.id')
                ->join($db.'.course_enrollments as ce','ce.order_id','orders.id')
                ->join($db.'.course_batches as cb','cb.id','ce.course_batch_id')
                ->where('cb.owner_id',config()->get('global.owner_id'))
                ->where('cb.id',$batch_id)
                ->value('total');

        $passed = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))
            ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','>=','syllabus_statuses.pass_mark')
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->where('cb.id',$batch_id)
            ->groupBy('o.user_id')
            ->value('total');

        $in_progress = SyllabusStatus::select(DB::raw('COUNT(syllabus_statuses.id) as total'))     ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('syllabus_statuses.mark','=',null)
            ->where('cb.owner_id',config()->get('global.owner_id'))
            ->where('cb.id',$batch_id)
            ->groupBy('o.user_id')
            ->value('total');


        $not_startted = User::select(DB::raw('COUNT(users.id) as total')) 
                        ->join($db.'.orders','orders.user_id','users.id')
                        ->join($db.'.course_enrollments as ce','ce.order_id','orders.id')
                        ->join($db.'.course_batches as cb','cb.id','ce.course_batch_id')
                        ->leftjoin($db.'.syllabus_statuses as s','s.course_enrollment_id','ce.id')
                        ->join($db.'.orders as o','o.id','ce.order_id')
                        ->whereNotIn('users.id',function($query) {

                           $query->select('orders.user_id');

                        })
                        ->where('cb.owner_id',config()->get('global.owner_id'))
                        ->where('cb.id',$batch_id)
                        ->groupBy('o.user_id')
                        ->value('total');

        $payment = CourseBatch::where('id',$batch_id)
                    ->with('total_payment')
                    ->first();


        $data = [
            'learner' => $learner,
            'passed'    => $passed,
            'in_progress'   => $in_progress,
            'not_startted'  => $not_startted,
            'payment'   => isset($payment->total_payment)?$payment->total_payment->amount:0,
            'course_name'   => $course_name
        ];

        return response()->json($data);
    }

    public function course_users($batch_id){

        $db = config()->get('database.connections.course.database');

        $res = User::select('users.photo_id','users.id','users.name','users.email','o.created_at as joining_date')
                ->join($db.'.orders as o','o.user_id','users.id')
                ->join($db.'.course_enrollments as ce','ce.order_id','o.id')
                ->with('photo')
                ->withCount(['course_progress' => function($query) use($batch_id,$db)
                {
                    $query->join($db.'.syllabuses','syllabuses.id','completeness.syllabus_id')
                          ->where('syllabuses.course_batch_id',$batch_id);

                }])
                ->when(Request()->search, function ($query, $field) {
                return $query->where(function($q) use($field){
                    $q->where('users.name','like','%'.$field.'%')
                    ->orWhere('users.email','like','%'.$field.'%');;
                });
            })
                ->where('ce.course_batch_id',$batch_id)
                ->paginate(10);

        return response()->json($res);
    }


    public function course_users_report($batch_id){

        $db = config()->get('database.connections.course.database');

        $res = User::select('users.photo_id','users.id','users.name','users.email','o.created_at as joining_date')
                ->join($db.'.orders as o','o.user_id','users.id')
                ->join($db.'.course_enrollments as ce','ce.order_id','o.id')
                ->withCount(['course_progress' => function($query) use($batch_id,$db)
                {
                    $query->join($db.'.syllabuses','syllabuses.id','completeness.syllabus_id')
                          ->where('syllabuses.course_batch_id',$batch_id);

                }])
                ->when(Request()->search, function ($query, $field) {
                return $query->where(function($q) use($field){
                    $q->where('users.name','like','%'.$field.'%')
                    ->orWhere('users.email','like','%'.$field.'%');;
                });
            })
                ->where('ce.course_batch_id',$batch_id)
                ->take(10000)
                ->skip(Request()->download_limit - 10000)
                ->get();

        return Excel::download(new CourseUserReport($res),'course_users.xlsx');
    }


    public function learners_report(){
        $db = config()->get('database.connections.course.database');

        $res = User::select(DB::raw('DISTINCT(orders.user_id)'),'photo_id','users.name','users.email','users.id','orders.created_at as joining_date')
                ->join($db.'.orders','orders.user_id','users.id')
                ->join($db.'.course_enrollments as ce','ce.order_id','orders.id')
                ->join($db.'.course_batches as cb','cb.id','ce.course_batch_id')
                ->when(Request()->search, function ($query, $field) {
                        return $query->where(function($q) use($field){
                            $q->where('users.name','like','%'.$field.'%')
                            ->orWhere('users.email','like','%'.$field.'%');
                        });
                    })
                ->where('cb.owner_id',config()->get('global.owner_id'))
                ->with('photo')
                ->withCount('total_enrolled')
                ->withCount('total_certificate')
                ->groupBy('orders.user_id')
                ->get();

        return $res;

        //return Excel::download(new LearnerReport($res),'learners.xlsx');
    }
    
}