<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Course;
use App\Models\Assessment\CourseBatch;
use App\Models\Assessment\CourseCategory;
use App\Models\Assessment\CourseTag;
use App\Models\Assessment\Tag;
use App\Models\Assessment\Order;
use App\Models\Assessment\CourseEnrollment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Models\Myaccount\Sharing;
use Illuminate\Support\Str;
use App\Models\Assessment\Syllabus;
use App\Http\Resources\Assessment\Sharing as AssessmentShare;
use DB;
use Auth;
use Carbon\Carbon; 
use App\Models\Myaccount\User;

class CourseController extends Controller
{
    // public function index(){
    //     $user_id = Auth::user()->id;

    //     $courses = CourseBatch::where('created_by',$user_id)->orderBy('id','DESC')->paginate(100);
    //     return response()->json($courses);
    // }
    public function index(Request $request){
        
        $db = config()->get('database.connections.assessment.database');

        $sharable = Sharing::join($db.'.course_batches','course_batches.id','sharings.table_id')
            ->join($db.'.courses','courses.id','course_batches.course_id')
            ->where('user_id',config()->get('global.user_id'))
            ->where('table_name','course_batches')
            ->pluck('courses.id');

        // if($request->sharable){
        //     $count = Sharing::where('table_name','courses')
        //         ->where('user_id',config()->get('global.user_id'))
        //         ->get();
                
        //         return AssessmentShare::collection($count);
        // }

        if(config()->get('global.owner_id')==null){
            $courses = Course::with('batch')
                ->where('created_by',config()->get('global.user_id'))
                ->orderby('id','DESC')
                ->paginate(10);
        }else{

        $courses = Course::with('batch')
        ->when(Config('global.owner_id'), function ($query, $id) {
            $query->where('courses.owner_id',$id);
            
        })
        ->when(!Config('global.view_all') , function ($query, $id) use($sharable) {
            if($sharable){
                $query->whereIn('courses.id',$sharable)
                ->orwhere('courses.created_by',config()->get('global.user_id'));
            }else{
                $query->where('courses.created_by',config()->get('global.user_id'));
            }
        
        })
        ->orderBy('id','DESC')
        ->paginate(10);
       }
        return response()->json($courses);
    }


    public function enrolled_batches(){

        $db = config()->get('database.connections.my-account.database');

        $user_id = Auth::user()->id;

        $res = CourseEnrollment::select('course_enrollments.id as enrollment_id', 'course_batches.course_alias_name',
            'courses.title as course_title',
            'course_batches.details',
            'course_batches.rating_sum',
            'course_batches.rating_count',
            'course_batches.title_content_url',
            'course_batches.id as course_batch_id',
            'u.name as creator'
            )
             ->join('orders','orders.id','course_enrollments.order_id')
             ->join('course_batches','course_batches.id','course_enrollments.course_batch_id')
             ->join($db.'.users as u','u.id','course_batches.created_by')
             ->join('courses','courses.id','course_batches.course_id')
             ->where('user_id',$user_id)
             ->paginate(10);

        return response()->json($res);
    }

    public function category(){
        
        $res = CourseCategory::all();
        return response()->json($res);
    }

    public function enroll(Request $request, $batch_id){

        $user_id = Auth::user()->id;
        
         $check = CourseEnrollment::select('course_enrollments.id as enroll_id')
                    ->join('orders','orders.id','course_enrollments.order_id')
                    ->where('orders.user_id',$user_id)
                    ->where('course_enrollments.course_batch_id',$batch_id)
                    ->first();

                    if($check){
                        return  response()->json($check->enroll_id);
                    }
                    else{
        

        $check = Order::select('course_enrollments.id')->join('course_enrollments','course_enrollments.order_id','orders.id')
                    ->where('course_enrollments.course_batch_id',$batch_id)
                    ->where('orders.user_id',$user_id)
                    ->first();


                    if($check){
                        return response()->json(['message' => 'You are already enrolled in this assessment .!']);
                    }else{
                        DB::beginTransaction();
        try{
            $order = new Order;
                $order->amount              = 0;
                $order->payment_status      = 0;
                $order->type                = 0;
                $order->user_id             = $user_id;
                $order->save();

                $course_enrollments     = new CourseEnrollment;
                $course_enrollments->order_id           = $order->id;
                $course_enrollments->course_batch_id    = $batch_id;
                $course_enrollments->journey_status     = '[]';
                $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
                $course_enrollments->course_completeness = 0;
                $course_enrollments->status = 1;
                $course_enrollments->save();
                DB::commit();

                return response()->json($course_enrollments->id);
        }catch (\Exception $e) {
            DB::rollback();
    // something went wrong
            }
                    }

        }

                
    }

    public function enroll_by_admin(Request $request, $batch_id){
       
        $user_id = $request['user_id'];
        
        
        $check = CourseEnrollment::select('course_enrollments.id as enroll_id')
                   ->join('orders','orders.id','course_enrollments.order_id')
                   ->where('orders.user_id',$user_id)
                   ->where('course_enrollments.course_batch_id',$batch_id)
                   ->first();

                   if($check){
                       return  response()->json($check->enroll_id);
                   }
                   else{
       
                    
       $check = Order::select('course_enrollments.id')->join('course_enrollments','course_enrollments.order_id','orders.id')
                   ->where('course_enrollments.course_batch_id',$batch_id)
                   ->where('orders.user_id',$user_id)
                   ->first();

                
                   if($check){
                       return response()->json(['message' => 'You are already enrolled in this assessment .!']);
                   }else{
                       DB::beginTransaction();
                       
       try{
           $order = new Order;
               $order->amount              = 0;
               $order->payment_status      = 0;
               $order->type                = 0;
               $order->user_id             = $user_id;
               $order->save();

               $course_enrollments     = new CourseEnrollment;
               $course_enrollments->order_id           = $order->id;
               $course_enrollments->course_batch_id    = $batch_id;
               $course_enrollments->journey_status     = '[]';
               $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
               $course_enrollments->course_completeness = 0;
               $course_enrollments->status = 1;
               $course_enrollments->save();
               DB::commit();

               return response()->json($course_enrollments->id);
       }catch (\Exception $e) {
           DB::rollback();
   // something went wrong
           }
                   }

       }
    }

    public function store(Request $request){


        $this->validate($request,[
            'title'             => 'required',
            // 'cat_id'            => 'required',
            // 'objective'         => 'required',
            // 'course_code'       => 'required|unique:courses'  
           ]);

        $data = $request->all();

        $user_id = Auth::user()->id;
            $owner_id = Config('global.owner_id');
            $data = $request->all();

            $insert = new Course;

            $insert->title              = $request->title;
            // $insert->cat_id             = $request->cat_id;
            // $insert->cat_id             = (int) explode("@",$request->cat_id)[0];
            // $insert->cat_title_en       = explode("@",$request->cat_id)[1];
            // $insert->cat_title_bn       = explode("@",$request->cat_id)[2];
            // $insert->course_level       = $request->course_level;
            // $insert->course_duration    = $request->course_duration;
            // $insert->language_id        = $request->language_id;
            // $insert->course_code        = $request->course_code;
            $insert->course_code        = rand(1000000, 9999999);
            $insert->created_by         = $user_id;
            $insert->updated_by         = $user_id;
            $insert->owner_id           = $owner_id;

            if($insert->save()){
                $duration = (int)$request['get_hrs'] * 60;
                $duration += (int)$request['get_mins'];
       
                $course_batch = new CourseBatch;

                $course_batch->title                    = 'Batch-1';
                $course_batch->course_id                = $insert->id;
                $course_batch->duration                 = $duration;
                $course_batch->repeat_status            = $request->repeat;
                $course_batch->live_link                = $request->live_link;
                $course_batch->live_class_url_type      = isset($request->live_class_url_type)?$request->live_class_url_type:null;
                $course_batch->repeat_num               = $request->repeat_num;
                $course_batch->repeat_period            = $request->repeat_period;
                $course_batch->restricted_access_code   = Str::random(15);
                $course_batch->occurs_on                = json_encode($request->occurs_on);
                $course_batch->occurrences              = $request->occurrences;
                $course_batch->occurs_end_type          = $request->occurs_end_type;
                $course_batch->start_date               = date('Y-m-d H:i:s',strtotime($request['start_date'])); 
                $course_batch->end_date                 = isset($request['end_date'])?date('Y-m-d H:i:s',strtotime($request['end_date'])):null;
                $course_batch->course_alias_name        = $request->title;
                $course_batch->certificate_alias_name   = $request->title;
                $course_batch->created_by               = $user_id;
                $course_batch->updated_by               = $user_id;
                $course_batch->owner_id                 = $owner_id;

                $course_batch->save();
                
                $syllabus =  new Syllabus;
                $syllabus->title               = 'Class 1';
                $syllabus->status              = 1;
                $syllabus->order_number        = 1;
                $syllabus->course_batch_id     = $course_batch->id;
                $syllabus->content_title       = '';
                $syllabus->duration            = $duration;
                $syllabus->live_class_url      = $request->live_link;
                $syllabus->live_class_url_type = isset($request->live_class_url_type)?$request->live_class_url_type:null;
                $syllabus->content_type        = '';
                $syllabus->created_by          = $user_id;
                $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($request['start_date']));
                $syllabus->end_date            = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $duration . ' Minutes'));
                $syllabus->save();

                $previous_class = [];

                if($request->repeat==1){
                    $occur = $request->occurrences?$request->occurrences:30;
                    $count = 1;
                    $i = 1;
                    while($i<$occur){

                        $previous_class = $syllabus;

                    if($request->repeat_period=='weekly'){
                        if($i==1){
                            $date = Carbon::parse($previous_class->start_date)->addWeeks($request->repeat_num-1);
                         }else{
                            $date = Carbon::parse($previous_class->start_date)->addWeeks($request->repeat_num-1);
                         }


                        $h  =  $date->format('h');
                        $in =  $date->format('i');
                        $s  =  $date->format('s');


                            foreach ($request->occurs_on as $key => $value) {
                                if($value && $i<$occur){

                                    $syllabus =  new Syllabus;
                                    $when = $date<=$previous_class->start_date?'next':'this';

                                    switch ($key) {

                                        case 1:
                                           $case = $when.' saturday';
                                           $syllabus->start_date = $date->modify($when.' saturday');
                                            break;
                                        case 2:
                                            $case = $when.' sunday';
                                            //return $date;
                                           $syllabus->start_date = $date->modify($when.' sunday');
                                            break;
                                        case 3:
                                            $case = $when.' monday';
                                           $syllabus->start_date = $date->modify($when.' monday');
                                            break;
                                        case 4:
                                            $case = $when.' tuesday';
                                            $syllabus->start_date = $date->modify($when.' tuesday');
                                            break;
                                        case 5:
                                            $case = $when.' wednesday';
                                           $syllabus->start_date = $date->modify($when.' wednesday');
                                            break;
                                        case 6:
                                            $case = $when.' thursday';
                                           $syllabus->start_date = $date->modify($when.' thursday');
                                            break;
                                        case 7:
                                            $case = $when.' friday';
                                            $syllabus->start_date = $date->modify($when.' friday');
                                            break;
                                        
                                        default:
                                            // code...
                                            break;


                                    }

                                    $syllabus->start_date =  date_time_set($syllabus->start_date, $h, $in, $s);

                                    $syllabus->end_date    = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $duration . ' Minutes'));

                                    if($request->end_time && ($syllabus->end_date>$request->end_time)){
                            
                                        break;
                                    }

                                    $syllabus->title               = 'Class '.($count+1);
                                    $syllabus->status              = 1;
                                    $syllabus->order_number        = 1;
                                    $syllabus->course_batch_id     = $course_batch->id;
                                    $syllabus->content_title       = '';
                                    $syllabus->duration            = $duration;
                                    $syllabus->live_class_url      = $request->live_link;
                                    $syllabus->live_class_url_type = isset($request->live_class_url_type)?$request->live_class_url_type:null;
                                    $syllabus->content_type        = '';
                                    $syllabus->created_by          = $user_id;

                                    $syllabus->save();

                                    $count++;
                                    $i++;
                                    
                                }
                            }

                        }else{
                            $syllabus =  new Syllabus;
                            
                            if($request->repeat_period=='daily'){
                                $syllabus->start_date          = Carbon::parse($previous_class->start_date)->addDays($request->repeat_num);

                        }else if ($request->repeat_period=='monthly'){
                            $syllabus->start_date          = Carbon::parse($previous_class->start_date)->addMonths($request->repeat_num);
                        }

                        $syllabus->end_date            = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $duration . ' Minutes'));
                        if($request->end_time && ($syllabus->end_date>$request->end_time)){
                            
                                        break;
                            }
                        
                        
                        

                        $syllabus->title               = 'Class '.($i+1);
                        $syllabus->status              = 1;
                        $syllabus->order_number        = 1;
                        $syllabus->course_batch_id     = $course_batch->id;
                        $syllabus->content_title       = '';
                        $syllabus->duration            = $duration;
                        $syllabus->live_class_url      = $request->live_link;
                        $syllabus->live_class_url_type = isset($request->live_class_url_type)?$request->live_class_url_type:null;
                        $syllabus->content_type        = '';
                        $syllabus->created_by          = $user_id;

                        $syllabus->save();
                        $i++;
                    }
                        }

                }


                $data = [
                    'id'        => $insert->id,
                    'title'     => $insert->title,
                    'batch_id'  => $course_batch->id,
                    'status'    => 'Success',
                    'code'      => '200',
                    'message'   => 'Course successfully saved.',
                ];

                return response()->json(['data' => $data], 200);
            }
            else{
                $data = [
                    'status'  => 'error',
                    'code'    => '404',
                    'message' => 'Error occurred. Course doesnot save.',
                ];

                return response()->json($data, 404);
            }
        return response()->json([
            'message' => 'File Type created successfully.',
            'data' => '12'
        ]);
   

    }
  

    public function destroy($id){

        $course = Course::find($id);

        if(is_null($course)) {
            return response()->json(['message' => 'Course not found'],404);
        }

        if($course->delete()){
            return response()->json([
                'data'    => $id,
                'message' => 'Sucessfully Deleted'
            ],200);
        }
        else{
            return response()->json([
                'message' => 'Something went wrong'
            ],401); 
        }
        
    }

}