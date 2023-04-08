<?php

namespace Muktopaath\Course\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use Carbon\CarbonPeriod;

//Models
Use App\Models\Myaccount\User;
Use App\Models\Myaccount\UserInfo;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Models\Course\CourseBatch;
use Illuminate\Support\Facades\Session;
use Muktopaath\Course\Models\Course\CertificateSubmit;
Use App\Models\Myaccount\InstitutionInfo;
use Excel;

class CopyReportController extends Controller
{
    public function totalLearnersStatisticsData(Request $request)
    {
        $RouteUserName = config('global.username');

        $RouteUser = InstitutionInfo::where('username',$RouteUserName)->first()->id;

        $other_credential = null;
        if(config('global.role')->id!=1)
            $other_credential = $RouteUser;

        $date_wise      = ($request['filterData'] == 'date_wise')?$request['filterData']:null;

        $start      = (isset($request['start'])&& !is_null($request['start']))?date('Y-m-d 00:00:00', strtotime($request['start'])):null;
        $end        = isset($request['end'])?date('Y-m-d 23:59:59', strtotime($request['end'])):null;
        
        //Weeks
        $day = date('w');
        $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
        $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));

        //Months
        $month_start = date('Y-m-01 00:00:00', strtotime(date('Y-m-d')));
        $month_end  = date('Y-m-t 23:59:59', strtotime(date('Y-m-d')));
        
        //Last Months
        $last_month_start = date("Y-m-d 00:00:00", strtotime("first day of previous month"));
        $last_month_end  = date("Y-m-d 23:59:59", strtotime("last day of previous month"));

        $date1 = new \DateTime($end);
        $date2 = new \DateTime($start);

        $diff = $date1->diff($date2);

        $total_month = (($diff->format('%y') * 12) + $diff->format('%m'.".".'%d'));
        $total_month = (float) $total_month;
        
        $date_filter = '"%Y"';

        if($total_month>=12 || $total_month == 0)
            $date_filter = '"%Y"';
        else if($total_month>1 && $total_month<12)
            $date_filter = '"%b-%Y"';
        else if($total_month<=1)
            $date_filter = '"%d-%b-%Y"';

            //return $date_filter;
        
        $total_registration = User::select(array(
                                DB::raw("DATE_FORMAT(users.created_at,$date_filter) AS filter_key"),
                                DB::raw("count(users.id) AS filter_value")
                            ))
                            ->groupBy('filter_key')
                            ->orderBy('users.created_at', 'asc')
                            ->when($other_credential, function($q) use($other_credential){
                                return $q->join('orders', 'users.id', 'orders.user_id')
                                        ->join('course_enrollments', 'course_enrollments.order_id', 'orders.id')
                                        ->join('course_batches', 'course_batches.id', 'course_enrollments.course_batch_id')
                                        ->where('course_batches.owner_id', $other_credential)
                                        ->distinct('orders.user_id');
                            })
                            ->when($start, function($q) use($start,$end){return $q->whereBetween('users.created_at' , [$start, $end]);})
                            ->get()
                            ->toArray();

        $total_enrollment = CourseEnrollment::select(array(
                                DB::raw("DATE_FORMAT(course_enrollments.created_at,$date_filter) AS filter_key"),
                                DB::raw("count(course_enrollments.id) AS filter_value")
                            ))
                            ->groupBy('filter_key')
                            ->orderBy('course_enrollments.created_at', 'asc')
                            ->when($other_credential, function($q) use($other_credential){
                                return $q->join('course_batches', 'course_batches.id', 'course_enrollments.course_batch_id')
                                        ->where('course_batches.owner_id', $other_credential);
                            })
                            ->when($start, function($q) use($start,$end){return $q->whereBetween('course_enrollments.created_at' , [$start, $end]);})
                            ->get()
                            ->toArray();

        $total_profile_completness = User::select(array(
                                DB::raw("DATE_FORMAT(users.created_at,$date_filter) AS filter_key"),
                                DB::raw("count(users.id) AS filter_value")
                            ))
                            ->where('users.completeness', 100)
                            ->groupBy('filter_key')
                            ->orderBy('users.created_at', 'asc')
                            ->when($other_credential, function($q) use($other_credential){
                                return $q->join('orders', 'users.id', 'orders.user_id')
                                ->join('course_enrollments', 'course_enrollments.order_id', 'orders.id')
                                ->join('course_batches', 'course_batches.id', 'course_enrollments.course_batch_id')
                                ->where('course_batches.owner_id', $other_credential)
                                ->distinct('orders.user_id');
                            })
                            ->when($start, function($q) use($start,$end){return $q->whereBetween('users.created_at' , [$start, $end]);})
                            ->get()
                            ->toArray();

        $total_certificate_issue = CertificateSubmit::select(array(
                                DB::raw("DATE_FORMAT(certificate_submit.updated_at,$date_filter) AS filter_key"),
                                DB::raw("count(certificate_submit.id) AS filter_value")
                            ))
                            ->whereNotNull('certificate_submit.file_name')
                            ->groupBy('filter_key')
                            ->orderBy('certificate_submit.updated_at', 'asc')
                            ->when($other_credential, function($q) use($other_credential){
                                return $q->join('course_enrollments', 'course_enrollments.id', 'certificate_submit.course_enrollment_id')
                                ->join('course_batches', 'course_batches.id', 'course_enrollments.course_batch_id')
                                ->where('course_batches.owner_id', $other_credential);
                            })
                            ->when($start, function($q) use($start,$end){return $q->whereBetween('certificate_submit.updated_at' , [$start, $end]);})
                            ->get()
                            ->toArray();

        if($start && $end){
            $date_difference = $diff->format('%a');
            $previous_start_date = $date2->modify("-$date_difference day")->format('Y-m-d');

            $total_registration_previous = User::when($start, function($q) use($start,$previous_start_date){
                                return $q->whereBetween('users.created_at' , [$previous_start_date, $start]);
                            })
                            ->when($other_credential, function($q) use($other_credential){
                                return $q->join('orders', 'users.id', 'orders.user_id')
                                        ->join('course_enrollments', 'course_enrollments.order_id', 'orders.id')
                                        ->join('course_batches', 'course_batches.id', 'course_enrollments.course_batch_id')
                                        ->where('course_batches.owner_id', $other_credential)
                                        ->distinct('orders.user_id');
                            })->count();

            $total_enrollment_previous = CourseEnrollment::when($start, function($q) use($start,$previous_start_date){
                                        return $q->whereBetween('course_enrollments.created_at' , [$previous_start_date, $start]);
                                    })
                                    ->when($other_credential, function($q) use($other_credential){
                                        return $q->join('course_batches', 'course_batches.id', 'course_enrollments.course_batch_id')
                                                ->where('course_batches.owner_id', $other_credential);
                                    })->count();

            $total_profile_completness_previous = User::where('users.completeness', 100)
                            ->when($start, function($q) use($start,$previous_start_date){return $q->whereBetween('users.created_at' , [$previous_start_date, $start]);})
                            ->when($other_credential, function($q) use($other_credential){
                                return $q->join('orders', 'users.id', 'orders.user_id')
                                ->join('course_enrollments', 'course_enrollments.order_id', 'orders.id')
                                ->join('course_batches', 'course_batches.id', 'course_enrollments.course_batch_id')
                                ->where('course_batches.owner_id', $other_credential)
                                ->distinct('orders.user_id');
                            })
                            ->count();

            $total_certificate_issue_previous = CertificateSubmit::when($start, function($q) use($start,$previous_start_date){
                                return $q->whereBetween('certificate_submit.updated_at' , [$previous_start_date, $start]);
                            })
                            ->whereNotNull('certificate_submit.file_name')
                            ->when($other_credential, function($q) use($other_credential){
                                return $q->join('course_enrollments', 'course_enrollments.id', 'certificate_submit.course_enrollment_id')
                                ->join('course_batches', 'course_batches.id', 'course_enrollments.course_batch_id')
                                ->where('course_batches.owner_id', $other_credential);
                            })->count();

        }

        // $total_data = [];
        // $total_data['total_registration']           = $total_registration;
        // $total_data['total_enrollment']             = $total_enrollment;
        // $total_data['total_profile_completness']    = $total_profile_completness;
        // $total_data['total_certificate_issue']      = $total_certificate_issue;

        //return $total_data;

        $total_registration_count = 0;
        $total_enrollment_count = 0;
        $total_profile_completness_count = 0;
        $total_certificate_issue_count = 0;


        $main_data = [["Year", "Total Registration", "Total Enrollment", "Total Profile Complete", "Certificate Issued"]];

        if(empty($total_registration) && empty($total_enrollment) && empty($total_profile_completness) && empty($total_certificate_issue)){
            array_push($main_data,["Year", 0, 0, 0, 0]);
        }

        foreach($total_registration as $key => $value){
            $init_data = [];

            array_push($init_data, $value['filter_key']);
            array_push($init_data, $value['filter_value']);
            
            $total_registration_count += $value['filter_value'];

            /* Enrollment */
            $match_data = array_search($value['filter_key'],array_column($total_enrollment, 'filter_key'));
            
            if($match_data !== false){
                array_push($init_data,$total_enrollment[$match_data]['filter_value']);
                $total_enrollment_count += (integer)$total_enrollment[$match_data]['filter_value'];
            }
            else{
                array_push($init_data,0);
            }

            /* Profile Complete */
            $match_data = array_search($value['filter_key'],array_column($total_profile_completness, 'filter_key'));
            
            if($match_data !== false){
                array_push($init_data,$total_profile_completness[$match_data]['filter_value']);
                $total_profile_completness_count += (integer)$total_profile_completness[$match_data]['filter_value'];
            }
            else{
                array_push($init_data,0);
            }

            /* Certificate Issued */
            $match_data = array_search($value['filter_key'],array_column($total_certificate_issue, 'filter_key'));
            
            if($match_data !== false){
                array_push($init_data,$total_certificate_issue[$match_data]['filter_value']);
                $total_certificate_issue_count += (integer)$total_certificate_issue[$match_data]['filter_value'];
            }
            else{
                array_push($init_data,0);
            }
            
            array_push($main_data,$init_data);

        }

        //Increments is 1 Decrement is 0

        //Registration
        
        $registration_progress = ['progress_data' => 0,'progress_type' => 2];

        if($start && $end){
            $progress_result = abs($total_registration_count - $total_registration_previous);

            if($total_registration_count>0)
                $progress_result = round((($progress_result*100)/$total_registration_count),2);
            else
                $progress_result = 0;


            $registration_progress['progress_data'] = $progress_result;

            if($total_registration_count > $total_registration_previous)
                $registration_progress['progress_type'] = 1;
            else
                $registration_progress['progress_type'] = 0;
        }

        //Enrollment

        $enrollment_progress = ['progress_data' => 0,'progress_type' => 2];

        if($start && $end){
            $progress_result = abs($total_enrollment_count - $total_enrollment_previous);

            if($total_enrollment_count>0)
                $progress_result = round((($progress_result*100)/$total_enrollment_count),2);
            else
                $progress_result = 0;

            $enrollment_progress['progress_data'] = $progress_result;

            if($total_enrollment_count > $total_enrollment_previous)
                $enrollment_progress['progress_type'] = 1;
            else
                $enrollment_progress['progress_type'] = 0;
        }

        //Profile

        $profile_completness_progress = ['progress_data' => 0,'progress_type' => 2];

        if($start && $end){
            $progress_result = abs($total_profile_completness_count - $total_profile_completness_previous);

            if($total_profile_completness_count>0)
                $progress_result = round((($progress_result*100)/$total_profile_completness_count),2);
            else
                $progress_result = 0;

            $profile_completness_progress['progress_data'] = $progress_result;

            if($total_profile_completness_count > $total_profile_completness_previous)
                $profile_completness_progress['progress_type'] = 1;
            else
                $profile_completness_progress['progress_type'] = 0;
        }

        //Certificate

        $certificate_progress = ['progress_data' => 0,'progress_type' => 2];

        if($start && $end){
            $progress_result = abs($total_certificate_issue_count - $total_certificate_issue_previous);

            if($total_certificate_issue_count>0)
                $progress_result = round((($progress_result*100)/$total_certificate_issue_count),2);
            else
                $progress_result = 0;

            $certificate_progress['progress_data'] = $progress_result;

            if($total_certificate_issue_count > $total_certificate_issue_previous)
                $certificate_progress['progress_type'] = 1;
            else
                $certificate_progress['progress_type'] = 0;
        }

        $total_data = [];
        $total_data['total_data']                     = $main_data;
        $total_data['total_registration']             = $total_registration_count;
        $total_data['total_enrollment']               = $total_enrollment_count;
        $total_data['total_profile_completness']      = $total_profile_completness_count;
        $total_data['total_certificate_issue']        = $total_certificate_issue_count;

        
        $total_data['registration_progress']            = $registration_progress;
        $total_data['enrollment_progress']              = $enrollment_progress;
        $total_data['profile_completness_progress']     = $profile_completness_progress;
        $total_data['certificate_progress']             = $certificate_progress;
        

        $total_data_2 = [
                            ["Month", "Total Registration", "Total Enrollment", "Profile Complete", "Certificate Issued"],
                            ["Jan", 10, 4, 5, 1],
                            ["Feb", 9, 5, 7, 6],
                            ["Mar", 6, 9, 4, 1],
                            ["Apr", 1, 5, 1, 6],
                            ["May", 0, 5, 1, 6],
                            ["Jun", 4, 7, 2, 1],
                            ["Jul", 1, 9, 7, 7],
                            ["Aug", 7, 5, 2, 1],
                            ["Sep", 1, 9, 7, 10],
                            ["Oct", 3, 5, 0, 6],
                            ["Nov", 5, 0, 10, 2],
                            ["Dec", 1, 3, 4, 0]
                        ];

        return response()->json($total_data);
    }

    public function completeness(Request $request){

        $db = config()->get('database.connections.course.database');

        $RouteUser = config()->get('global.owner_id');

        $batch = (object) [];
        $batch->id = $request->batch_id?$request->batch_id:848;
        //$batch_id = 47

        // $res = CourseEnrollment::select('ed.id','ed.name',DB::raw('COUNT(course_enrollments.id) as enrollment'),DB::raw('SUM(CASE WHEN course_enrollments.course_completeness = 100.00 THEN 1 ELSE 0 END) as completeness'))
        //     ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
        //     ->join('orders as o','o.id','course_enrollments.order_id')
        //     ->join('emis_info as ei','o.user_id','u.id')
        //     ->leftJoin('emis_distric as ed','ed.id','ei.DistrictId')
        //     ->where('cb.owner_id',$RouteUser)
        //     ->groupBy('ed.id')
        //     ->get();

        // $district = DB::table('emis_distric')->get();

        // $res = DB::table('emis_info as ei')
        //     ->select('ei.DistrictId as id',DB::raw('SUM(CASE WHEN ei.is_enroll = 1 THEN 1 ELSE 0 END) as enrollment'),DB::raw('SUM(CASE WHEN ei.is_complete = 1 THEN 1 ELSE 0 END) as completeness'))
        //     ->groupBy('ei.DistrictId')
        //     ->get();
        if(Request()->map_index=='enrolled'){

            // $res = DB::table('emis_info as ei')
            // ->select('ei.DistrictId as id',DB::raw('SUM(CASE WHEN ei.is_enroll = 1 THEN 1 ELSE 0 END) as completeness'))
            // ->groupBy('ei.DistrictId')
            // ->get();
            $qryStr = 'SELECT e.DistrictId AS id,COUNT(DISTINCT ce.order_id) AS enrollment
            FROM '.$db.'.course_enrollments ce
            JOIN (
                SELECT id,user_id FROM '.$db.'.orders
            ) o ON o.id = ce.order_id
            JOIN (
                SELECT DistrictId,user_id FROM '.$db.'.emis_info
            ) e ON e.user_id = o.user_id
            WHERE ce.course_batch_id = ' . $batch->id . '
            GROUP BY e.DistrictId';
            $cmp = DB::select($qryStr);

            // $cmp = DB::table('emis_institutions')
            //     ->select('eu.district_id as id',DB::raw('SUM(emis_institutions.teacher) as enrollment'))
            //     ->join('emis_upozila as eu','eu.id','emis_institutions.upazila_id')
            //     ->groupBy('eu.district_id')
            //     ->get();
            $qryStr = 'SELECT e.DistrictId AS id,COUNT(DISTINCT ce.order_id) AS completeness
            FROM '.$db.'.course_enrollments ce
            JOIN (
                SELECT course_enrollment_id,file_name FROM '.$db.'.certificate_submit
            ) c ON c.course_enrollment_id = ce.id
            JOIN (
                SELECT id,user_id FROM '.$db.'.orders
            ) o ON o.id = ce.order_id
            JOIN (
                SELECT DistrictId,user_id FROM '.$db.'.emis_info
            ) e ON e.user_id = o.user_id
            WHERE ce.course_batch_id = ' . $batch->id . '
            AND c.file_name != ""
            GROUP BY e.DistrictId';
            $res = DB::select($qryStr);
        }else if(Request()->map_index=='completeness'){

            // $res = DB::table('emis_info as ei')
            // ->select('ei.DistrictId as id',DB::raw('SUM(CASE WHEN ei.is_complete = 1 THEN 1 ELSE 0 END) as completeness'))
            // ->groupBy('ei.DistrictId')
            // ->get();

            $qryStr = 'SELECT e.DistrictId AS id,COUNT(DISTINCT ce.order_id) AS completeness
            FROM '.$db.'.course_enrollments ce
            JOIN (
                SELECT course_enrollment_id,file_name FROM '.$db.'.certificate_submit
            ) c ON c.course_enrollment_id = ce.id
            JOIN (
                SELECT id,user_id FROM '.$db.'.orders
            ) o ON o.id = ce.order_id
            JOIN (
                SELECT DistrictId,user_id FROM '.$db.'.emis_info
            ) e ON e.user_id = o.user_id                
            WHERE ce.course_batch_id = ' . $batch->id . '
            AND c.file_name != ""
            GROUP BY e.DistrictId';
            $res = DB::select($qryStr);

            // $cmp = DB::table('emis_info as ei')
            // ->select('ei.DistrictId as id',DB::raw('SUM(CASE WHEN ei.is_enroll = 1 THEN 1 ELSE 0 END) as enrollment'))
            // ->groupBy('ei.DistrictId')
            // ->get();

            $qryStr = 'SELECT e.DistrictId AS id,COUNT(DISTINCT ce.order_id) AS enrollment
            FROM '.$db.'.course_enrollments ce
            JOIN (
                SELECT id,user_id FROM '.$db.'.orders
            ) o ON o.id = ce.order_id
            JOIN (
                SELECT DistrictId,user_id FROM '.$db.'.emis_info
            ) e ON e.user_id = o.user_id
            WHERE ce.course_batch_id = ' . $batch->id . '
            GROUP BY e.DistrictId';
            $cmp = DB::select($qryStr);
        }        

        $district = DB::table('emis_distric')->get();
        $data = array(); $district_ids = [];
        foreach ($district as $v) {
            $v = (array)$v;
            $data[$v['id']]['id'] = $v['id'];
            $data[$v['id']]['name'] = $v['name'];
            $data[$v['id']]['name_bn'] = $v['name_bn'];
            array_push($district_ids, $v['id']);
        }

        $totalTeacherInfo = DB::table('emis_institutions')
            ->select(DB::raw('emis_upozila.district_id AS id,SUM(emis_institutions.teacher) as total_teacher'))
            ->join('emis_upozila','emis_institutions.upazila_id','emis_upozila.id')
            ->whereIn('emis_upozila.district_id',$district_ids)
            ->groupBy('emis_upozila.district_id')
            ->get();

        foreach ($totalTeacherInfo as $v) {
            $v = (array)$v;
            $data[$v['id']]['total_teacher'] = (int)$v['total_teacher'];
        }

        foreach ($cmp as $v) {
            $v = (array)$v;
            $data[$v['id']]['enrollment'] = (int)$v['enrollment'];
        }

        foreach ($res as $v) {
            $v = (array)$v;
            $data[$v['id']]['completeness'] = (int)$v['completeness'];
        }

        return response()->json($data);
    }

    public function stat_data(Request $request){

        if($request->batch_id){

            $total = DB::table('emis_info')->value(DB::raw('COUNT(id)'));

            $enrollment = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('emis_info as ei','ei.user_id','o.user_id')
                ->join('emis_distric as ed','ed.id','ei.DistrictId')
                ->where('course_enrollments.course_batch_id',$request->batch_id)
                ->value(DB::raw('COUNT(course_enrollments.id)'));
        }
        
    }

    public function divisions(){
        $res = DB::table('emis_division')->get();
        return response()->json($res);
    }

    public function districts($division_id){
        $res = DB::table('emis_distric')->select('id','name','name_bn')->where('division_id',$division_id)->get();
        return response()->json($res);
    }

    public function upazilas($district_id){
        $res = DB::table('emis_upozila')->where('district_id',$district_id)->get();
        return response()->json($res);
    }

    public function emis_institutions($upazila_id){
        $res = DB::table('emis_institutions')->where('upazila_id',$upazila_id)->get();
        return response()->json($res);
    }

    public function donut(Request $request)
    {

        $db = config()->get('database.connections.course.database');

        $batch = (object) [];
        $batch->id = $request->batch_id?$request->batch_id:848;
        // $RouteUserName = config('global.username');

        // $RouteUser = InstitutionInfo::where('username', $RouteUserName)->first()->id;

        //$data['certificate'] = $certificate?$certificate->total_certificate:0;
        // $enroll = DB::table('emis_info')->select(DB::raw('COUNT(*) as total_enrolled'))
        //             ->where('is_enroll', 1)->first();
        // $qryStr = 'SELECT COUNT(DISTINCT ce.order_id) AS total_order
        //         FROM course_enrollments ce
        //         JOIN (
        //             SELECT id,user_id FROM orders
        //         ) o ON o.id = ce.order_id
        //         where ce.course_batch_id = ' . $batch->id;
        // $res = DB::select($qryStr);

        $totalEnrolled = CourseEnrollment::where('course_batch_id',$batch->id)->count(DB::raw('DISTINCT order_id'));

        // $certificate = DB::table('emis_info')->select(DB::raw('COUNT(*) as total_certificate'))->where('is_complete', 1)->first();

        $totalCertificate = CourseEnrollment::where('course_enrollments.course_batch_id',$batch->id)
        ->JOIN('certificate_submit AS cs','cs.course_enrollment_id','course_enrollments.id')
        ->where('cs.file_name','!=','')
        ->count();

        // $cer = $certificate ? $certificate->total_certificate : 0;

        $data['enrolled'] = $totalEnrolled; // ($enroll->total_enrolled - $cer);
        $data['certificate'] =  $totalCertificate; //$certificate ? $certificate->total_certificate : 0;
        // $data['real_enroll'] = $enroll->total_enrolled;
        // $data['real_enroll'] = $enrollTotaC;

        $division = DB::table('emis_division')->get();
        $divisions = array();
        foreach ($division as $v) {
            $v = (array)$v;
            $divisions[$v['id']]['name'] = $v['name'];
            $divisions[$v['id']]['name_bn'] = $v['name_bn'];
        }

        // $totalInfo = DB::table('emis_info')->select(DB::raw('divisionid as id,COUNT(*) as total_teacher'))->groupBy('divisionid')->get();
        $totalInfo = DB::table('emis_institutions AS ei')
            ->join('emis_upozila AS eu','eu.id','ei.upazila_id')
            ->join('emis_distric AS ed','ed.id','eu.district_id')
            ->select(DB::raw('ed.division_id as id,SUM(ei.teacher) as total_teacher'))
            ->groupBy('ed.division_id')->get();
        foreach ($totalInfo as $v) {
            $v = (array)$v;
            $divisions[$v['id']]['total_teacher'] = (int)$v['total_teacher'];
        }

        // $enrollInfo = DB::table('emis_info')->select(DB::raw('divisionid as id,COUNT(*) as total_enroll'))->groupBy('divisionid')->where('is_enroll',1)->get();
        
        $qryStr = 'SELECT e.DivisionId AS id,COUNT(DISTINCT ce.order_id) AS total_enroll
                FROM '.$db.'.course_enrollments ce
                JOIN (
                    SELECT id,user_id FROM '.$db.'.orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DivisionId,user_id FROM '.$db.'.emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                GROUP BY e.DivisionId';

        $enrollInfo = DB::select($qryStr);

        foreach ($enrollInfo as $v) {
            $v = (array)$v;
            $divisions[$v['id']]['total_enroll'] = $v['total_enroll'];
        }

        // $certificateInfo = DB::table('emis_info')->select(DB::raw('divisionid as id,COUNT(*) as total_cer'))->groupBy('divisionid')->where('is_complete',1)->get();

        $qryStr = 'SELECT e.DivisionId AS id,COUNT(DISTINCT ce.order_id) AS total_cer
                FROM course_enrollments ce
                JOIN (
                    SELECT course_enrollment_id,file_name FROM certificate_submit
                ) c ON c.course_enrollment_id = ce.id
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DivisionId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND c.file_name != ""
                GROUP BY e.DivisionId';
        $certificateInfo = DB::select($qryStr);

        foreach ($certificateInfo as $v) {
            $v = (array)$v;
            $divisions[$v['id']]['total_certificate'] = $v['total_cer'];
        }

        $data['divisions'] = $divisions;

        /*$data['divisions'] = DB::table('emis_division')
        ->select('emis_division.id',DB::raw('COUNT(DISTINCT(o.user_id)) as total_enroll'),DB::raw('SUM(ei.teacher) as total_teacher'),'emis_division.name',DB::raw('COUNT(DISTINCT(certificate_submit.course_enrollment_id)) as total_certificate'))
                            ->leftJoin('emis_distric as ed','ed.division_id','emis_division.id')
                            ->leftjoin('emis_upozila as eu','eu.district_id','ed.id')
                ->leftJoin('emis_institutions as ei','ei.upazila_id','eu.id')
                ->leftjoin('emis_info as ein','ein.DistrictId','ed.id')
                ->leftJoin('users as u','u.id','ein.user_id')
                ->leftJoin('orders as o','o.user_id','u.id')
                ->leftjoin('course_enrollments as ce','ce.order_id','o.id')
                ->leftJoin('certificate_submit','certificate_submit.course_enrollment_id','ce.id')
                ->leftJoin('course_batches as cb','cb.id','ce.course_batch_id')
                ->where(function($q) use($RouteUser){
                            $q->where('cb.owner_id',$RouteUser)
                               ->orWhereNull('cb.owner_id');
                        })
                ->groupBy('emis_division.id')
                ->get();*/

        return response()->json($data);

    }


    public function all_data(Request $request){

        $RouteUser = config()->get('global.owner_id');

        $batch = (object) [];
        $batch->id = $request->batch_id?$request->batch_id:848;

        if($request->division_id){

            $division = DB::table('emis_distric')->where('division_id',$request->division_id)->get();
            $divisions = array();
            foreach ($division as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['id'] = $v['id'];
                $divisions[$v['id']]['name'] = $v['name'];
                $divisions[$v['id']]['name_bn'] = $v['name_bn'];
                $totalInfo = DB::table('emis_institutions')
                ->select(DB::raw('SUM(emis_institutions.teacher) as total_teacher'))->join('emis_upozila','emis_institutions.upazila_id','emis_upozila.id')
                ->where('emis_upozila.district_id',$v['id'])
                ->first();

                $divisions[$v['id']]['total_teacher'] = $totalInfo->total_teacher;
            }

            // $enrollInfo = DB::table('emis_info')->select(DB::raw('DistrictId as id,COUNT(*) as total_enroll'))->where('DivisionId',$request->division_id)->groupBy('DistrictId')->where('is_enroll',1)->get();

            $qryStr = 'SELECT e.DistrictId AS id,COUNT(DISTINCT ce.order_id) AS total_enroll
                FROM course_enrollments ce
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DivisionId,DistrictId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND e.DivisionId = ' . $request->division_id . '
                GROUP BY e.DistrictId';
            $enrollInfo = DB::select($qryStr);

            foreach ($enrollInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_enroll'] = $v['total_enroll'];
            }

            // $certificateInfo = DB::table('emis_info')->select(DB::raw('DistrictId as id,COUNT(*) as total_cer'))->where('DivisionId',$request->division_id)->groupBy('DistrictId')->where('is_complete',1)->get();

            $qryStr = 'SELECT e.DistrictId AS id,COUNT(DISTINCT ce.order_id) AS total_cer
                FROM course_enrollments ce
                JOIN (
                    SELECT course_enrollment_id,file_name FROM certificate_submit
                ) c ON c.course_enrollment_id = ce.id
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DivisionId,DistrictId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND e.DivisionId = ' . $request->division_id . '
                AND c.file_name != ""
                GROUP BY e.DistrictId';
            $certificateInfo = DB::select($qryStr);

            foreach ($certificateInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_certificate'] = $v['total_cer'];
            }

            $res = $divisions;

        }else if($request->district_id){

            $division = DB::table('emis_upozila')->where('district_id',$request->district_id)->get();
            $divisions = array();
            foreach ($division as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['id'] = $v['id'];
                $divisions[$v['id']]['name'] = $v['name'];
                $divisions[$v['id']]['name_bn'] = $v['name_bn'];
                $totalInfo = DB::table('emis_institutions')
                ->select(DB::raw('SUM(emis_institutions.teacher) as total_teacher'))
                    ->where('emis_institutions.upazila_id',$v['id'])
                ->first();

                $divisions[$v['id']]['total_teacher'] = $totalInfo->total_teacher;
            }

            // $enrollInfo = DB::table('emis_info')->select(DB::raw('UpazillaId as id,COUNT(*) as total_enroll'))->where('DistrictId',$request->district_id)->groupBy('UpazillaId')->where('is_enroll',1)->get();

            $qryStr = 'SELECT e.UpazillaId AS id,COUNT(DISTINCT ce.order_id) AS total_enroll
                FROM course_enrollments ce
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DistrictId,UpazillaId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND e.DistrictId = ' . $request->district_id . '
                GROUP BY e.UpazillaId';
            $enrollInfo = DB::select($qryStr);

            foreach ($enrollInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_enroll'] = $v['total_enroll'];
            }

            // $certificateInfo = DB::table('emis_info')->select(DB::raw('UpazillaId as id,COUNT(*) as total_cer'))->where('DistrictId',$request->district_id)->groupBy('UpazillaId')->where('is_complete',1)->get();

            $qryStr = 'SELECT e.UpazillaId AS id,COUNT(DISTINCT ce.order_id) AS total_cer
                FROM course_enrollments ce
                JOIN (
                    SELECT course_enrollment_id,file_name FROM certificate_submit
                ) c ON c.course_enrollment_id = ce.id
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DistrictId,UpazillaId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND e.DistrictId = ' . $request->district_id . '
                AND c.file_name != ""
                GROUP BY e.UpazillaId';
            $certificateInfo = DB::select($qryStr);

            foreach ($certificateInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_certificate'] = $v['total_cer'];
            }

            $res = $divisions;
            $deo = DB::table('emis_deos')
                    ->select('emis_deos.*')
                    ->where('district_id',$request->district_id)
                    ->first();

            $data['data'] = $res;
            $data['deo'] = $deo;

            return response()->json($data);

        }else if($request->district_name){

            $nm = strtoupper($request->district_name);
            if($nm=='CHITTAGONG'){$nm='CHATTOGRAM';}
            if($nm=='COMILLA'){$nm='CUMILLA';}
            if($nm=='BOGRA'){$nm='BOGURA';}
            if($nm=='NETRAKONA'){$nm='NETROKONA';}

            $dis = DB::table('emis_distric')->where('name',$nm)->value('id');
            $div = DB::table('emis_distric')->where('name',$nm)->value('division_id');

            $division = DB::table('emis_upozila')->where('district_id',$dis)->get();
            $divisions = array();
            foreach ($division as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['id'] = $v['id'];
                $divisions[$v['id']]['name'] = $v['name'];
                $divisions[$v['id']]['name_bn'] = $v['name_bn'];
                $totalInfo = DB::table('emis_institutions')
                ->select(DB::raw('SUM(emis_institutions.teacher) as total_teacher'))
                    ->where('emis_institutions.upazila_id',$v['id'])
                ->first();

                $divisions[$v['id']]['total_teacher'] = $totalInfo->total_teacher;
            }

            // $enrollInfo = DB::table('emis_info')->select(DB::raw('UpazillaId as id,COUNT(*) as total_enroll'))->where('DistrictId',$dis)->groupBy('UpazillaId')->where('is_enroll',1)->get();

            $qryStr = 'SELECT e.UpazillaId AS id,COUNT(DISTINCT ce.order_id) AS total_enroll
                FROM course_enrollments ce
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DistrictId,UpazillaId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND e.DistrictId = ' . $dis . '
                GROUP BY e.UpazillaId';
            $enrollInfo = DB::select($qryStr);

            foreach ($enrollInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_enroll'] = $v['total_enroll'];
            }

            // $certificateInfo = DB::table('emis_info')->select(DB::raw('UpazillaId as id,COUNT(*) as total_cer'))->where('DistrictId',$dis)->groupBy('UpazillaId')->where('is_complete',1)->get();

            $qryStr = 'SELECT e.UpazillaId AS id,COUNT(DISTINCT ce.order_id) AS total_cer
                FROM course_enrollments ce
                JOIN (
                    SELECT course_enrollment_id,file_name FROM certificate_submit
                ) c ON c.course_enrollment_id = ce.id
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DistrictId,UpazillaId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND e.DistrictId = ' . $dis . '
                AND c.file_name != ""
                GROUP BY e.UpazillaId';
            $certificateInfo = DB::select($qryStr);

            foreach ($certificateInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_certificate'] = $v['total_cer'];
            }

            $res = $divisions;

            $deo = DB::table('emis_deos')
                    ->select('emis_deos.*')
                    ->where('district_id',$dis)
                    ->first();

            $data['data'] = $res;
            $data['district_id'] = $dis;
            $data['division_id'] = $div;
            $data['deo'] = $deo;

            return response()->json($data);

        }else if($request->upazila_id){

            $division = DB::table('emis_institutions')->where('upazila_id',$request->upazila_id)->get();


            $divisions = array();
            foreach ($division as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['id'] = $v['id'];
                $divisions[$v['id']]['name'] = $v['institution_name'];
                $totalInfo = DB::table('emis_institutions')
                ->select('emis_institutions.teacher as total_teacher')
                ->where('emis_institutions.upazila_id',$v['upazila_id'])
                ->first();

                $divisions[$v['id']]['total_teacher'] = $totalInfo->total_teacher;
            }

            // $enrollInfo = DB::table('emis_info')->select(DB::raw('emis_institutions.id as id,COUNT(DISTINCT(emis_info.user_id)) as total_enroll'))->where('emis_info.UpazillaId',$request->upazila_id)
            //     ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            //     ->groupBy('emis_institutions.EIIN')
            //     ->where('emis_info.is_enroll',1)->get();

            // return $divisions;

            $qryStr = 'SELECT ei.id AS id,COUNT(DISTINCT ce.order_id) AS total_enroll
                FROM course_enrollments ce
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT UpazillaId,user_id,EIIN FROM emis_info
                ) e ON e.user_id = o.user_id
                JOIN (
                    SELECT id,EIIN FROM emis_institutions
                ) ei ON ei.EIIN = e.EIIN
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND e.UpazillaId = ' . $request->upazila_id . '
                GROUP BY ei.EIIN';
            $enrollInfo = DB::select($qryStr);

            foreach ($enrollInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_enroll'] = $v['total_enroll'];
            }

            // $certificateInfo = DB::table('emis_info')->select(DB::raw('emis_institutions.id as id,COUNT(*) as total_certificate'))->where('emis_info.UpazillaId',$request->upazila_id)
            //     ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            //     ->groupBy('emis_institutions.EIIN')
            //     ->where('emis_info.is_complete',1)->get();   

            $qryStr = 'SELECT ei.id AS id,COUNT(DISTINCT ce.order_id) AS total_certificate
                FROM course_enrollments ce
                JOIN (
                    SELECT course_enrollment_id,file_name FROM certificate_submit
                ) c ON c.course_enrollment_id = ce.id
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT UpazillaId,user_id,EIIN FROM emis_info
                ) e ON e.user_id = o.user_id
                JOIN (
                    SELECT id,EIIN FROM emis_institutions
                ) ei ON ei.EIIN = e.EIIN
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND e.UpazillaId = ' . $request->upazila_id . '
                AND c.file_name != ""
                GROUP BY ei.EIIN';
            $certificateInfo = DB::select($qryStr);
                    
            foreach ($certificateInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_certificate'] = $v['total_certificate'];
            }

            $res = $divisions;


            // $res = DB::table('emis_distric')
            //     ->select('ei.id',DB::raw('COUNT(DISTINCT(o.user_id)) as total_enroll'),'ei.teacher as total_teacher','ei.institution_name as name',DB::raw('COUNT(DISTINCT(certificate_submit.course_enrollment_id)) as total_certificate'))
            //     ->leftJoin('emis_distric as ed','ed.id','emis_distric.id')
            //     ->leftjoin('emis_upozila as eu','eu.district_id','ed.id')
            //     ->leftJoin('emis_institutions as ei','ei.upazila_id','eu.id')
            //     ->leftjoin('emis_info as ein','ein.EIIN','ei.EIIN')
            //     ->leftJoin('orders as o','o.user_id','ein.user_id')
            //     ->leftjoin('course_enrollments as ce','ce.order_id','o.id')
            //     ->leftJoin('certificate_submit','certificate_submit.course_enrollment_id','ce.id')
            //     ->leftJoin('course_batches as cb','cb.id','ce.course_batch_id')
            //     ->where(function($q) use($RouteUser){
            //                 $q->where('cb.owner_id',$RouteUser)
            //                    ->orWhereNull('cb.owner_id');
            //             })
            //     ->where('ei.upazila_id',$request->upazila_id)
            //     ->groupBy('ei.id')
            //     ->get();

        }else if($request->institution_id){
            $user = config()->get('database.connections.my-account.database');

            $res = DB::table('emis_info')
            ->select('users.name','users.email','users.phone')
            ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            ->join($user.'.users','users.id','emis_info.user_id')
            ->where('emis_institutions.id',$request->institution_id)
            ->where('emis_info.is_enroll',1)
            ->get();
        }else{

            $district = DB::table('emis_distric')->get();
            $data = array(); $district_ids = [];
            foreach ($district as $v) {
                $v = (array)$v;
                $data[$v['id']]['id'] = $v['id'];
                $data[$v['id']]['name'] = $v['name'];
                $data[$v['id']]['name_bn'] = $v['name_bn'];
                array_push($district_ids, $v['id']);
            }

            $totalTeacherInfo = DB::table('emis_institutions')
                ->select(DB::raw('emis_upozila.district_id AS id,SUM(emis_institutions.teacher) as total_teacher'))
                ->join('emis_upozila','emis_institutions.upazila_id','emis_upozila.id')
                ->whereIn('emis_upozila.district_id',$district_ids)
                ->groupBy('emis_upozila.district_id')
                ->get();

            foreach ($totalTeacherInfo as $v) {
                $v = (array)$v;
                $data[$v['id']]['total_teacher'] = $v['total_teacher'];
            }

            // $enrollInfo = DB::table('emis_info')->select(DB::raw('DistrictId as id,COUNT(*) as total_enroll'))->groupBy('DistrictId')->where('is_enroll',1)->get();

            $qryStr = 'SELECT e.DistrictId AS id,COUNT(DISTINCT ce.order_id) AS total_enroll
                FROM course_enrollments ce
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DistrictId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id
                WHERE ce.course_batch_id = ' . $batch->id . '
                GROUP BY e.DistrictId';
            $enrollInfo = DB::select($qryStr);

            foreach ($enrollInfo as $v) {
                $v = (array)$v;
                $data[$v['id']]['total_enroll'] = $v['total_enroll'];
            }

            // $certificateInfo = DB::table('emis_info')->select(DB::raw('DistrictId as id,COUNT(*) as total_cer'))->groupBy('DistrictId')->where('is_complete',1)->get();

            $qryStr = 'SELECT e.DistrictId AS id,COUNT(DISTINCT ce.order_id) AS total_cer
                FROM course_enrollments ce
                JOIN (
                    SELECT course_enrollment_id,file_name FROM certificate_submit
                ) c ON c.course_enrollment_id = ce.id
                JOIN (
                    SELECT id,user_id FROM orders
                ) o ON o.id = ce.order_id
                JOIN (
                    SELECT DistrictId,user_id FROM emis_info
                ) e ON e.user_id = o.user_id                
                WHERE ce.course_batch_id = ' . $batch->id . '
                AND c.file_name != ""
                GROUP BY e.DistrictId';
            $certificateInfo = DB::select($qryStr);

            foreach ($certificateInfo as $v) {
                $v = (array)$v;
                $data[$v['id']]['total_certificate'] = $v['total_cer'];
            }

            $res = $data;


            // $res = DB::table('emis_distric')
            //         ->select('emis_distric.id',DB::raw('COUNT(DISTINCT(o.user_id)) as total_enroll'),DB::raw('SUM(ei.teacher) as total_teacher'),'emis_distric.name',DB::raw('COUNT(DISTINCT(certificate_submit.course_enrollment_id)) as total_certificate'))
            //         ->leftjoin('emis_upozila as eu','eu.district_id','emis_distric.id')
            //         ->leftJoin('emis_institutions as ei','ei.upazila_id','eu.id')
            //         ->leftjoin('emis_info as ein','ein.DistrictId','emis_distric.id')
            //         ->leftJoin('orders as o','o.user_id','ein.user_id')
            //         ->leftjoin('course_enrollments as ce','ce.order_id','o.id')
            //         ->leftJoin('certificate_submit','certificate_submit.course_enrollment_id','ce.id')
            //         ->leftJoin('course_batches as cb','cb.id','ce.course_batch_id')
            //         ->where('cb.owner_id',$RouteUser)
            //         ->orWhereNull('cb.owner_id')
            //         ->groupBy('emis_distric.id')
            //         ->get();
        }
        return $res;

        // $get
        // foreach($getAllDistrict as $key => $val){
            
        // }
        // return $res;
    }

    public function download_report(Request $request){
        
        $user = config()->get('database.connections.my-account.database');

        if($request->institution_id){
            $val = DB::table('emis_info')
            ->select('users.name','users.email','users.phone')
            ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            ->join($user.'.users','users.id','emis_info.user_id')
            ->where('emis_institutions.id',$request->institution_id)
            ->where('emis_info.is_enroll',1)
            ->when(Request()->edu_level_id!=0, function ($query, $request) {
                    return $query->where(function($q) use($request){
                        $q->where('emis_info.type',$request->edu_level_id);
                    });
            })
            ->get();
        }else if ($request->upazila_id){
            $val = DB::table('emis_info')
            ->select('users.name','users.email','users.phone')
            ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            ->join($user.'.users','users.id','emis_info.user_id')
            ->where('emis_info.UpazillaId',$request->upazila_id)
            ->when(Request()->edu_level_id!=0, function ($query, $request) {
                    return $query->where(function($q) use($request){
                        $q->where('emis_info.type',Request()->edu_level_id);
                    });
            })
            ->where('emis_info.is_enroll',1)
            ->get();
        }else if ($request->district_id){
            $val = DB::table('emis_info')
            ->select('users.name','users.email','users.phone')
            ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            ->join($user.'.users','users.id','emis_info.user_id')
            ->where('emis_info.DistrictId',$request->district_id)
            ->when(Request()->edu_level_id!=0, function ($query, $request) {
                    return $query->where(function($q) use($request){
                        $q->where('emis_info.type',Request()->edu_level_id);
                    });
            })
            ->where('emis_info.is_enroll',1)
            ->get();
        }else if($request->division_id){

            $val = DB::table('emis_info')
            ->select('users.name','users.email','users.phone')
            ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            ->join($user.'.users','users.id','emis_info.user_id')
            ->where('emis_info.DivisionId',$request->division_id)
            ->when(Request()->edu_level_id!=0, function ($query, $request) {
                    return $query->where(function($q) use($request){
                        $q->where('emis_info.type',Request()->edu_level_id);
                    });
            })
            ->where('emis_info.is_enroll',1)
            ->get();
        }else{
            $val = DB::table('emis_info')
            ->select('users.name','users.email','users.phone')
            ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            ->join($user.'.users','users.id','emis_info.user_id')
            ->when(Request()->edu_level_id!=0, function ($query, $request) {
                    return $query->where(function($q) use($request){
                        $q->where('emis_info.type',Request()->edu_level_id);
                    });
            })
            ->where('emis_info.is_enroll',1)
            ->get();
        }

        return $val;

        $filename = 'user-report-'.date('d-m-Y-His');
        Excel::create($filename,function($excel) use ($val){
            $excel->sheet('Sheet 1',function($sheet) use ($val){ 
                $sheet->fromArray($val);
            }); 
        })->export('xls');

    }



    /*public function all_data($username, Request $request){

        $RouteUserName = config('global.username');

        $RouteUser = InstitutionInfo::where('username',$RouteUserName)->first()->id;

        if($request->division_id){

            $division = DB::table('emis_distric')->where('division_id',$request->division_id)->get();
            $divisions = array();
            foreach ($division as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['id'] = $v['id'];
                $divisions[$v['id']]['name'] = $v['name'];
                $divisions[$v['id']]['name_bn'] = $v['name_bn'];
                $totalInfo = DB::table('emis_institutions')
                ->select(DB::raw('SUM(emis_institutions.teacher) as total_teacher'))->join('emis_upozila','emis_institutions.upazila_id','emis_upozila.id')
                ->where('emis_upozila.district_id',$v['id'])
                ->first();

                $divisions[$v['id']]['total_teacher'] = $totalInfo->total_teacher;
            }

            $enrollInfo = DB::table('emis_info')->select(DB::raw('DistrictId as id,COUNT(*) as total_enroll'))->where('DivisionId',$request->division_id)->groupBy('DistrictId')->where('is_enroll',1)->get();

            foreach ($enrollInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_enroll'] = $v['total_enroll'];
            }

            $certificateInfo = DB::table('emis_info')->select(DB::raw('DistrictId as id,COUNT(*) as total_cer'))->where('DivisionId',$request->division_id)->groupBy('DistrictId')->where('is_complete',1)->get();
            foreach ($certificateInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_certificate'] = $v['total_cer'];
            }

            $res = $divisions;

        }else if($request->district_id){

            $division = DB::table('emis_upozila')->where('district_id',$request->district_id)->get();
            $divisions = array();
            foreach ($division as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['id'] = $v['id'];
                $divisions[$v['id']]['name'] = $v['name'];
                $divisions[$v['id']]['name_bn'] = $v['name_bn'];
                $totalInfo = DB::table('emis_institutions')
                ->select(DB::raw('SUM(emis_institutions.teacher) as total_teacher'))
                    ->where('emis_institutions.upazila_id',$v['id'])
                ->first();

                $divisions[$v['id']]['total_teacher'] = $totalInfo->total_teacher;
            }

            $enrollInfo = DB::table('emis_info')->select(DB::raw('UpazillaId as id,COUNT(*) as total_enroll'))->where('DistrictId',$request->district_id)->groupBy('UpazillaId')->where('is_enroll',1)->get();

            foreach ($enrollInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_enroll'] = $v['total_enroll'];
            }

            $certificateInfo = DB::table('emis_info')->select(DB::raw('UpazillaId as id,COUNT(*) as total_cer'))->where('DistrictId',$request->district_id)->groupBy('UpazillaId')->where('is_complete',1)->get();
            foreach ($certificateInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_certificate'] = $v['total_cer'];
            }

            $res = $divisions;
            $deo = DB::table('emis_deos')
                    ->select('emis_deos.*')
                    ->where('district_id',$request->district_id)
                    ->first();

            $data['data'] = $res;
            $data['deo'] = $deo;

            return response()->json($data);

        }else if($request->district_name){

            $nm = strtoupper($request->district_name);
            if($nm=='CHITTAGONG'){$nm='CHATTOGRAM';}
            if($nm=='COMILLA'){$nm='CUMILLA';}
            if($nm=='BOGRA'){$nm='BOGURA';}
            if($nm=='NETRAKONA'){$nm='NETROKONA';}

            $disRes = DB::table('emis_distric')->where('name',$nm)->select('id','division_id')->first();
            $dis = $disRes->id;
            $div = $disRes->division_id;

            // TOTAL INFO             
            $qry = 'SELECT eu.id,eu.name,eu.name_bn,tt.total_teacher,te.total_enroll,tc.total_certificate
                FROM emis_upozila AS eu
                JOIN (
                    SELECT UpazillaId,COUNT(id) AS total_teacher FROM emis_info
                    WHERE DistrictId = '.$dis.'
                    GROUP BY UpazillaId
                ) tt ON eu.id=tt.UpazillaId
                JOIN (
                    SELECT UpazillaId,COUNT(id) AS total_enroll FROM emis_info
                    WHERE is_enroll > 0 AND DistrictId = '.$dis.'
                    GROUP BY UpazillaId
                ) te ON eu.id=te.UpazillaId
                JOIN (
                    SELECT UpazillaId,COUNT(id) AS total_certificate FROM emis_info
                    WHERE is_complete > 0 AND DistrictId = '.$dis.'
                    GROUP BY UpazillaId
                ) tc ON eu.id=tc.UpazillaId';
            
            $res = DB::select($qry);
                        
            // DEO INFO
            $deo = DB::table('emis_deos')
                    ->select('emis_deos.*')
                    ->where('district_id',$dis)
                    ->first();

            $data['data'] = $res;
            $data['district_id'] = $dis;
            $data['division_id'] = $div;
            $data['deo'] = $deo;

            return response()->json($data);

        }else if($request->upazila_id){

            $division = DB::table('emis_institutions')->where('upazila_id',$request->upazila_id)->get();


            $divisions = array();
            foreach ($division as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['id'] = $v['id'];
                $divisions[$v['id']]['name'] = $v['institution_name'];
                $totalInfo = DB::table('emis_institutions')
                ->select('emis_institutions.teacher as total_teacher')
                ->where('emis_institutions.upazila_id',$v['upazila_id'])
                ->first();

                $divisions[$v['id']]['total_teacher'] = $totalInfo->total_teacher;
            }

            $enrollInfo = DB::table('emis_info')->select(DB::raw('emis_institutions.id as id,COUNT(DISTINCT(emis_info.user_id)) as total_enroll'))->where('emis_info.UpazillaId',$request->upazila_id)
                ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
                ->groupBy('emis_institutions.EIIN')
                ->where('emis_info.is_enroll',1)->get();



            foreach ($enrollInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_enroll'] = $v['total_enroll'];
            }

            $certificateInfo = DB::table('emis_info')->select(DB::raw('emis_institutions.id as id,COUNT(*) as total_certificate'))->where('emis_info.UpazillaId',$request->upazila_id)
                ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
                ->groupBy('emis_institutions.EIIN')
                ->where('emis_info.is_complete',1)->get();   
                    
            foreach ($certificateInfo as $v) {
                $v = (array)$v;
                $divisions[$v['id']]['total_certificate'] = $v['total_certificate'];
            }

            $res = $divisions;


            // $res = DB::table('emis_distric')
            //     ->select('ei.id',DB::raw('COUNT(DISTINCT(o.user_id)) as total_enroll'),'ei.teacher as total_teacher','ei.institution_name as name',DB::raw('COUNT(DISTINCT(certificate_submit.course_enrollment_id)) as total_certificate'))
            //     ->leftJoin('emis_distric as ed','ed.id','emis_distric.id')
            //     ->leftjoin('emis_upozila as eu','eu.district_id','ed.id')
            //     ->leftJoin('emis_institutions as ei','ei.upazila_id','eu.id')
            //     ->leftjoin('emis_info as ein','ein.EIIN','ei.EIIN')
            //     ->leftJoin('orders as o','o.user_id','ein.user_id')
            //     ->leftjoin('course_enrollments as ce','ce.order_id','o.id')
            //     ->leftJoin('certificate_submit','certificate_submit.course_enrollment_id','ce.id')
            //     ->leftJoin('course_batches as cb','cb.id','ce.course_batch_id')
            //     ->where(function($q) use($RouteUser){
            //                 $q->where('cb.owner_id',$RouteUser)
            //                    ->orWhereNull('cb.owner_id');
            //             })
            //     ->where('ei.upazila_id',$request->upazila_id)
            //     ->groupBy('ei.id')
            //     ->get();

        }else if($request->institution_id){

            // $res = DB::table('course_enrollments as ce')
            //         ->select('u.name','u.email','u.phone')
            //         ->join('course_batches as cb','cb.id','ce.course_batch_id')
            //         ->join('orders as o','o.id','ce.order_id')
            //         ->join('users as u','u.id','o.user_id')
            //         ->join('emis_info as ei','ei.user_id','u.id')
            //         ->join('emis_institutions as ein','ein.EIIN','ei.EIIN')
            //         ->where('ein.id',$request->institution_id)
            //         ->where('cb.owner_id',$RouteUser)
            //         ->where('ce.course_completeness','100.00')
            //         ->get();

            $res = DB::table('emis_info')
            ->select('users.name','users.email','users.phone')
            ->join('emis_institutions','emis_institutions.EIIN','emis_info.EIIN')
            ->join('users','users.id','emis_info.user_id')
            ->where('emis_institutions.id',$request->institution_id)
            ->where('emis_info.is_enroll',1)
            ->get();
        }else{

            $qry = 'SELECT ed.id,ed.name,ed.name_bn,tt.total_teacher,te.total_enroll,tc.total_certificate
                FROM emis_distric AS ed
                JOIN (
                    SELECT DistrictId,COUNT(id) AS total_teacher FROM emis_info
                    GROUP BY DistrictId
                ) tt ON ed.id=tt.DistrictId
                JOIN (
                    SELECT DistrictId,COUNT(id) AS total_enroll FROM emis_info
                    WHERE is_enroll > 0
                    GROUP BY DistrictId
                ) te ON ed.id=te.DistrictId
                JOIN (
                    SELECT DistrictId,COUNT(id) AS total_certificate FROM emis_info
                    WHERE is_complete > 0
                    GROUP BY DistrictId
                ) tc ON ed.id=tc.DistrictId';
            
            $data = DB::select($qry);

            return response()->json($data);
        }
        return $res;
    }*/

    function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];
            
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
             
        for ($currentDate = $startDate; $currentDate <= $endDate; 
                                        $currentDate += (86400)) {
                                                
            $date = date('Y-m-d', $currentDate);
            $rangArray[] = $date;
        }
  
        return $rangArray;
    }

    // public function trendline(){
    //     $RouteUserName = config('global.username');

    //     $RouteUser = InstitutionInfo::where('username',$RouteUserName)->first()->id;

    //     // $batch =    CourseBatch::where('owner_id',$RouteUser)
    //     //             ->orderBy('start_date','DESC')
    //     //             ->first();
    //     $batch = (object) [];
    //     $batch->id = 848;

    //     //$dates =  $this->getBetweenDates($batch->start_date,date('Y-m-d'));
    //      $dates =  $this->getBetweenDates('2022-09-01',date('Y-m-d'));

    //     $data = [];
    //     $total = DB::table('emis_institutions')->value(DB::raw('SUM(teacher)'));
    //                 $e= 0; $c = 0;
    //     foreach ($dates as $key => $value) {


    //         $enrolled = DB::table('course_enrollments as ce')
    //                         // ->join('course_batches as cb','cb.id','ce.course_batch_id')
    //                         ->whereDate('ce.created_at',$value)
    //                         ->where('ce.course_batch_id',$batch->id)
    //                         ->value(DB::raw('COUNT(ce.id)'));

    //         $certificate = DB::table('certificate_submit as cs')
    //                     ->join('course_enrollments as ce','ce.id','cs.course_enrollment_id')
    //                     // ->join('course_batches as cb','cb.id','ce.course_batch_id')
    //                     ->where('ce.course_batch_id',$batch->id)
    //                     ->whereDate('cs.created_at',$value)
    //                     ->value(DB::raw('COUNT(cs.id)'));

    //         $e+=$enrolled;
    //         $c+=$certificate;

    //         $data[$key]['date'] = $value;
    //         $data[$key]['total'] = $total;
    //         $data[$key]['enrolled'] = $e;
    //         $data[$key]['certificate'] = $c;

    //     }

    //     return response()->json($data);
    // }

    public function trendline(Request $request){
        // $RouteUserName = config('global.username');

        // $RouteUser = InstitutionInfo::where('username',$RouteUserName)->first()->id;
        $batch = (object) [];
        $batch->id = $request->batch_id?$request->batch_id:848;
        // $batch =    CourseBatch::where('owner_id',$RouteUser)
        //             ->orderBy('start_date','DESC')
        //             ->first();

        $qry = 'SELECT t.info_date AS `date`,
                e.total_teacher,
                t.total_enrolled,
                @running_enrolled:=@running_enrolled + t.total_enrolled AS `enrolled`,
                t.total_certificates,
                @running_certificates:=@running_certificates + t.total_certificates AS `certificate`
                FROM
                (
                    SELECT DATE(ce.created_at) AS info_date, 
                        COUNT(DISTINCT ce.order_id) AS total_enrolled, 
                        COUNT(cs.id) AS total_certificates
                    FROM course_enrollments AS ce
                    LEFT JOIN certificate_submit AS cs ON cs.course_enrollment_id = ce.id
                    WHERE ce.course_batch_id= ' . $batch->id . '
                    AND DATE(ce.created_at) <= CURDATE()
                    GROUP BY DATE(ce.created_at)
                ) t
                JOIN (SELECT @running_enrolled:=0, @running_certificates:=0) r
                JOIN (SELECT SUM(teacher) AS total_teacher FROM emis_institutions) e
                ORDER BY t.info_date ASC';
        
        $data = DB::select($qry);

        return response()->json($data);
    }

    public function district_data(){

        $RouteUserName = config('global.username');

        $RouteUser = InstitutionInfo::where('username',$RouteUserName)->first()->id;

        if(Request()->district){
            $enroll = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('emis_info as ei','ei.user_id','o.user_id')
                ->join('emis_distric as ed','ed.id','ei.DistrictId')
                ->where('cb.owner_id',$RouteUser)
                ->where('ed.name',Request()->district)
                ->value(DB::raw('COUNT(course_enrollments.id)'));

            $completeness = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('emis_info as ei','ei.user_id','o.user_id')
                ->join('emis_distric as ed','ed.id','ei.DistrictId')
                ->where('cb.owner_id',$RouteUser)
                ->where('ed.name',Request()->district)
                ->where('course_enrollments.course_completeness','100.00')
                ->value(DB::raw('COUNT(course_enrollments.id)'));

            $certificate = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('emis_info as ei','ei.user_id','o.user_id')
                ->join('certificate_submit as cs','cs.course_enrollment_id','course_enrollments.id')
                ->join('emis_distric as ed','ed.id','ei.DistrictId')
                ->where('cb.owner_id',$RouteUser)
                ->where('ed.name',Request()->district)
                ->value(DB::raw('COUNT(course_enrollments.id)'));

            $institution = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('emis_info as ei','ei.user_id','o.user_id')
                ->join('emis_distric as ed','ed.id','ei.DistrictId')
                ->where('cb.owner_id',$RouteUser)
                ->where('ed.name',Request()->district)
                ->value(DB::raw('COUNT(ei.id)'));

            return response()->json([
                'district' => Request()->district,
                'enroll' => $enroll,
                'completeness' => $completeness,
                'certificate' => $certificate,
                'institution' => $institution]);

        }
    }

    public function compare($username, $district_id){

        $RouteUserName = config('global.username');

        $RouteUser = InstitutionInfo::where('username',$RouteUserName)->first()->id;

        $type = Request()->type;
        if($type=='profession'){

            $res = CourseEnrollment::select('profession.title',DB::raw('COUNT(DISTINCT(users.id)) as users'))
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('orders','orders.id','course_enrollments.order_id')
                ->join('users','users.id','orders.user_id')
                ->join('user_infos','user_infos.user_id','users.id')
                ->join('profession','profession.id','user_infos.profession')
                ->join('geo_districts','geo_districts.id','user_infos.district_id')
                ->where('geo_districts.id',$district_id)
                ->where('cb.owner_id',$RouteUser)
                ->groupBy('profession.id')
                ->get();

        }elseif($type=='gender'){

            $res = CourseEnrollment::select(DB::raw('(CASE WHEN user_infos.gender =1 THEN "MALE" When user_infos.gender=2 THEN "FEMALE" When user_infos.gender=3 THEN "OTHERS" ELSE "NOT GIVEN" END) AS title'),DB::raw('COUNT(DISTINCT(users.id)) as users'))
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('orders','orders.id','course_enrollments.order_id')
                ->join('users','users.id','orders.user_id')
                ->join('user_infos','user_infos.user_id','users.id')
                ->join('geo_districts','geo_districts.id','user_infos.district_id')
                ->where('geo_districts.id',$district_id)
                ->where('cb.owner_id',$RouteUser)
                ->groupBy('user_infos.gender')
                ->get();
        }

        return $res;
    }

    public function districtWiseLearner(Request $request)
    {
        $RouteUser = config()->get('global.owner_id');

        $other_credential = null; $start = null; $end = null; $batch_id = null; $profession_id = null; $gender = null;
        // if(config('global.role')->id!=1){

        //     $other_credential = $RouteUser;

        //     $start      = (isset($request['start'])&& !is_null($request['start']))?date('Y-m-d 00:00:00', strtotime($request['start'])):null;
        //     $end        = isset($request['end'])?date('Y-m-d 23:59:59', strtotime($request['end'])):null;
        //     $batch_id        = isset($request['batch_id'])?$request->batch_id:null;
        //     $profession_id        = isset($request['profession_id'])?$request->profession_id:null;
        //     $gender        = isset($request['gender'])?$request->gender:null;
        // }
        //$header_filter = ($request['headerFilter'])?$request['headerFilter']:null;
        if(isset($request['filterDivision'])){
            if($request['headerFilter'] == 'learner'){

                $total_data = UserInfo::select(array(
                    DB::raw("geo_districts.district_name_eng as name"),
                    DB::raw("geo_districts.id"),
                    DB::raw("count(user_infos.district_id) AS total"),
                    DB::raw("count(DISTINCT(user_infos.user_id)) AS users")
                ))
                ->when($other_credential, function($q) use($other_credential){
                    return $q->join('users', 'users.id', 'user_infos.user_id')
                        ->join('orders', 'users.id', 'orders.user_id')
                        ->join('course_enrollments', 'course_enrollments.order_id', 'orders.id')
                        ->join('course_batches', function($join) use($other_credential){
                            $join->on('course_batches.id', 'course_enrollments.course_batch_id')
                            ->where('course_batches.owner_id', $other_credential);
                        })
                        ->distinct('orders.user_id');
                })
                ->rightJoin('geo_districts', 'user_infos.district_id', 'geo_districts.id')
                ->where('geo_districts.geo_division_id',$request['filterDivision'])
                ->groupBy('geo_districts.id')
                ->orderBy('geo_districts.id')
                ->when($start, function($q) use($start,$end){
                    return $q->whereBetween('user_infos.created_at' , [$start, $end]);
                })
                ->when($batch_id, function($q) use($batch_id){
                    return $q->where('course_enrollments.course_batch_id' ,$batch_id);
                })
                ->when($profession_id, function($q) use($profession_id){
                    return $q->where('user_infos.profession' ,$profession_id);
                })
                ->when($gender, function($q) use($gender){
                    return $q->where('user_infos.gender' ,$gender);
                })
                ->get();
            }else{
                $total_data = InstitutionInfo::select(array(
                    DB::raw("geo_districts.district_name_eng as name"),
                    DB::raw("geo_districts.id"),
                    DB::raw("count(user_infos.district_id) AS total"),
                    DB::raw("count(DISTINCT(user_infos.user_id)) AS users")
                ))
                ->join('users', 'users.email', 'institution_infos.email')
                ->join('user_infos','user_infos.user_id','users.id')
                ->rightJoin('geo_districts', 'user_infos.district_id', 'geo_districts.id')
                ->where('geo_districts.geo_division_id',$request['filterDivision'])
                ->groupBy('geo_districts.id')
                ->orderBy('geo_districts.id')
                ->when($start, function($q) use($start,$end){return $q->whereBetween('user_infos.created_at' , [$start, $end]);})
                ->when($batch_id, function($q) use($batch_id){
                    return $q->where('course_enrollments.course_batch_id' ,$batch_id);
                })
                ->when($profession_id, function($q) use($profession_id){
                    return $q->where('user_infos.profession' ,$profession_id);
                })
                ->when($gender, function($q) use($gender){
                    return $q->where('user_infos.gender' ,$gender);
                })
                ->get();
            }

            if(sizeof($total_data)==0){
                $total_data = DB::table('geo_districts')
                ->select('geo_districts.district_name_eng as name',DB::raw("0 AS total"))
                ->where('geo_districts.geo_division_id',$request['filterDivision'])
                ->orderBy('geo_districts.id')
                ->get();
            }
        }else{
            if($request['headerFilter'] == 'learner'){

                $total_data = UserInfo::select(array(
                    DB::raw("geo_divisions.division_name_eng as name"),
                    DB::raw("count(user_infos.division_id) AS total"),
                    DB::raw("count(DISTINCT(user_infos.user_id)) AS users")
                ))
                ->when($other_credential, function($q) use($other_credential){
                    return $q->join('users', 'users.id', 'user_infos.user_id')
                        ->join('orders', 'users.id', 'orders.user_id')
                        ->join('course_enrollments', 'course_enrollments.order_id', 'orders.id')
                        ->join('course_batches', function($join) use($other_credential){
                            $join->on('course_batches.id', 'course_enrollments.course_batch_id')
                            ->where('course_batches.owner_id', $other_credential);
                        })
                        ->distinct('orders.user_id');
                })
                ->rightJoin('geo_divisions', 'user_infos.division_id', 'geo_divisions.id')
                ->groupBy('geo_divisions.id')
                ->orderBy('geo_divisions.id')
                ->when($start, function($q) use($start,$end){return $q->whereBetween('user_infos.created_at' , [$start, $end]);})
                ->when($batch_id, function($q) use($batch_id){
                    return $q->where('course_enrollments.course_batch_id' ,$batch_id);
                })
                ->when($profession_id, function($q) use($profession_id){
                    return $q->where('user_infos.profession' ,$profession_id);
                })
                ->when($gender, function($q) use($gender){
                    return $q->where('user_infos.gender' ,$gender);
                })
                ->get();


            }else{
                $total_data = InstitutionInfo::select(array(
                    DB::raw("geo_divisions.division_name_eng as name"),
                    DB::raw("count(user_infos.division_id) AS total"),
                    DB::raw("count(DISTINCT(user_infos.user_id)) AS users")
                ))
                ->join('users', 'users.email', 'institution_infos.email')
                ->join('user_infos', 'user_infos.user_id', 'users.id')
                ->rightJoin('geo_divisions', 'user_infos.division_id', 'geo_divisions.id')
                ->groupBy('geo_divisions.id')
                ->orderBy('geo_divisions.id')
                ->when($start, function($q) use($start,$end){return $q->whereBetween('user_infos.created_at' , [$start, $end]);})
                ->when($batch_id, function($q) use($batch_id){
                    return $q->where('course_enrollments.course_batch_id' ,$batch_id);
                })
                ->when($profession_id, function($q) use($profession_id){
                    return $q->where('user_infos.profession' ,$profession_id);
                })
                ->when($gender, function($q) use($gender){
                    return $q->where('user_infos.gender' ,$gender);
                })
                ->get();
            }

            if(sizeof($total_data)==0){
                $total_data = DB::table('geo_divisions')
                ->select('geo_divisions.division_name_eng as name',DB::raw("0 AS total"))
                ->orderBy('geo_divisions.id')
                ->get();
            }
        }
        

        return response()->json($total_data);
    }
}
