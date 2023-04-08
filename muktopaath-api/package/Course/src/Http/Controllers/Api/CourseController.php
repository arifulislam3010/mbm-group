<?php

namespace Muktopaath\Course\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use Muktopaath\Course\Models\AdminAppSetting\Tag;
use Illuminate\Support\Str;
//Models
use Muktopaath\Course\Models\Course\Wishlist;
use Muktopaath\Course\Models\Course\Course;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Models\Course\CourseCategory;
use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\Completeness;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Muktopaath\Course\Models\Course\CourseContentUserFeedback;
use App\Models\Myaccount\InstitutionInfo;
use App\Models\UserManagement\BatchAssign;
use App\Models\Question\Question;
//Resources
use Muktopaath\Course\Http\Resources\OrderCollection as OrderCollection;
use Muktopaath\Course\Http\Resources\EnrollCourse as EnrollCourseResource;
use Muktopaath\Course\Http\Resources\EnrollBasic as EnrollBasicResource;
use Muktopaath\Course\Http\Resources\Course as ResourceCourse;
use Muktopaath\Course\Http\Resources\Batch as ResourceBatch;
use Muktopaath\Course\Http\Resources\BatchLogin as ResourceBatchLogin;
use Muktopaath\Course\Http\Resources\BatchBasic as BatchBasicResource;
use Muktopaath\Course\Http\Resources\Order as OrderResource;
use Muktopaath\Course\Http\Resources\CourseCategories as ResourceCourseCategories;
use App\Models\ContentBank\LearningContent;
use Muktopaath\Course\Models\Course\SyllabusStatus;
use DateTime;
use Auth;
use DB;
use Carbon\Carbon;
use Muktopaath\Course\Models\Course\EnrolledAttachment;
use Muktopaath\Course\Lib\GamificationClass;
use Muktopaath\Course\Lib\helper;
use Muktopaath\Course\Lib\EnrollMent;
use App\Lib\GamificationTrait;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Http\Resources\Content;
use Muktopaath\Course\Http\Resources\LearnerResource;
Use App\Models\Myaccount\User;
use Illuminate\Support\Facades\Cache;
class CourseController extends Controller
{
    use EnrollMent,GamificationTrait;
    public $successStatus = 200;
    private function redisCache($key,$content=null){
        $redisKey = 'api_course'.md5($key);
        if(empty($content)) {
            if (Cache::store('redis')->has($redisKey)) {
                $content = Cache::store('redis')->get($redisKey);
                $content = json_decode($content, true);
                $data = (object) [];
                if(isset($content[$redisKey])){
                    $data->data = $content[$redisKey];
                }else{
                    $data->data = $content;
                }
                
                return $data;
            }
        }else {
            if(!is_array($content)) {
                $content = array($redisKey=>$content);
            }
            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
            Cache::store('redis')->put($redisKey,$content,3600);
        }
        return '';
    }
    public function courseEntrollmentAttachment(Request $request){
        $data = $request->all();
        $enrolled_id = '';
        $attach_id = [];
        foreach ($data as $key => $value) {
            $enrolled_id = $value['enrolled_id'];
            $datafind = EnrolledAttachment::where('enrolled_id',$value['enrolled_id'])->where('index_key',$key)->first();
            if($datafind){
                if($value['attach_id']!=null || $value['attach_id']!=''){
                    if($value['attach']){
                        $datafind->attachment_id = $value['attach_id'];
                        $datafind->attach = 1;
                    }else{
                        $datafind->attachment = $value['attach_id'];
                        $datafind->attach = 0;
                    }
                    $datafind->updated_by = Auth::user()->id;
                    $datafind->save();
                }else{
                    $datafind->ddelete();
                }
                
            }else{
              $store = new EnrolledAttachment();
              $store->enrolled_id = $value['enrolled_id'];
                if($value['attach']){
                    $store->attachment_id = $value['attach_id'];
                    $store->attach = 1;
                }else{
                    $store->attachment = $value['attach_id'];
                    $store->attach = 0;
                }
              
              $store->index_key = $key;
              $store->created_by = Auth::user()->id;
              $store->updated_by = Auth::user()->id;
              $store->save();
            }
        }
        
        return $attachments = EnrolledAttachment::where('enrolled_id',$enrolled_id)->orderBy('index_key','ASC')->get();
    }

    public function attach(Request $request){

        $attachments = $request->attachments;

        foreach ($attachments as $key => $value) {
            $att = EnrolledAttachment::where('index_key',$key)
                    ->where('enrollment_id',$request->enroll_id)
                    ->first();
            if($att){
                $att->enrollment_id = $request->enroll_id;
                $att->index_key     = $key;
                $att->info_input    = $value['info_input'];
                $att->attach        = isset($value['attach'])?$value['attach']:0;
                $att->file_id       = $value['file_id'];
                $att->created_by    = config()->get('global.user_id');
                $att->update();
            }else{
                $att = new EnrolledAttachment;
                $att->enrollment_id = $request->enroll_id;
                $att->index_key     = $key;
                $att->info_input    = $value['info_input'];
                $att->attach        = isset($value['attach'])?$value['attach']:0;
                $att->file_id       = $value['file_id'];
                $att->created_by    = config()->get('global.user_id');
                $att->save();
            }


        }

        return response()->json(['data' => $att, 'message' => 'Successfully attached files']);

    }

    public function EnrollmentCheck($id){
        $user = config()->get('global.user_id');
        if($user){
            $courseBatch  = CourseBatch::Where('uuid',$id)->first();
            if($courseBatch){
                $enroll = CourseEnrollment::where('user_id',$user)->where('course_batch_id',$courseBatch->id)->first();
                if($enroll){
                    $EnrollmentId = $enroll->id;
                    $uuid = $enroll->uuid;
                }else{
                    $EnrollmentId = '';
                    $uuid = '';
                }
                $Wishlist = Wishlist::where('user_id',$user)->where('course_batch_id',$courseBatch->id)->first();
                if($Wishlist){
                    $wishlist_status = 1;
                }else{
                    $wishlist_status = 0;
                }

                return response()->json(['status'=>1,'EnrollmentId'=>$EnrollmentId,'uuid'=>$uuid,'wishlist_status'=>$wishlist_status], $this->successStatus);
            }else{
                return response()->json(['status'=>3,'EnrollmentId'=>'','wishlist_status'=>0], $this->successStatus);
            }
        }else{
            return response()->json(['status'=>3,'EnrollmentId'=>'','wishlist_status'=>0], $this->successStatus);
        }
        
    }


   
    public function allCoursesChange(Request $request)
    {
        //return $request->all();
        $username            = ($request->has('username'))?$request['username']:null;
        if($username!=null){
            if($partnerSelect = InstitutionInfo::where('username',$username)->first())
            {
                $owner_id = $partnerSelect->id;
            }else
            {
                $owner_id = null;
            }
            
        }else
        {
            $owner_id = null;
        }
        $upcomming     = ($request->has('upcomming'))?$request['upcomming']:null;
        $payment_status     = ($request->has('payment_status'))?$request['payment_status']:null;
        $admin_featured     = ($request->has('admin_featured'))?$request['admin_featured']:null;
        $featured           = ($request->has('featured'))?$request['featured']:null;
        $order              = ($request->has('order'))?$request['order']:null;
        $status             = ($request->has('status'))?$request['status']:null;
        $limit              = ($request->has('limit'))?$request['limit']:null;
        if($limit<1 || $limit>20){$limit = 20;}
        $price_search       = ($request->has('price_search'))?$request['price_search']:null;
        $price_start        = ($request->has('price_start'))?$request['price_start']:null;
        $price_end          = ($request->has('price_end'))?$request['price_end']:null;
        $duration_search    = ($request->has('duration_search'))?$request['duration_search']:null;
        $duration_start     = ($request->has('duration_start'))?$request['duration_start']:null;
        $duration_end       = ($request->has('duration_end'))?$request['duration_end']:null;
        $rating             = ($request->has('rating'))?$request['rating']:null;
        $favorite           = ($request->has('favorite'))?$request['favorite']:null;
        $for_web            = ($request->has('for_web'))?null:1;
        $end                = ($request->has('end'))?$request['end']:null;
        $running            = ($request->has('running'))?$request['running']:null;

        $today_date = Carbon::now()->format('Y-m-d');
        if($admin_featured){
            $limit = 20;
        }
       //$cd = new DateTime();
        if($favorite!=null){
            //return $today_date;
            $course = CourseBatch::where('course_batches.published_status', 1)
            // ->where('course_batches.courses_for_status',0)
            ->when($owner_id, function($q) use($owner_id){return $q->where('course_batches.owner_id' , $owner_id);})
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->select('course_batches.*')
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')
            
            ->groupBy('course_batches.course_id');})
            ->where(function($q) use($today_date){
                            $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
                        })
            ->orderBy('enroll', 'DESC')
            ->orderBy('rating_sum', 'DESC')
            ->paginate($limit);
            //return response()->json(['dd'=>$course]);
            //return ResourceBatch::collection($course);
            return BatchBasicResource::collection($course);
        }
        if($running!=null){
            //return $today_date;
            $course = CourseBatch::where('course_batches.published_status', 1)
            // ->where('course_batches.courses_for_status',0)
            ->when($owner_id, function($q) use($owner_id){return $q->where('course_batches.owner_id' , $owner_id);})
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->select('course_batches.*')
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')
            ->groupBy('course_batches.course_id');})
            // ->where(function($q) use($today_date){
            //                 $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
            //             })
            ->orderBy('enroll', 'DESC')
            ->orderBy('rating_sum', 'DESC')
            ->paginate($limit);
            return BatchBasicResource::collection($course);
        }
        if($end==1){
            $course = CourseBatch::when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->where('published_status', 1)
            // ->where('courses_for_status',0)
            ->whereIn('created_at',function($query){$query->select(DB::raw('max(created_at)'))->from('course_batches')
                          ->groupBy('course_id');})
            ->when($status, function($q) use($status){return $q->where('status',$status);})
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->whereNotNull('end_date')
             ->where(function($q) use($today_date){
                                $q->where('end_date','<=',$today_date);
                            })
            ->orderBy('created_at',$order)->paginate($limit);
            //return ResourceBatch::collection($course);
            return BatchBasicResource::collection($course);     
        }
        if($upcomming==1){
            $course = CourseBatch::when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->where('published_status', 1)
            // ->where('courses_for_status',0)
            ->whereIn('created_at',function($query){$query->select(DB::raw('max(created_at)'))->from('course_batches')
                          ->groupBy('course_id');})
            ->when($status, function($q) use($status){return $q->where('status',$status);})
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->whereNotNull('end_date')
             ->where(function($q) use($today_date){
                                $q->where('start_date','>',new DateTime());
                            })
            ->orderBy('created_at',$order)->paginate($limit);
            //return ResourceBatch::collection($course);
            return BatchBasicResource::collection($course);     
        }
        if($admin_featured==1){
            $course = CourseBatch::when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->where('published_status', 1)
            // ->where('courses_for_status',0)
            ->whereIn('created_at',function($query){$query->select(DB::raw('max(created_at)'))->from('course_batches')
                          ->groupBy('course_id');})
            ->when($status, function($q) use($status){return $q->where('status',$status);})
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->where('admin_featured', $admin_featured)
            ->orderBy('created_at',$order)->paginate($limit);
            return BatchBasicResource::collection($course);     
        }
       $course = CourseBatch::when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
        ->where('published_status', 1)
        // ->where('courses_for_status',0)
        ->whereIn('created_at',function($query){$query->select(DB::raw('max(created_at)'))->from('course_batches')
                      ->groupBy('course_id');})        
        ->when($payment_status, function($q) use($payment_status){return $q->where('payment_status', $payment_status);})
        ->when($featured, function($q) use($featured){return $q->where('featured', $featured);})
        ->when($status, function($q) use($status){return $q->where('status',$status);})
        ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
        ->when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
        ->orderBy('created_at',$order)->paginate($limit);
        //return ResourceBatch::collection($course);
        return BatchBasicResource::collection($course);
    }
    
     public function allCourses(Request $request)
    {
        //return $request->all();
        $username            = ($request->has('username'))?$request['username']:null;
        if($username!=null){
            if($partnerSelect = InstitutionInfo::where('username',$username)->first())
            {
                $owner_id = $partnerSelect->id;
            }else
            {
                $owner_id = null;
            }
            
        }else
        {
            $owner_id = null;
        }
        $upcomming     = ($request->has('upcomming'))?$request['upcomming']:null;
        $payment_status     = ($request->has('payment_status'))?$request['payment_status']:null;
        $admin_featured     = ($request->has('admin_featured'))?$request['admin_featured']:null;
        $featured           = ($request->has('featured'))?$request['featured']:null;
        $order              = ($request->has('order'))?$request['order']:null;
        $status             = ($request->has('status'))?$request['status']:null;
        $limit              = ($request->has('limit'))?$request['limit']:null;
        if($limit<1 || $limit>20){$limit = 20;}
        $price_search       = ($request->has('price_search'))?$request['price_search']:null;
        $price_start        = ($request->has('price_start'))?$request['price_start']:null;
        $price_end          = ($request->has('price_end'))?$request['price_end']:null;
        $duration_search    = ($request->has('duration_search'))?$request['duration_search']:null;
        $duration_start     = ($request->has('duration_start'))?$request['duration_start']:null;
        $duration_end       = ($request->has('duration_end'))?$request['duration_end']:null;
        $rating             = ($request->has('rating'))?$request['rating']:null;
        $favorite           = ($request->has('favorite'))?$request['favorite']:null;
       //$cd = new DateTime();
        if($favorite!=null){
            $course = CourseBatch::leftJoin('course_enrollments','course_batches.id','=','course_enrollments.course_batch_id')
            ->leftJoin('course_enrollment_rating','course_enrollments.id','=','course_enrollment_rating.enrollement_id')
            ->where('course_batches.published_status', 1)
            ->where('course_batches.courses_for_status',0)
             ->where(function($q) use($today_date){
                            $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
                        })
            ->when($owner_id, function($q) use($owner_id){return $q->where('course_batches.owner_id' , $owner_id);})
            ->select('course_batches.*',DB::raw('AVG(course_enrollment_rating.rating_point) as ratings_average'))->orderBy('ratings_average','DESC')
            ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')
                      ->groupBy('course_batches.course_id');})->groupBy('course_batches.id')->get();
            //return response()->json(['dd'=>$course]);
            return ResourceBatch::collection($course);
            //return BatchBasicResource::collection($course);
        }
       $course = CourseBatch::when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
        ->where('published_status', 1)
        ->where('course_batches.courses_for_status',0)
        ->whereIn('created_at',function($query){$query->select(DB::raw('max(created_at)'))->from('course_batches')
                      ->groupBy('course_id');})        
        // ->when($duration_search, function($q) use($duration_start,$duration_end){return $q->whereBetween('duration',[$duration_start, $duration_end]);})
        ->when($price_search, function($q) use($price_start,$price_end){return $q->whereBetween('payment_point_amount',[$price_start, $price_end]);})
        ->when($upcomming, function($q) use($upcomming){return $q->where('start_date','>',new DateTime());})
        ->when($payment_status, function($q) use($payment_status){return $q->where('payment_status', $payment_status);})
        ->when($featured, function($q) use($featured){return $q->where('featured', $featured);})
        ->when($admin_featured, function($q) use($admin_featured){return $q->where('admin_featured', $admin_featured);})
        ->when($status, function($q) use($status){return $q->where('status',$status);})
         ->where(function($q) use($today_date){
                            $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
                        })
        ->orderBy('created_at',$order)->paginate($limit);
        return ResourceBatch::collection($course);
        //return BatchBasicResource::collection($course);
    }

    public function courseDetails($id)
    {   
        if(config()->get('global.redis_status')){
            if(empty($cmp)){
                    $course = CourseBatch::with('sessions','owner','course','Rating')->Where('uuid',$id)->orWhere('slug',$id)->first();
                    
                    if($course) {
                        $course = new ResourceBatch($course);
                        $cmp = $this->redisCache('details_'.$id,$course);
                        return $course;
                    }
                    else return '0';
                
            }else{
                return response()->json($cmp);
            }
        }else{
            $course = CourseBatch::with('sessions','owner','course','Rating')->Where('uuid',$id)->orWhere('slug',$id)->first();
                
            if($course) {
                $course = new ResourceBatch($course);
                return $course;
            }
            else return '0';
        }
    }

    public function courseDetailsPreview($id,$token)
    {  
        $course = CourseBatch::with('sessions','owner','CreatedBy','UpdatedBy','course')->where([['preview_token',$token],['id',$id]])->first();
        if(!empty($course)) return new ResourceBatch($course);
        else return '0';
    }


    
    public function courseEntrollment(Request $request)
    {

        
        $helper = new helper;
        
        $data = $request->all();
        $user = $user = config()->get('global.user_id');

        try{
            DB::beginTransaction();
            $batch = CourseBatch::find($data['batch_id']);
            if($batch){
                 $orderCheck = Order::where('user_id',$user)->with('enrollment')->whereHas('enrollment', function($q)use($batch){$q->where('course_batch_id','=',$batch->id);})->first();
                if($orderCheck){
                    $data = [
                        'status'    => true,
                        'message'   => 'Already Enrolled This course',
                        'data'      => $orderCheck,
                        ];
                    return response()->json($data,$this->successStatus);
                }else{
                    
                    $record = Order::latest()->first();
                    if($record){$order_number=$record->order_number;}else{$order_number='';}
                    
                    
                    $order = new Order;
                    $order->amount              = $batch->payment_point_amount;
                    $order->order_number        = $helper->nextOrderNumber($order_number);
                    $order->payment_status      = 0;
                    $order->type                = 0;
                    $order->user_id             = $user;
        
                    if($order->save()){
                       
                            
                        $course_enrollments     = new CourseEnrollment;
                        $course_enrollments->order_id           = $order->id;
                        $course_enrollments->uuid           = Str::uuid();
                        $course_enrollments->user_id           = $user;
                        $course_enrollments->activity_update_time    = Carbon::now();
                        
                        $course_enrollments->course_batch_id    = $batch->id;
                        // $course_enrollments->journey_status     = json_encode($helper->journeystatus(json_decode($batch->syllabus)));
                        $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
                        $course_enrollments->course_completeness = 0;
                        if($batch->enrolment_approval_status==1)
                        $course_enrollments->status = 0;
                        else
                        $course_enrollments->status = 1;
                        $course_enrollments->save();
                        $this->gamificationStore('enr',$user,$course_enrollments->id,1);
                        $orderN = Order::where('id',$order->id)->with('enrollment')->first();
                        $data = [
                            'status'    => true,
                            'message'   => 'Enrolled success',
                            'data'      => $orderN,
                            ];
                        return response()->json($data,$this->successStatus);
                       
                        // $gamification_class = new GamificationClass;
                        // $gamification_class->gamificationStore('enr',$user->id,$course_enrollments->id,1);
                    }
                    
                }
            }else{

            }

            DB::commit();

            $data = [
                'status'    => true,
                'message'   => 'Enrollment Successful',
                'data'    => $course_enrollments,
            ];
            return response()->json($data,$this->successStatus);
        }
        catch(\Exception $e){
            DB::rollBack();
            $data = [
                'status'  => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($data, 404);
        }
        
    }
    


    public function checkoutList(Request $request)
    {
        $type    = ($request->has('type'))?$request['type']:null;

        try{
            $user = Auth::user();

            $order = Order::where([
                ['user_id' , $user->id],
                ['payment_status' , $type]
            ])->get();

            $data = [
                'status'    => true,
                'data'      => $order
            ];
            return response()->json($data,$this->successStatus);
        }
        catch(\Exception $e){
            $data = [
                'status'  => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($data, 404);
        }
    }

    public function myCourseEnrollmentList(Request $request)
    {
        
        $today_date = Carbon::now()->format('Y-m-d');
        
        $data = $request->all();
        try{
            $user = config()->get('global.user_id');
            if(isset($data['limit'])){
                if($data['type']==1){
                   
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->where('orders.user_id',$user)->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_enrollments.status', 1)->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('course_enrollments.course_completeness','>=',100)->orderBy('course_enrollments.id','DESC')->get(); 
                }elseif($data['type']==2){
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->where('course_enrollments.status', 1)->whereNull('course_batches.deleted_at')->where('course_batches.published_status', 1)
                        ->where(function($q){
                        $q->where('course_batches.end_date','>=',date('Y-m-d'))->orWhereNull('course_batches.end_date');
                        })
                       ->select('course_enrollments.*')->where('orders.user_id',$user)->where('course_enrollments.course_completeness','<',100)->orderBy('course_enrollments.id','DESC')->get(); 
                }elseif($data['type']==3){
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->where('course_enrollments.status', 1)->whereNull('course_batches.deleted_at')->where('course_batches.published_status', 1) ->where(function($q){
                        $q->where('course_batches.end_date','>=',date('Y-m-d'))->orWhereNull('course_batches.end_date');
                        })->select('course_enrollments.*')->where('orders.user_id',$user)->where('course_enrollments.course_completeness','<', 100)->orderBy('course_enrollments.id','DESC')->get(); 
                }elseif($data['type']==4){
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_enrollments.status', 0)->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('orders.user_id',$user)->orderBy('course_enrollments.id','DESC')->get(); 
                }else{
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('orders.user_id',$user)->orderBy('course_enrollments.id','DESC')->get();  
                }
            }else{
                if($data['type']==1){
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->where('orders.user_id',$user)->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_enrollments.status', 1)->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('course_enrollments.course_completeness','>=',100)->orderBy('course_enrollments.id','DESC')->paginate(8); 
                }elseif($data['type']==2){
                    
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('orders.user_id',$user)->orderBy('course_enrollments.activity_update_time','DESC')->paginate(8);

                    // $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->where('course_enrollments.status', 1)->whereNull('course_batches.deleted_at')->where('course_batches.published_status', 1)
                    //     ->where(function($q){
                    //     $q->where('course_batches.end_date','>=',date('Y-m-d'))->orWhereNull('course_batches.end_date');
                    //     })
                    //    ->select('course_enrollments.*')->where('orders.user_id',$user)->where('course_enrollments.course_completeness','<',100)->orderBy('course_enrollments.id','DESC')->paginate(8); 
                }elseif($data['type']==3){
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->where('course_enrollments.status', 1)->whereNull('course_batches.deleted_at')->where('course_batches.published_status', 1) ->where(function($q){
                        $q->where('course_batches.end_date','>=',date('Y-m-d'))->orWhereNull('course_batches.end_date');
                        })->select('course_enrollments.*')->where('orders.user_id',$user)->where('course_enrollments.course_completeness','<', 100)->orderBy('course_enrollments.id','DESC')->paginate(8); 
                }elseif($data['type']==4){
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_enrollments.status', 0)->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('orders.user_id',$user)->orderBy('course_enrollments.id','DESC')->paginate(8); 
                }else{
                    $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_batches.published_status', 1)->select('course_enrollments.*')->where('orders.user_id',$user)->orderBy('course_enrollments.id','DESC')->paginate(8);  
                }
            }
            
            
            return EnrollBasicResource::collection($order);
            //return EnrollCourseResource::collection($order);
        }
        catch(\Exception $e){
            $data = [
                'status'  => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($data, 404);
        }
    }

    public function UserEnrollmentList(Request $request,$username)
    {
        
        $today_date = Carbon::now()->format('Y-m-d');
        
        $data = $request->all();
        try{
            $user = User::where('username',$username)->first();
            if($user){
                $order = CourseEnrollment::join('orders','orders.id','course_enrollments.order_id')->where('orders.user_id',$user->id)->rightJoin('course_batches','course_batches.id','course_enrollments.course_batch_id')->whereNull('course_batches.deleted_at')->where('course_enrollments.status', 1)->where('course_batches.published_status', 1)->select('course_enrollments.*')->orderBy('course_enrollments.id','DESC')->paginate(8); 
            
                return EnrollBasicResource::collection($order);
            }else{
                $data = [
                    'status'  => false,
                    'message' => 'user not found',
                ];
    
                return response()->json($data, 404);
            }
           
            //return EnrollCourseResource::collection($order);
        }
        catch(\Exception $e){
            $data = [
                'status'  => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($data, 404);
        }
    }
    

    public function EnrollCourseDetails($id){
        $user_id = $user = config()->get('global.user_id');
        $check = Order::where('orders.user_id',$user_id)->join('course_enrollments','course_enrollments.order_id','orders.id')->where('course_enrollments.id',$id)->orWhere('course_enrollments.uuid',$id)->first();
        // return $check;
        if($check)
        {
            $this->EnrollMentUpdate($id);
            $courseEntrollment = CourseEnrollment::where('uuid',$id)->with('courseBatch','courseRating','Attachment')->first();
            return new EnrollCourseResource($courseEntrollment);
        }else{
            $data = [
                'status'  => false,
                'message' => 'Data not found',
            ];
            return response()->json($data, 404);
        }
    }
    
    public function courseContentFeedback(Request $request,$course_batch_id,$unit_id,$lesson_id){
        $user = Auth::user();
        $result['total_likes']=0;
        $result['total_dislikes']=0;
        $result['total_flags']=0;
        $result['liked']=0;
        $result['disliked']=0;
        $result['flagged']=0;
        return $result;
        // return $result = CourseContentUserFeedback::select(
        //     DB::raw('SUM(CASE WHEN liked = 1 THEN 1 ELSE 0 END) AS total_likes'),
        //     DB::raw('SUM(CASE WHEN disliked = 1 THEN 1 ELSE 0 END) AS total_dislikes'),
        //     DB::raw('SUM(CASE WHEN flagged = 1 THEN 1 ELSE 0 END) AS total_flags'),
        //     DB::raw('(SELECT liked FROM course_content_user_feedbacks WHERE user_id = '.$user->id.' AND course_batch_id = '.$course_batch_id.' AND unit_id = '. $unit_id.' AND lesson_id = '. $lesson_id.') AS liked'),
        //     DB::raw('(SELECT disliked FROM course_content_user_feedbacks WHERE user_id = '.$user->id.' AND course_batch_id = '.$course_batch_id.' AND unit_id = '. $unit_id.' AND lesson_id = '. $lesson_id.') AS disliked'),
        //     DB::raw('(SELECT flagged FROM course_content_user_feedbacks WHERE user_id = '.$user->id.' AND course_batch_id = '.$course_batch_id.' AND unit_id = '. $unit_id.' AND lesson_id = '. $lesson_id.') AS flagged')
        // )->where([['course_batch_id',$course_batch_id],['unit_id', $unit_id],['lesson_id',$lesson_id]])->first();
    }
    
    public function courseContentFeedbackStore(Request $request,$course_batch_id){
        $user = config()->get('global.user_id');
        
        if($request['action_type']=='like'){
            $this->gamificationStoreCourse('lld',$user,$course_batch_id,1,$request['unit_id'],$request['lesson_id']);
            return CourseContentUserFeedback::updateOrCreate(
                ['course_batch_id' => $course_batch_id, 'unit_id' => $request['unit_id'] , 'lesson_id' => $request['lesson_id'], 'user_id' => $user],
                ['liked' => $request['liked'], 'disliked' => $request['disliked']]
            );
        }
        elseif($request['action_type']=='dislike'){
            $this->gamificationStoreCourse('lld',$user,$course_batch_id,1,$request['unit_id'],$request['lesson_id']);
            return CourseContentUserFeedback::updateOrCreate(
                ['course_batch_id' => $course_batch_id, 'unit_id' => $request['unit_id'] , 'lesson_id' => $request['lesson_id'], 'user_id' => $user],
                ['liked' => $request['liked'], 'disliked' => $request['disliked']]
            );
        }
        elseif($request['action_type']=='flag'){
            $this->gamificationStoreCourse('fg',$user,$course_batch_id,1,$request['unit_id'],$request['lesson_id']);
            return CourseContentUserFeedback::updateOrCreate(
                ['course_batch_id' => $course_batch_id, 'unit_id' => $request['unit_id'] , 'lesson_id' => $request['lesson_id'], 'user_id' => $user],
                ['flagged' => $request['flagged'] , 'flag_report' => $request['flag_report']]
            );
        }
            
        
        
        //return $user;
        //return $course_batch_id;
        //return $request->all();
    }

    public function search(Request $request){
        //return $request->all();
         $today_date = Carbon::now()->format('Y-m-d');
         $for_web            = ($request->has('for_web'))?null:1;
         $category = ($request->has('id'))?$request['id']:null;
         $search = ($request->has('search'))?$request['search']:null;
         $rating = ($request->has('rating'))?$request['rating']:null;
         // $tag = Tag::where('title','like',"%$search%")->pluck('id')->toArray();

         $status             = ($request->has('status'))?$request['status']:null;
         $owner_id             = ($request->has('owner_id'))?$request['owner_id']:null;
         $limit              = ($request->has('limit'))?$request['limit']:15;
        if($request['type']=='all'){
            $course = CourseBatch::select("course_batches.*")->join('courses','courses.id','course_batches.course_id')
           ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')->where('published_status', 1)
           ->where('courses_for_status',0)
           ->groupBy('course_id');})
           ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
           ->where(function($q) use($today_date){
                            $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
                        })
           ->orderBy('id', 'DESC')
           ->paginate($limit);        
            return BatchBasicResource::collection($course);
        }

        if($request['type']=='favorite'){
            //return $today_date;
            $course = CourseBatch::where('course_batches.published_status', 1)
            ->Join('courses','courses.id','course_batches.course_id')
            ->select('course_batches.*')
            // ->where('courses_for_status',0)
            // ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            // ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')
            // ->groupBy('course_batches.course_id');})
            // ->when($category, function($q) use($category){return $q->where('courses.cat_id',$category);})
            // ->where(function($q) use($today_date){
            //                 $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
            //             })
            // ->orderBy('enroll', 'DESC')
            // ->orderBy('rating_sum', 'DESC')
            ->paginate($limit);
            //return response()->json(['dd'=>$course]);
            //return ResourceBatch::collection($course);
            return BatchBasicResource::collection($course);
        }
        if($request['type']=='running'){
            //return $today_date;
            $course = CourseBatch::where('course_batches.published_status', 1)
            // ->where('course_batches.courses_for_status',0)
            
            ->where('courses_for_status',0)
            ->select('course_batches.*')
            ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')
            ->groupBy('course_batches.course_id');})
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            // ->where(function($q) use($today_date){
            //                 $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
            //             })
            ->orderBy('enroll', 'DESC')
            ->orderBy('rating_sum', 'DESC')
            ->paginate($limit);
            return BatchBasicResource::collection($course);
        }
        if($request['type']=='end'){
            $course = CourseBatch::where('published_status', 1)
            ->whereIn('created_at',function($query){$query->select(DB::raw('max(created_at)'))->from('course_batches')
                          ->groupBy('course_id');})
            ->when($status, function($q) use($status){return $q->where('status',$status);})
            ->where('courses_for_status',0)
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->whereNotNull('end_date')
             ->where(function($q) use($today_date){
                                $q->where('end_date','<=',$today_date);
                            })
            ->orderBy('created_at','DESC')->paginate($limit);
            return BatchBasicResource::collection($course);     
        }
        if($request['type']=='upcomming'){
            $course = CourseBatch::where('published_status', 1)
            ->whereIn('created_at',function($query){$query->select(DB::raw('max(created_at)'))->from('course_batches')
                          ->groupBy('course_id');})
            ->when($status, function($q) use($status){return $q->where('status',$status);})
            ->where('courses_for_status',0)
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->whereNotNull('end_date')
             ->where(function($q) use($today_date){
                                $q->where('start_date','>',new DateTime());
                            })
            ->orderBy('created_at','DESC')->paginate($limit);
            //return ResourceBatch::collection($course);
            return BatchBasicResource::collection($course);     
        }
        if($request['type']=='admin_featured'){
            $course = CourseBatch::where('published_status', 1)
            ->when($status, function($q) use($status){return $q->where('status',$status);})
            ->where('courses_for_status',0)
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->whereIn('created_at',function($query){$query->select(DB::raw('max(created_at)'))->from('course_batches')
                          ->groupBy('course_id');})
            ->when($owner_id, function($q) use($owner_id){return $q->where('owner_id' , $owner_id);})
            ->where('admin_featured',1)
            ->orderBy('created_at','DESC')->paginate($limit);
            return BatchBasicResource::collection($course);     
        }

       

        if($request['type']=='tag'){
            $tag_id = ($request->has('id'))?$request['id']:null;
            $course = CourseBatch::select("course_batches.*")->join('courses','courses.id','course_batches.course_id')
           ->leftjoin('course_tags','course_tags.course_id','courses.id')
           ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')->where('published_status', 1)
           ->where('courses_for_status',0)
           ->groupBy('course_id');})
           ->where('course_tags.tag_id',$tag_id)
           ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
           ->where(function($q) use($today_date){
                            $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
                        })
           ->orderBy('id', 'DESC')
           ->paginate(15);        
            return BatchBasicResource::collection($course);
        }

      
        if(is_null($search) && is_null($category)){
            $course = CourseBatch::where('id',0)->paginate(15);
            return BatchBasicResource::collection($course);
        }
        if(empty($tag)){
            $tag=null;
        }
        if($category==1){
            $course = CourseBatch::select("course_batches.*")
            ->leftJoin('courses','courses.id','course_batches.course_id')
            ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')->where('course_batches.published_status',1)
            ->groupBy('course_id');})
            ->where('courses_for_status',0)
            ->where(function($q) use($today_date){
                                $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
                            })
            ->when($category, function($q) use($category){return $q->where('courses.cat_id',$category);})
            ->when($search, function($q) use($search){return $q->where('course_batches.course_alias_name','like',"%$search%")->orWhere([['course_batches.course_alias_name_en','like',"%$search%"],['course_batches.published_status',1]]);})
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->orderBy('id','DESC')
            ->paginate($limit);        
            return BatchBasicResource::collection($course); 
        }
        $course = CourseBatch::select("course_batches.*")
            ->leftJoin('courses','courses.id','course_batches.course_id')
            ->whereIn('course_batches.id',function($query){$query->select(DB::raw('max(course_batches.id)'))->from('course_batches')->where('course_batches.published_status',1)
            ->groupBy('course_id');})
            ->where('courses_for_status',0)
            ->where(function($q) use($today_date){
                                $q->where('course_batches.end_date','>=',$today_date)->orWhereNull('course_batches.end_date');
                            })
            ->when($category, function($q) use($category){return $q->where('courses.cat_id',$category);})
            ->when($search, function($q) use($search,$category){
                return $q->where(function($qr) use($search,$category){
                    if($category!=null){
                        $qr->where([['course_batches.course_alias_name','like',"%$search%"],['courses.cat_id',$category]])
                    ->orWhere([['course_batches.course_alias_name_en','like',"%$search%"],['course_batches.published_status',1],['course_batches.courses_for_status',0],['courses.cat_id',$category]]);
                    }else{
                        $qr->where('course_batches.course_alias_name','like',"%$search%")
                    ->orWhere([['course_batches.course_alias_name_en','like',"%$search%"],['course_batches.published_status',1],['course_batches.courses_for_status',0]]);
                    }
                    
                });
            })
            ->when($for_web, function($q){ return $q->where('enrollement_validation_status', 0); })
            ->orderBy('id','DESC')
            ->paginate($limit);        
            return BatchBasicResource::collection($course);
        
    }

    public function content(Request $request, $id){
         $datetime = gmdate("Y-m-d H:i:s",strtotime('+6 Hours'));

            $res = Syllabus::where('id',$id)->first();

            if(!$res || $res->learning_content_id==null){
                return response()->json(
                    [ 
                        'message' => 'No contents found',
                        'data' => [],
                        'status'=> false
                    ]
                );
            }
    
            $content = LearningContent::where('id',$res->learning_content_id)->first();
            if($content){
            $content['start_date'] = $res->start_date;
            $content['end_date'] = $res->end_date;
            $content['current_date_time'] = $datetime;
            $content['syllabus_id'] = $res->id;
            $content['suggested_lesson'] = $res->suggested_lesson;
            $content['completeness'] = $res->completeness;
            $content['UserFeedback'] = $res->UserFeedback;

            
            if($content->content_type==!"exam" || $content->content_type==!"quiz" || $content->content_type==!"assignment"){
                $data['data'] = $content;
                return response()->json($data);
            }


            return new LearnerResource($content);
        }    
    }

    public function syllabusSubmission(Request $request){


        $data = $request->all();

        $res = Syllabus::where('id',$request->syllabus_id)->first();
            
          if($res){
            $content = LearningContent::where('id',$res->learning_content_id)->first();
            $carry = json_decode($content->quiz_marks);
            // $carry = $data['quiz_data'];

            $ids = $request->ques_ids;
            $ids = json_decode($ids);

            if($ids!=''){
                  if(sizeof($ids)>1){
                    $ids_ordered = implode(',', $ids);
                  }else{
                    $ids_ordered = $ids[0];
                  }
                 
            
                $quiz = Question::wherein('id',$ids)
                        ->orderByRaw("FIELD(id, $ids_ordered)")
                        ->get();
                $answer = [];
                $ids = [];
    
               
                foreach ($quiz as $key => $value) {
    
                    array_push($ids,$value->id);
                    $answer[$value->id] = json_decode($value->answer);
    
                    //array_push($answer, json_decode($value->answer));
                }
            }else{
                $quiz='';
                $answer = [];
                $ids = [];
            }
            $count = 0;
            $mark = 0;

            $batch_id = Syllabus::where('id',$request->syllabus_id)->value('course_batch_id');

            $Syllabus = Syllabus::where('id',$request->syllabus_id)->with('contents')->first();

            $enroll_id = CourseEnrollment::join('orders as o','o.id','course_enrollments.order_id')
                    ->where('o.user_id',config()->get('global.user_id'))
                    ->where('course_enrollments.course_batch_id',$batch_id)
                    ->value('course_enrollments.id');
           // return $enroll_id;
           
            $attempt = isset(json_decode($content->more_data_info)->attempt)?json_decode($content->more_data_info)->attempt:1;


            $check = SyllabusStatus::where('course_enrollment_id',$enroll_id)
                        ->where('course_batch_id',$batch_id)
                        ->where('syllabus_id',$request->syllabus_id)
                        ->first();
             $totalmark= 0;
             $passmark= 0;
             if($Syllabus && $Syllabus->contents){
             	if(isset(json_decode($Syllabus->contents->more_data_info)->total_mark)){
             		$totalmark = json_decode($Syllabus->contents->more_data_info)->total_mark;
             	}

             	if(isset(json_decode($Syllabus->contents->more_data_info)->pass_mark)){
             		$passmark = json_decode($Syllabus->contents->more_data_info)->pass_mark;
             	}
             	
             }
            
            if($check){
                if($res->content_type=='assignment' || $res->content_type=='exam' ||$res->content_type=='quiz'){
                    if(($attempt>=$check->attempt) || ($attempt+$check->extra_attempt)>=($check->attempt+$check->extra_attempt)){
                        
                        if($request->marks>$check->marks){
                            $check->answers = isset($request->answers)?$request->answers:null;
                            $check->submission_time = isset($request->submission_time)?$request->submission_time:null;
                            $check->ques_ids = isset($request->ques_ids)?$request->ques_ids:null;
                            $check->mark = isset($request->marks)?$request->marks:null;
                            $check->attempt = $check->attempt+1;
                            $check->file_id =  isset($request->file_id)?$request->file_id:null;
                            $check->total_marks =  $totalmark;
                            $check->pass_mark =  $passmark;
                        }else{
                            $check->attempt = $check->attempt+1;
                            $check->submission_time = isset($request->submission_time)?$request->submission_time:null;
                        } 
                        $check->update();
                    }else{
                        return response()->json(['message' => 'You dont have any attempt remaining','status'=>false]);
                    }
                }else{
                    
                    if($quiz==''){
                        
                        
                    }else{
                            $check->answers = $request->answers;
                            $check->ques_ids = $request->ques_ids;
                            $check->mark = $request->marks;
                            $check->completeness = $request->completeness;
                            $check->attempt = $check->attempt+1;
                            $check->total_marks =  $totalmark;
                            $check->pass_mark =  $passmark;
                            if(isset($request->submission_time) && $request->submission_time!=0){
                                $check->submission_time = isset($request->submission_time)?$request->submission_time:null;
                            }
                           
                            $check->update();
                    }
                }
                

            }else{
                $submit = new SyllabusStatus;

                $file_info = json_decode($request->answers);

                $submit->course_enrollment_id = $enroll_id;
                $submit->course_batch_id = $batch_id;
                $submit->syllabus_id = isset($request->syllabus_id)?$request->syllabus_id:null;
                $submit->submission_time = isset($request->submission_time)?$request->submission_time:null;
                $submit->answers = isset($request->answers)?$request->answers:null;
                $submit->feedback_arr = isset($request->feedback_arr)?$request->feedback_arr:null;
                $submit->ques_ids = json_encode($ids);
                $submit->file_id =  isset($request->file_id)?$request->file_id:null;
                $submit->obtain_marks = isset($request->obtain_marks)?$request->obtain_marks:null;
                $submit->status = $request->status;
                $submit->attempt = 1;
                $submit->total_marks =  $totalmark;
                $submit->pass_mark =  $passmark;
                $submit->mark = isset($request->marks)?$request->marks:null;
                $submit->save();
            }


            return response()->json(['message' => 'submitted successfully','status'=>true]);
          }else{
            return response()->json(['message' => 'Not submitted successfully','status'=>false]);
          }
            
    }

    public function Completeness(Request $request){
        $user_id = $user = config()->get('global.user_id');
        $enroll_id = ($request->has('enroll_id'))?$request['enroll_id']:null;
        $unit_id = ($request->has('unit_id'))?$request['unit_id']:null;
        $lesson_id = ($request->has('lesson_id'))?$request['lesson_id']:null;
        $enroll_completeness = ($request->has('enroll_completeness'))?$request['enroll_completeness']:0;
        $unit_completeness = ($request->has('unit_completeness'))?$request['unit_completeness']:0;
        $lesson_completeness = ($request->has('lesson_completeness'))?$request['lesson_completeness']:0;
        $check = Order::where('orders.user_id',$user_id)->join('course_enrollments','course_enrollments.order_id','orders.id')->where('course_enrollments.id',$enroll_id)->first();
        
        if($check){
            $enroll = CourseEnrollment::find($enroll_id);
            $enroll->course_completeness=$enroll_completeness;
            $enroll->update();
        
            $unit = Completeness::where('syllabus_id',$unit_id)->where('enroll_id',$enroll_id)->where('user_id',$user_id)->orderBy('completeness','DESC')->first();
            if($unit){
                $unit->completeness = $unit_completeness;
                $unit->update();
            }else{
                $unit = new Completeness;
                $unit->syllabus_id = $unit_id;
                $unit->completeness = $unit_completeness;
                $unit->enroll_id = $enroll_id;
                $unit->user_id = $user_id;
                $unit->save();
            }
            $lesson = Completeness::where('syllabus_id',$lesson_id)->where('enroll_id',$enroll_id)->where('user_id',$user_id)->orderBy('completeness','DESC')->first();
            if($lesson){
                $lesson->completeness = $lesson_completeness;
                $lesson->update();
            }else{
                $lesson = new Completeness;
                $lesson->syllabus_id = $lesson_id;
                $lesson->completeness = $lesson_completeness;
                $lesson->enroll_id = $enroll_id;
                $lesson->user_id = $user_id;
                $lesson->save();
            }

            if($lesson_completeness==100){
                $lesson = Syllabus::where('id',$request->lesson_id)->first();
                if($lesson){
                    $this->gamificationStoreCourse($lesson->content_type,$user_id,$enroll_id,1,$request['unit_id'],$request['lesson_id']);
                }
            }
            if($unit_completeness==100){
                $unit = Syllabus::where('id',$request->unit_id)->first();
                if($unit){
                    $this->gamificationStoreCourse($unit->content_type,$user_id,$enroll_id,1,$request['unit_id'],$request['lesson_id']);
                }
            }

            return response()->json(['message' => 'Submitted successfully']);
        }else{
            return response()->json(['message' => 'Enrollment not found']);
        }
    }
    
    public function loadOldCourses(Request $request){
        //return $request->all();
        
        $qry = 'SELECT co.*, cou.is_complete FROM course_old_user AS cou
                LEFT JOIN course_old AS co ON cou.course_id = co.id
                WHERE cou.user_id='.$request['old_user_id'];
        $getData = DB::select($qry);
        return $getData;
    }

    public function gamificationcheck(){
        return $this->gamificationStore('enr',100,1,1);
    }
}
