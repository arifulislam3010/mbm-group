<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Syllabus;
use App\Models\Myaccount\Sharing;
use App\Models\Assessment\SyllabusStatus;
use App\Models\Assessment\CourseEnrollment;
use DB;

class DashboardController extends Controller
{
    public function view(){

        if(config()->get('global.owner_id')){ 
            
            $sharable = Sharing::where('user_id',config()->get('global.user_id'))
            ->where('table_name','course_batches')
            ->where('db_con_name','classroom')
            ->pluck('table_id');


            $res = Syllabus::select(DB::raw('count(syllabuses.id) as total_classes'))
                    ->join('course_batches as cb','cb.id','syllabuses.course_batch_id')
                    ->join('courses as c','c.id','cb.course_id')
                    ->where(function($query) {
                            $query->where('c.owner_id',config()->get('global.owner_id'))
                                ->where('syllabuses.parent_id',null)
                                ->where('c.deleted_at',null);
                        })
                    ->when(!Config('global.view_all') , function ($query, $id) use($sharable) {
                        if($sharable){
                            $query->whereIn('cb.id',$sharable)
                            ->orwhere('cb.created_by',config()->get('global.user_id'));
                        }else{
                            $query->where('cb.created_by',config()->get('global.user_id'));
                        }
                    
                    })
                    ->first();

            $pending_reviewal = SyllabusStatus::select(DB::raw('count(syllabus_statuses.id) as pending_for_reviewal'))
                            ->join('course_batches as cb','cb.id','syllabus_statuses.course_batch_id')
                            ->join('courses as c','c.id','cb.course_id')
                            ->where('c.owner_id',config()->get('global.owner_id'))
                            ->where('syllabus_statuses.mark',null)
                            ->where('c.deleted_at',null)
                            ->first();

            $date_time = date("Y-m-d H:i:s");

            $completed = Syllabus::select(DB::raw('count(syllabuses.id) as completed_class'))->join('course_batches as cb','cb.id','syllabuses.course_batch_id')
                                ->join('courses as c','c.id','cb.course_id')
                                ->where('c.owner_id',config()->get('global.owner_id'))
                                ->where(function($query) use($date_time) {
                                    $query->where('syllabuses.parent_id',null)
                                        ->where('syllabuses.end_date','<',$date_time)
                                        ->where('c.deleted_at',null);
                                })
                        ->when(!Config('global.view_all') , function ($query, $id) use($sharable) {
                        if($sharable){
                            $query->whereIn('cb.id',$sharable)
                            ->orwhere('cb.created_by',config()->get('global.user_id'));
                        }else{
                            $query->where('cb.created_by',config()->get('global.user_id'));
                        }
                    
                    })
                    ->first();

            $total_students = CourseEnrollment::select(DB::raw('count(course_enrollments.id) as total_students'))
            ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('courses as c','c.id','cb.course_id')
                ->where('c.deleted_at',null)
                ->where('c.owner_id',config()->get('global.owner_id'))
                ->first();

            $data['total_classes'] = $res['total_classes'];
            $data['pending_for_reviewal'] = $pending_reviewal['pending_for_reviewal'];
            $data['completed_classes'] = $completed['completed_class'];
            $data['total_students'] = $total_students['total_students'];

        }else{
                $res = Syllabus::select(DB::raw('count(syllabuses.id) as total_classes'))
                    ->join('course_batches as cb','cb.id','syllabuses.course_batch_id')
                    ->join('course_enrollments as ce','ce.course_batch_id','cb.id')
                    ->join('orders as o','o.id','ce.order_id')
                    ->join('courses as c','c.id','cb.course_id')
                    ->where('syllabuses.parent_id',null)
                    ->where('c.deleted_at',null)
                    ->where('o.user_id',config()->get('global.user_id'))
                    ->first();

            $pending_reviewal = SyllabusStatus::select(DB::raw('count(syllabus_statuses.id) as pending_for_reviewal'))
                            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
                            ->join('orders as o','o.id','ce.order_id')
                            ->where('o.user_id',config()->get('global.user_id'))
                            ->where('syllabus_statuses.mark',null)
                            ->first();
                            
            $date_time = date("Y-m-d H:i:s");

            $completed = Syllabus::select(DB::raw('count(syllabuses.id) as completed_class'))->join('course_batches as cb','cb.id','syllabuses.course_batch_id')
                    ->join('course_enrollments as ce','ce.course_batch_id','cb.id')
                    ->join('orders as o','o.id','ce.order_id')
                    ->join('courses as c','c.id','cb.course_id')
                    ->where('o.user_id',config()->get('global.user_id'))
                    ->where('syllabuses.parent_id',null)
                    ->where('c.deleted_at',null)
                    ->where('syllabuses.end_date','<',$date_time)
                    ->first();



                $data['total_classes'] = $res['total_classes'];
                $data['pending_for_reviewal'] = $pending_reviewal['pending_for_reviewal'];
                $data['completed_classes'] = $completed['completed_class'];

        }

        return response()->json($data);

        
    }
}
