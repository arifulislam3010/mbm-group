<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Muktopaath\Course\Models\Course\Course;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Models\Course\CourseCategory;
use Muktopaath\Course\Models\Course\CourseTag;
use Muktopaath\Course\Models\Course\Tag;
use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Models\Myaccount\Sharing;
use Illuminate\Support\Str;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Http\Resources\Sharing as AssessmentShare;
use Muktopaath\Course\Interfaces\DemoInterface;
use DB;
use Auth; 
use Carbon\Carbon;
use App\Models\Myaccount\User;

class CourseController extends Controller
{
    protected $demo;

    public function __construct(DemoInterface $demo){
        $this->demo = $demo;
    }

    public function viewcourse(Request $request){

        if(config()->get('global.owner_id')==1){
            $res = Course::Select('id','title')->where('type',$request->type)->get();
        }else{
            $res = Course::Select('id','title')->where('type',$request->type)->where('owner_id',config()->get('global.owner_id'))->get();
        }

        return response()->json($res);
    }

    public function index(Request $request){

        if($request->selectable){

            if($request->view_batches && $request->course_id){
                $res = CourseBatch::Select('id','title')
                   ->where('course_batches.type',$request->course_type)
                    ->where('course_id',$request->course_id)->get();
                    return response()->json($res);
            }

            $res = Course::Select('id','title')->where('course_batches.type',$request->course_type)->where('owner_id',config()->get('global.owner_id'))->get();
                return response()->json($res);
        }


        $db = config()->get('database.connections.course.database');

        $sharable = Sharing::join($db.'.course_batches','course_batches.id','sharings.table_id')
            ->join($db.'.courses','courses.id','course_batches.course_id')
            ->where('course_batches.type',$request->course_type)
            ->where('user_id',config()->get('global.user_id'))
            ->where('table_name','course_batches')
            ->pluck('course_batches.id');

        $orderby = isset($request->filter)?$request->filter:'DESC';
        $is_super_admin = config()->get('global.owner_id')==1?true:false;

        $batches = CourseBatch::select('course_batches.course_id','course_batches.owner_id','course_batches.id','course_batches.uuid','course_batches.title','course_batches.published_status','course_batches.featured','course_batches.published','course_batches.admin_featured','course_batches.published_date','course_batches.admin_published_date','course_batches.created_at','course_batches.updated_at','course_batches.course_alias_name','course_batches.feature_order')
                    ->join('courses','courses.id','course_batches.course_id')
                    ->where('course_batches.type',$request->course_type)
                   ->when(!$is_super_admin,function($query) use($request) {

                       return $query->where('courses.owner_id',config()->get('global.owner_id'));
                         
                    })
                    ->when(isset($request->type) && $request->type==1,function($query) use($request) {
                            
                            return $query->where(function($query) use($request) {
                            $query->where('course_batches.published',1)
                                ->where('course_batches.published_status',1);
                        });
                        
                    })
                    ->when(isset($request->type) && ($request->type==2 ),function($query) use($request) {
                            return $query->where(function($query) use($request) {
                            $query->where('course_batches.published_status', '=',0)
                                ->orwhere('course_batches.published_status','=',null);
                        });
                        
                    })
                    ->when(isset($request->key),function($query) use($request) {
                            return $query->where(function($query) use($request) {
                            $query->where('course_batches.course_alias_name', 'like', '%'.$request->key.'%');
                        });
                        
                    })
                    ->when(isset($request->type) && ($request->type==3 ),function($query) use($request) {
                            return $query->where(function($query) use($request) {
                            $query->where('course_batches.published_status', '=',1)
                                ->where('course_batches.published','=',0);
                        });
                        
                    })
                    ->when(isset($request->type) && ($request->type==4 ),function($query) use($request) {
                            return $query->where(function($query) use($request) {
                            $query->where('course_batches.admin_featured', '=',1)
                                ->where('course_batches.published','=',1);
                        });
                        
                    })
                    ->when($request->course_id,function($query) use($request) {
                            return $query->where('course_batches.course_id',$request->course_id); 
                        
                    })
                    ->when($request->cat_id,function($query) use($request) {
                            return $query->where('courses.cat_id',$request->cat_id); 
                        
                    })
                    ->when(config()->get('global.view_all')==false , function ($query, $id) use($sharable) {
                        if($sharable){
                            $query->whereIn('course_batches.id',$sharable)
                            ->orwhere('course_batches.created_by',config()->get('global.user_id'));
                        }else{
                            $query->where('course_batches.created_by',config()->get('global.user_id'));
                        }
                    
                    })
                    ->orderby('course_batches.id',$orderby)
                    ->with('course','owner')
                    ->withCount('lessons','certificates','reviewals')
                    ->paginate(10);
        // $batches = CourseBatch::where('id',$id)->fist();

        return response()->json($batches);
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

        if($request->id){
            
            $user_id = $request->id;
        }else{
            $user_id = config()->get('global.user_id');
        }

                
         $check = CourseEnrollment::select('course_enrollments.id as enroll_id')
                    ->join('orders','orders.id','course_enrollments.order_id')
                    ->where('orders.user_id',$user_id)
                    ->where('course_enrollments.course_batch_id',$batch_id)
                    ->first();

                    if($check){
                        return response()->json(['message' => 'You are already enrolled in this assessment .!']);
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
 

                $db = config()->get('database.connections.my-account.database');



                $res = CourseEnrollment::select('u.name','u.email','u.phone','course_enrollments.created_at as enrolled_at','course_enrollments.course_completeness','course_enrollments.id','u.id as user_id')
                        ->where('course_enrollments.id',$course_enrollments->id)
                        ->join('orders as o','o.id','course_enrollments.order_id')
                        ->join($db.'.users as u','u.id','o.user_id')
                        ->first();

                return response()->json([
                    'data' => $res,
                    'message' => 'Successfully enrolled'
                ]);
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
               $course_enrollments->enrolled_by_admin = 1;
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
            'cat_id'            => 'required',
            // 'objective'         => 'required', 
           ]);

            $data = $request->all();

            $insert = new Course;

            $insert->title              = $request->title;
            $insert->cat_id             = $request->cat_id;
            $insert->type               = $request->type;
            $insert->bn_title           = isset($request->bn_title)?$request->bn_title:null;
            
            $insert->learning_outcomes  = json_encode($request->learn);
            $insert->created_by         = config()->get('global.user_id');
            $insert->owner_id           = config()->get('global.owner_id');


            if($insert->save()){
                if($request->tags){
                     $insert->tags()->sync($request->tags);
                }

                $course_batch = new CourseBatch;

                $course_batch->title                    = 'Batch-1';
                $course_batch->course_id                = $insert->id;
                $course_batch->type                     = $request->type;
                $course_batch->bn_title                 = isset($request->bn_title)?$request->bn_title:null;
                $course_batch->uuid                     = Str::orderedUuid();
                $course_batch->requirement              = "<p>&nbsp;</p>";
                $course_batch->course_alias_name        = $request->title;
                $course_batch->certificate_alias_name   = $request->title;
                $course_batch->course_requirment        = '[{"info":"","attach":false}]';
                $course_batch->marks                    = '{"content":{"marks":"0","pass_marks":0},"exam":{"marks":"0","pass_marks":0},"quiz":{"marks":"0","pass_marks":0},"assignment":{"marks":"0","pass_marks":0}}';
                $course_batch->created_by               = config()->get('global.user_id');
                $course_batch->updated_by               = config()->get('global.user_id');
                $course_batch->owner_id                 = config()->get('global.owner_id');

                $course_batch->save();


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