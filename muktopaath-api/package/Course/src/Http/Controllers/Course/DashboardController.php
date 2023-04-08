<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Models\Course\SyllabusStatus;
use App\Models\Myaccount\Sharing;
use Muktopaath\Course\Models\Course\CertificateSubmit;
use DB;
use Cache;

class DashboardController extends Controller
{
    public function view(Request $request){
        
        if(config()->get('global.owner_id')){

            $db = config()->get('database.connections.course.database');

            $sharable = Sharing::join($db.'.course_batches','course_batches.id','sharings.table_id')
            ->join($db.'.courses','courses.id','course_batches.course_id')
            ->where('course_batches.type',$request->course_type)
            ->where('user_id',config()->get('global.user_id'))
            ->where('table_name','course_batches')
            ->pluck('course_batches.id');

            $owner_id = config()->get('global.owner_id');

            $total_course = CourseBatch::Select(DB::raw('COUNT(id) as total'))
                        ->where('course_batches.type',$request->course_type)
                        ->when(!Config('global.view_all') , function ($query, $id) use($sharable) {
                        if($sharable){
                            $query->whereIn('course_batches.id',$sharable)
                            ->orwhere('course_batches.created_by',config()->get('global.user_id'));
                        }else{
                            $query->where('course_batches.created_by',config()->get('global.user_id'));
                        }
                    
                    })
                    ->where('owner_id',$owner_id)->value('total');

            $running_course = CourseBatch::select(DB::raw('COUNT(id) as total'))
                        ->where('course_batches.type',$request->course_type)
                        ->when(!Config('global.view_all') , function ($query, $id) use($sharable) {
                                    if($sharable){
                                        $query->whereIn('course_batches.id',$sharable)
                                        ->orwhere('course_batches.created_by',config()->get('global.user_id'));
                                    }else{
                                        $query->where('course_batches.created_by',config()->get('global.user_id'));
                                    }
                                
                                })
                                ->where('owner_id',$owner_id)
                                ->where('published_status',1)
                                ->where('end_date',null)->value('total');

            $draft = CourseBatch::select(DB::raw('COUNT(id) as total'))
                    ->where('course_batches.type',$request->course_type)
                    ->where('owner_id',$owner_id)
                    ->where(function($q){
                            $q->where('published_status','!=',1)
                                ->orWhere('published_status',null);
                        })
                        ->when(!Config('global.view_all') , function ($query, $id) use($sharable) {
                        if($sharable){
                            $query->whereIn('course_batches.id',$sharable)
                            ->orwhere('course_batches.created_by',config()->get('global.user_id'));
                        }else{
                            $query->where('course_batches.created_by',config()->get('global.user_id'));
                        }
                    
                    })
                    ->value('total');

            $certificate = CertificateSubmit::select(DB::raw('COUNT(certificate_submit.id) as total'))
                ->join('course_enrollments','course_enrollments.id','certificate_submit.course_enrollment_id')
                ->join('course_batches','course_batches.id','course_enrollments.course_batch_id')
                ->where('course_batches.type',$request->course_type)
                ->when(!Config('global.view_all') , function ($query, $id) use($sharable) {
                        if($sharable){
                            $query->whereIn('course_batches.id',$sharable)
                            ->orwhere('course_batches.created_by',config()->get('global.user_id'));
                        }else{
                            $query->where('course_batches.created_by',config()->get('global.user_id'));
                        }
                    
                    })
                ->where('course_batches.owner_id',$owner_id)
                ->value('total');


            return response()->json([
                'data' => [
                    'total_course' => $total_course,
                    'running_course' => $running_course,
                    'draft' => $draft,
                    'certificate' => $certificate
                ]
            ]);


        }
    }

    //a function which return a query of the count of number of people passed and failed in syllabus_statuses table for a particular syllabus_id

    public function performance_stats($id){

        $count = SyllabusStatus::Select(DB::raw('COUNT(id) as total'))->where('syllabus_id',$id)->value('total');
        
        $res = SyllabusStatus::Select(DB::raw('(100 * COUNT(CASE WHEN syllabus_statuses.mark < syllabus_statuses.pass_mark THEN 0 END)/'.$count.') as failed'),DB::raw('(100 * COUNT(CASE WHEN syllabus_statuses.mark >= syllabus_statuses.pass_mark THEN 1 END)/'.$count.') as passed'),DB::raw('(100 * COUNT(CASE WHEN syllabus_statuses.mark is null THEN 1 END)/'.$count.') as waiting'))
            ->where('syllabus_statuses.syllabus_id',$id)
            ->first();

        return response()->json($res);
    }



}
