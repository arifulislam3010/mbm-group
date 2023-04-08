<?php

namespace Muktopaath\Course\Repositories;

use Muktopaath\Course\Interfaces\JourneyInterface;
use Muktopaath\Course\Models\Course\Completeness;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use DB;

class JourneyRepository implements JourneyInterface
{

    public function update($req){

        $journey = Completeness::where('syllabus_id',$req['syllabus_id'])
                    ->where('user_id',$req['user_id'])
                    ->first();
        if($journey){

            $journey->completeness = 100;
            $journey->update();

        }else{

            $journey = new Completeness;
            $journey->user_id = $req['user_id'];
            $journey->syllabus_id = $req['syllabus_id'];
            $journey->completeness = 100;
            $journey->save();

        }

        $parent = Syllabus::where('id',$journey->syllabus_id)->value('parent_id');

        $count = Syllabus::select(DB::raw('COUNT(id) as total'))->where('parent_id',$parent)->value('total');

        $total_completeness = Syllabus::select(DB::raw('SUM(ce.completeness) as completeness'))
                                ->join('completeness as ce','ce.syllabus_id','syllabuses.id')
                                ->where('user_id',$req['user_id'])
                                ->value('completeness');

        $updated_completeness = $total_completeness/$count;

        $chk = Completeness::where('syllabus_id',$parent)->where('user_id',$req['user_id'])->first();

        if($chk){
            $chk->completeness = $updated_completeness;
            $chk->update();
        }else{
            $chk = new Completeness;
            $chk->user_id = $req['user_id'];
            $chk->syllabus_id = $parent;
            $chk->completeness = $updated_completeness;
            $chk->save();
        }

        $batch_id = Syllabus::where('id',$parent)->value('course_batch_id');

        $count_units = Syllabus::select(DB::raw('COUNT(id) as total'))
                        ->where('course_batch_id',$batch_id)
                        ->value('total');

        $unit_total = Completeness::select(DB::raw('SUM(completeness.completeness) as total'))
                            ->join('syllabuses as s','s.id','completeness.syllabus_id')
                            ->join('course_batches as cb','cb.id','s.course_batch_id')
                            ->where('cb.id',$batch_id)
                            ->value('total');

        $ce = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                ->where('course_enrollments.course_batch_id',$batch_id)
                ->where('o.user_id',$req['user_id'])
                ->first(); 
       

        $ce->course_completeness = $unit_total/$count_units;
        $ce->update();

        return $ce;
 
        return 1;
    }
    
}