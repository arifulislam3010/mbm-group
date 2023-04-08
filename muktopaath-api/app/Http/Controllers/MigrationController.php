<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Models\Course\Completeness;
class MigrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function contentMigrateDuration($skip){
        $next_id = $skip+100;
       $learning_contents = DB::connection('content-bank')->table('learning_contents')->select('id','duration')->whereNotNull('duration')->skip($skip)->take(100)->get();
        // return $learning_contents;
        if(count($learning_contents)>0){
            foreach ($learning_contents as $sku) {
                    $duration = $sku->duration;
                    if(is_numeric($duration)){
                        $duration_main  = gmdate('H:i:s',$duration);
                       
                    }else{
                        $duration_main =  $duration;
                    }
                    // return $duration_main;

                    DB::connection('content-bank')->table('learning_contents')
                    ->where('id', $sku->id)
                    ->update(array('duration' => $duration_main)); 
                
            }
            $url = 'https://v3api.muktopaath.gov.bd/content-migrate-duration/'.$next_id;
            echo 'Wait 1 seconds...'.$next_id;
            echo '<meta http-equiv="refresh" content="5;URL='.$url.'" />';
            exit; 
        }else{
            
            echo 'Done';
            exit; 
        }
    }
    public  function contentMigrate($skip){
        $next_id = $skip+50;
        $learning_contents = DB::connection('content-bank')->table('learning_contents')->select('id','content_url')->Where('content_url', 'like', '%<iframe%')->skip($skip)->take(50)->get();
        // return $learning_contents;
        if(count($learning_contents)>0){
            foreach ($learning_contents as $sku) {
                $content_url = $sku->content_url;
                preg_match_all("/<iframe[^>]*src=[\"|']([^'\"]+)[\"|'][^>]*>/i", $content_url, $output );
                $return = array();
                if( isset( $output[1][0] ) ) {
                    if(isset($output[1][0])){
                        DB::connection('content-bank')->table('learning_contents')
                        ->where('id', $sku->id)
                        ->update(array('content_url' => $output[1][0])); 
                    }
                }
            }
            $url = 'https://v3api.muktopaath.gov.bd/content-migrate/'.$next_id;
            echo 'Wait 1 seconds...'.$next_id;
            echo '<meta http-equiv="refresh" content="5;URL='.$url.'" />';
            exit; 
        }else{
            
            echo 'Done';
            exit; 
        }
        
    }

    public function courseCompleteness ($skip) {
        // $data = DB::connection('course')->select('Select max(enroll_id) as enroll_id from completeness');
        // $enroll_id = $data[0]->enroll_id;
        // if(empty($enroll_id))
        //     $enroll_id = 0;

        $next_id = $skip+100;
        $CourseEnrollments = CourseEnrollment::skip($skip)->take(100)->get();
        // $CourseEnrollments = CourseEnrollment::where('id','>',$skip)->limit(100)->get();

        foreach($CourseEnrollments as $CourseEnrollment){
            if($CourseEnrollment->journey_status){
                try {
                    $course_journey = json_decode($CourseEnrollment->journey_status);
                    $course_journey_uc = count($course_journey);
                    $unit_journey_values=0;
                    $unitQuery = 'INSERT INTO `completeness` (`id`,`user_id`,`syllabus_id`,`completeness`,`enroll_id`,`created_at`,`updated_at`) VALUES';
                    for($i=0;$i<$course_journey_uc;$i++) {
                        try {
                            $unit_completeness = 0;
                            $unit = Syllabus::where('course_batch_id', $CourseEnrollment->course_batch_id)->where('order_number', $i + 1)->first();
                            if ($unit) {
                                $lessonQuery = 'INSERT INTO `completeness` (`id`,`user_id`,`syllabus_id`,`completeness`,`enroll_id`,`created_at`,`updated_at`) VALUES';
                                $course_journey_c = count($course_journey[$i]);
                                $lesson_values = 0;
                                for ($j = 0; $j < $course_journey_c; $j++) {

                                    $completeness = $course_journey[$i][$j]->completeness;
                                    $unit_completeness = $unit_completeness + $completeness;
                                    $lesson = Syllabus::where('parent_id', $unit->id)->where('order_number', $j + 1)->first();
                                    if ($lesson) {
                                        $lesson_values = $lesson_values + 1;
                                        $lessonQuery .= '(null,';
                                        $lessonQuery .= $CourseEnrollment->orderId->user_id . ',';
                                        $lessonQuery .= $lesson->id . ',';
                                        $lessonQuery .= $completeness . ',';
                                        $lessonQuery .= $CourseEnrollment->id . ',';
                                        $lessonQuery .= '"' . $CourseEnrollment->updated_at . '",';
                                        $lessonQuery .= '"' . $CourseEnrollment->updated_at . '"),';

                                        // Completeness::updateOrCreate(
                                        //     ['syllabus_id' => $lesson->id,'user_id'=>$CourseEnrollment->orderId->user_id],
                                        //     ['syllabus_id' => $lesson->id, 'completeness' => $completeness,'user_id'=>$CourseEnrollment->orderId->user_id]

                                        // );
                                    }
                                }

                                $lessonQuery = substr($lessonQuery, 0, -1);
                                $lessonQuery .= ';';
                                if ($course_journey_c > 0 && $lesson_values > 0) {
                                    DB::connection('course')->update($lessonQuery);
                                }


                                $unitCount = count($course_journey[$i]);
                                $uc = $unit_completeness / ($unitCount != 0 ? $unitCount : 1);

                                $unit_journey_values = $unit_journey_values + 1;
                                $unitQuery .= '(null,';
                                $unitQuery .= $CourseEnrollment->orderId->user_id . ',';
                                $unitQuery .= $unit->id . ',';
                                $unitQuery .= $uc . ',';
                                $unitQuery .= $CourseEnrollment->id . ',';
                                $unitQuery .= '"' . $CourseEnrollment->updated_at . '",';
                                $unitQuery .= '"' . $CourseEnrollment->updated_at . '"),';

                                // Completeness::updateOrCreate(
                                //     ['syllabus_id' => $unit->id,'user_id'=>$CourseEnrollment->orderId->user_id],
                                //     ['syllabus_id' => $unit->id, 'completeness' => $uc,'user_id'=>$CourseEnrollment->orderId->user_id]

                                // );
                            }
                        }catch (\Exception $ex){
                            
                        }
                    }
                    // return $unitQuery;
                    $unitQuery = substr($unitQuery, 0, -1);
                    $unitQuery.=';';
                    if($course_journey_uc>0 && $unit_journey_values>0){
                        DB::connection('course')->update($unitQuery);
                    }  
                }catch (\Exception $ex){
                            
                } 
            }
        }
        //$url = 'https://v3api.muktopaath.gov.bd/course-completeness-migrate/'.$next_id;
        //echo 'Wait 1 seconds...'.$next_id;
        $url = 'https://v3api.muktopaath.gov.bd/course-completeness-migrate/'.$next_id;
        echo 'Wait 1 seconds...'.$next_id;
        echo '<meta http-equiv="refresh" content="0;URL='.$url.'" />';
        exit; 
        
    }

    public function courseCompleteness2 ($from,$take) {
        $next_id = $from+$take;
        $CourseEnrollments = CourseEnrollment::with('courseBatch')->where('course_completeness',100)->skip($from)->take($take)->get();
        $FinalQuery='INSERT INTO `completeness` (`id`,`user_id`,`syllabus_id`,`completeness`,`enroll_id`,`created_at`,`updated_at`) VALUES';
        // try {
        $minimum_data = 0;
        foreach($CourseEnrollments as $CourseEnrollment){
            if(isset($CourseEnrollment->courseBatch) && isset($CourseEnrollment->courseBatch->lessons) && $CourseEnrollment->user_id!=null){
                $minimum_data= $minimum_data+1;
                // try {
                    $course_journey = (array) json_decode($CourseEnrollment->journey_status,true);
                    $unit_journey_values=0;
                    $i=0;
                    foreach($CourseEnrollment->courseBatch->lessons as $unit) {
                           if($CourseEnrollment->course_completeness==100){
                            $unit_completeness = 0;
                            if ($unit && isset($unit->lessons) && count($unit->lessons)>0) {
                                
                               $lesson_values = 0;
                                $j=0;
                                foreach ($unit->lessons as $lesson) {
                                    if ($lesson) {
                                        $lesson_values = $lesson_values + 1;
                                        $FinalQuery .= '(null,';
                                        $FinalQuery .= $CourseEnrollment->user_id . ',';
                                        $FinalQuery .= $lesson->id . ',';
                                        $FinalQuery .= '100,';
                                        $FinalQuery .= $CourseEnrollment->id . ',';
                                        $FinalQuery .= '"' . $CourseEnrollment->updated_at . '",';
                                        $FinalQuery .= '"' . $CourseEnrollment->updated_at . '"),';
                                    }
                                    $j++;
                                }

                               

                                $unit_journey_values = $unit_journey_values + 1;
                                $FinalQuery .= '(null,';
                                $FinalQuery .= $CourseEnrollment->user_id . ',';
                                $FinalQuery .= $unit->id . ',';
                                $FinalQuery .= '100,';
                                $FinalQuery .= $CourseEnrollment->id . ',';
                                $FinalQuery .= '"' . $CourseEnrollment->updated_at . '",';
                                $FinalQuery .= '"' . $CourseEnrollment->updated_at . '"),';
                            }
                            $i++;
                           }else{
                            $unit_completeness = 0;
                            if ($unit && isset($unit->lessons) && count($unit->lessons)>0) {
                                
                               $lesson_values = 0;
                                $j=0;
                                foreach ($unit->lessons as $lesson) {
                                    $completeness = (isset($course_journey[$i]) && isset($course_journey[$i][$j]) && isset($course_journey[$i][$j]['completeness']))?$course_journey[$i][$j]['completeness']:0;
                                    $unit_completeness = $unit_completeness + $completeness;
                                    if ($lesson) {
                                        $lesson_values = $lesson_values + 1;
                                        $FinalQuery .= '(null,';
                                        $FinalQuery .= $CourseEnrollment->user_id . ',';
                                        $FinalQuery .= $lesson->id . ',';
                                        $FinalQuery .= $CourseEnrollment->course_completeness==100?100:$completeness . ',';
                                        $FinalQuery .= $CourseEnrollment->id . ',';
                                        $FinalQuery .= '"' . $CourseEnrollment->updated_at . '",';
                                        $FinalQuery .= '"' . $CourseEnrollment->updated_at . '"),';
                                    }
                                    $j++;
                                }

                                // $FinalQuery = substr($lessonQuery, 0, -1);
                                // $lessonQuery .= ' ON DUPLICATE KEY UPDATE completeness=VALUES(completeness);';
                                // $FinalQuery.=$lessonQuery;
                                // if ($lesson_values > 0) {
                                //     DB::connection('course')->select($lessonQuery);
                                // }

                                $uc = $unit_completeness / ($lesson_values != 0 ? $lesson_values : 1);

                                $unit_journey_values = $unit_journey_values + 1;
                                $FinalQuery .= '(null,';
                                $FinalQuery .= $CourseEnrollment->user_id . ',';
                                $FinalQuery .= $unit->id . ',';
                                $FinalQuery .= $CourseEnrollment->course_completeness==100?100:$uc . ',';
                                $FinalQuery .= $CourseEnrollment->id . ',';
                                $FinalQuery .= '"' . $CourseEnrollment->updated_at . '",';
                                $FinalQuery .= '"' . $CourseEnrollment->updated_at . '"),';
                            }
                            $i++;
                           }
                        // try {
                            
                        // }catch (\Exception $ex){
                            
                        // }
                    }
                    // return $unitQuery;
                    // $unitQuery = substr($unitQuery, 0, -1);
                    // $unitQuery.=' ON DUPLICATE KEY UPDATE completeness=VALUES(completeness);';
                    // $FinalQuery.=$unitQuery;
                    // if($unit_journey_values>0){
                    //     DB::connection('course')->select($unitQuery);
                    // }  
                // }catch (\Exception $ex){
                            
                // } 
            }
        }
        if($minimum_data>0){
            $FinalQuery = substr($FinalQuery, 0, -1);
            $FinalQuery.=' ON DUPLICATE KEY UPDATE completeness=VALUES(completeness);';
            // return $FinalQuery;
            
            DB::connection('course')->update($FinalQuery);
        }
        
            
        // }catch (\Exception $ex){
                                
        // } 
        $url = 'https://v3api.muktopaath.gov.bd/course-completeness-migrate2/'.$next_id.'/'.$take;
        echo 'Wait 1 seconds...'.$next_id;
        echo '<meta http-equiv="refresh" content="0;URL='.$url.'" />';
        exit; 
        
    }

    public function courseCompleteness3 ($skip) {
        // $data = DB::connection('course')->select('Select max(enroll_id) as enroll_id from completeness');
        // $enroll_id = $data[0]->enroll_id;
        // if(empty($enroll_id))
        //     $enroll_id = 0;

        $next_id = $skip+100;
        $CourseEnrollments = CourseEnrollment::skip($skip)->take(100)->get();
        // $CourseEnrollments = CourseEnrollment::where('id','>',$skip)->limit(100)->get();

        foreach($CourseEnrollments as $CourseEnrollment){
            if($CourseEnrollment->journey_status){
                try {
                    $course_journey = json_decode($CourseEnrollment->journey_status);
                    $course_journey_uc = count($course_journey);
                    $unit_journey_values=0;
                    $unitQuery = 'INSERT INTO `completeness` (`id`,`user_id`,`syllabus_id`,`completeness`,`enroll_id`,`created_at`,`updated_at`) VALUES';
                    for($i=0;$i<$course_journey_uc;$i++) {
                        try {
                            $unit_completeness = 0;
                            $unit = Syllabus::where('course_batch_id', $CourseEnrollment->course_batch_id)->where('order_number', $i + 1)->first();
                            if ($unit) {
                                $lessonQuery = 'INSERT INTO `completeness` (`id`,`user_id`,`syllabus_id`,`completeness`,`enroll_id`,`created_at`,`updated_at`) VALUES';
                                $course_journey_c = count($course_journey[$i]);
                                $lesson_values = 0;
                                for ($j = 0; $j < $course_journey_c; $j++) {

                                    $completeness = $course_journey[$i][$j]->completeness;
                                    $unit_completeness = $unit_completeness + $completeness;
                                    $lesson = Syllabus::where('parent_id', $unit->id)->where('order_number', $j + 1)->first();
                                    if ($lesson) {
                                        $lesson_values = $lesson_values + 1;
                                        $lessonQuery .= '(null,';
                                        $lessonQuery .= $CourseEnrollment->orderId->user_id . ',';
                                        $lessonQuery .= $lesson->id . ',';
                                        $lessonQuery .= $completeness . ',';
                                        $lessonQuery .= $CourseEnrollment->id . ',';
                                        $lessonQuery .= '"' . $CourseEnrollment->updated_at . '",';
                                        $lessonQuery .= '"' . $CourseEnrollment->updated_at . '"),';

                                        // Completeness::updateOrCreate(
                                        //     ['syllabus_id' => $lesson->id,'user_id'=>$CourseEnrollment->orderId->user_id],
                                        //     ['syllabus_id' => $lesson->id, 'completeness' => $completeness,'user_id'=>$CourseEnrollment->orderId->user_id]

                                        // );
                                    }
                                }

                                $lessonQuery = substr($lessonQuery, 0, -1);
                                $lessonQuery .= ';';
                                if ($course_journey_c > 0 && $lesson_values > 0) {
                                    DB::connection('course')->update($lessonQuery);
                                }


                                $unitCount = count($course_journey[$i]);
                                $uc = $unit_completeness / ($unitCount != 0 ? $unitCount : 1);

                                $unit_journey_values = $unit_journey_values + 1;
                                $unitQuery .= '(null,';
                                $unitQuery .= $CourseEnrollment->orderId->user_id . ',';
                                $unitQuery .= $unit->id . ',';
                                $unitQuery .= $uc . ',';
                                $unitQuery .= $CourseEnrollment->id . ',';
                                $unitQuery .= '"' . $CourseEnrollment->updated_at . '",';
                                $unitQuery .= '"' . $CourseEnrollment->updated_at . '"),';

                                // Completeness::updateOrCreate(
                                //     ['syllabus_id' => $unit->id,'user_id'=>$CourseEnrollment->orderId->user_id],
                                //     ['syllabus_id' => $unit->id, 'completeness' => $uc,'user_id'=>$CourseEnrollment->orderId->user_id]

                                // );
                            }
                        }catch (\Exception $ex){
                            
                        }
                    }
                    // return $unitQuery;
                    $unitQuery = substr($unitQuery, 0, -1);
                    $unitQuery.=';';
                    if($course_journey_uc>0 && $unit_journey_values>0){
                        DB::connection('course')->update($unitQuery);
                    }  
                }catch (\Exception $ex){
                            
                } 
            }
        }
        //$url = 'https://v3api.muktopaath.gov.bd/course-completeness-migrate/'.$next_id;
        //echo 'Wait 1 seconds...'.$next_id;
        $url = 'https://v3api.muktopaath.gov.bd/course-completeness-migrate/'.$next_id;
        echo 'Wait 1 seconds...'.$next_id;
        echo '<meta http-equiv="refresh" content="0;URL='.$url.'" />';
        exit; 
        
    }
    
    public function SingleCourseCompleteness($id) {
        $next_id = $id+1;
        return $CourseEnrollment = CourseEnrollment::with('courseBatch')->find($id);
           if($CourseEnrollment && $CourseEnrollment->journey_status){
                
                $course_journey = json_decode($CourseEnrollment->journey_status);
                
                for($i=0;$i<count($course_journey);$i++) {
                    
                    $unit_completeness = 0;
                    $unit = Syllabus::where('course_batch_id',$CourseEnrollment->course_batch_id)->where('order_number',$i+1)->first();
                    if($unit){
                      
                        for($j=0;$j<count($course_journey[$i]);$j++) {
                          return $completeness = (isset($course_journey[$i]) && isset($course_journey[$i][$j]) && isset($course_journey[$i][$j]->completeness))?$course_journey[$i][$j]->completeness:0;
                          return  $completeness = $course_journey[$i][$j]->completeness;
                           $unit_completeness = $unit_completeness+$completeness;
                           $lesson = Syllabus::where('parent_id',$unit->id)->where('order_number',$j+1)->first();
                           
                           if($lesson){
                           
                                Completeness::updateOrCreate(
                                    ['syllabus_id' => $lesson->id,'user_id'=>$CourseEnrollment->orderId->user_id],
                                    ['syllabus_id' => $lesson->id, 'completeness' => $completeness,'user_id'=>$CourseEnrollment->orderId->user_id]
                                );
                           }
                        }

                       
                        $unitCount = count($course_journey[$i]);
                        $uc = $unit_completeness/($unitCount!=0?$unitCount:1);
                        Completeness::updateOrCreate(
                            ['syllabus_id' => $unit->id,'user_id'=>$CourseEnrollment->orderId->user_id],
                            ['syllabus_id' => $unit->id, 'completeness' => $uc,'user_id'=>$CourseEnrollment->orderId->user_id]
                        );
                   }
                }
              
                $url = 'https://v3api.muktopaath.gov.bd/single-course-completeness-migrate/'.$next_id;
                echo 'Wait 1 seconds...'.$next_id;
                echo '<meta http-equiv="refresh" content="5;URL='.$url.'" />';
                exit; 
            }else{
                $url = 'https://v3api.muktopaath.gov.bd/single-course-completeness-migrate/'.$next_id;
                echo 'Wait 3 seconds...'.$next_id;
                echo '<meta http-equiv="refresh" content="5;URL='.$url.'" />';
                exit;   
            }
        
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Finance\PaymentRequest  $paymentRequest
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentRequest $paymentRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Finance\PaymentRequest  $paymentRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentRequest $paymentRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Finance\PaymentRequest  $paymentRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentRequest $paymentRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Finance\PaymentRequest  $paymentRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentRequest $paymentRequest)
    {
        //
    }
}
