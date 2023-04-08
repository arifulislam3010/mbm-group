<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use App\Mail\MailSender;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Muktopaath\Course\Models\Course\Course;
use Muktopaath\Course\Models\Course\CourseBatch;
use Muktopaath\Course\Models\Course\CertificateSubmit;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\RestrictedUser;
use App\Models\AdminSettings\Tag;
use App\Models\Myaccount\User;
use App\Models\Myaccount\InstitutionInfo;
use App\Models\CourseTag;
use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Lib\ManualEncodeDecode;
use Muktopaath\Course\Lib\CourseEnrollment as CourseEnrollmentTrait;
use Muktopaath\Course\Http\Resources\DetailsResource;
use Muktopaath\Course\Exports\TransactionReport;
Use \Carbon\Carbon;
use Validator;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;
use DB;
use App\Lib\SMS;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use Maatwebsite\Excel\Facades\Excel;
use Muktopaath\Course\Models\Course\CertificateTemplate;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;
use Muktopaath\Course\Http\Resources\Batch as ResourceBatch;
class CourseBatchController extends Controller
{    
    use CourseEnrollmentTrait;
    
    public function index($id){
        
        $batches = CourseBatch::where('id',$id)->with('course','certificate')->first();
        if($batches->certificate  && isset($batches->certificate->more_data)){
            $batches->certificate->more_data = json_decode($batches->certificate->more_data);
        }
        
        return response()->json($batches);
    }


    public function find($id){
        
        $batch = CourseBatch::with('course')->findOrFail($id);
        $batch->course->tag = json_decode($batch->course->tag);
        return new ResourceBatch($batch);
        return response()->json($batch);
    }

    public function add(Request $request,$id){
        $messsages = array(
            'required'=>'e_required',
            'title' => 'title'
        );

        $rules = array(
                'title'             => 'required',
            );

       $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }

        $user_id = config()->get('global.user_id');
        $owner_id = config()->get('global.owner_id');
        
        $batch = new CourseBatch;
        $batch->title      = $request->title;
        $batch->course_id  = $id;

        $batch->details                  = $request->details;
        $batch->objective                = $request->objective;
        $batch->requirement              = "<p>&nbsp;</p>";
        $batch->featured                 = 0;
        $batch->course_alias_name        = $request->title;
        $batch->certificate_alias_name   = $request->title;
        $batch->syllabus                 = '{"0":{"lessons":[{"id":1,"name":"New Lesson 1","order":1,"fixed":false}]},"units":[{"id":1,"name":"New Unit 1","order":1,"fixed":false}],"study_mode":"0"}';
        $batch->course_requirment        = '[{"info":"","attach":false}]';
        $batch->marks                    = '{"content":{"marks":"0","pass_marks":0},"exam":{"marks":"0","pass_marks":0},"quiz":{"marks":"0","pass_marks":0},"assignment":{"marks":"0","pass_marks":0}}';
        $batch->created_by               = $user_id;
        $batch->updated_by               = $user_id;
        $batch->owner_id                 = $owner_id;
        $batch->save();

        $batch->course = Course::findOrFail($id);

        return response()->json($batch);
    }
    

   public function update(Request $request){

        $messsages = array(
            'required'=>'e_required',
            'id' => 'id',
            'request_type'  => 'request_type'
        );

        $rules = array(
                'id'             => 'required',
                'request_type'  => 'required'
            );

       $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }
        // return $request->cat_id;
        $update_others = CourseBatch::find($request->id);
        $course = Course::find($update_others->course_id);

        if($request->request_type == 'course-info'){

            // if($course->course_code != $request->course_code){
            //     $this->validate($request, [
            //         'title'             => 'required',
            //         'cat_id'            => 'required',
            //         // 'objective'         => 'required',
            //         // 'course_code'       => 'required|unique:courses',
            //     ]);
            // }
            // else{
            //     $this->validate($request, [
            //         'title'             => 'required',
            //         'cat_id'            => 'required',
            //         'objective'         => 'required',
            //         // 'course_code'       => 'required',
            //     ]);
            // }

            $course->title                  = $request->title;
            $course->bn_title               = $request->bn_title;
            $course->cat_id                 = $request->cat_id;
            $course->description            = $request->description;
            $course->objective              = $request->objective;
            if($request->course_cb_id){
                $course->course_cb_id           = $request->course_cb_id;
            }
            if($request->course_video_id){
                $course->course_video_id        = $request->course_video_id;
            }
            

            $course->course_level           = $request->course_level;
            $course->language_id            = $request->language_id;

            if($request->course_cb_id){
                $course->course_cb_id           = $request->course_cb_id;
            }
            if($request->course_video_id){
                $course->course_video_id        = $request->course_video_id;
            }

            $course->learning_outcomes      = json_encode($request->learning_outcomes);
            $course->requirement            = json_encode($request->requirement);
            $course->course_motto           = $request->course_motto;

            $course->update();

            $tags = isset($request->tags)?$request->tags:[];

            if($request->newtags){
                foreach ($request->newtags as $value) {
                    if(!Tag::where('title',$value)->first()){
                        $tag = new Tag;
                        $tag->title = $value;
                        $tag->save();

                        array_push($tags,$tag->id);
                }

                    //array_push($tags,$tag->id);
                }

            }
            if(isset($request->tags)){
                $course->tags()->sync($tags);
            }

            // $update_others->title_content_url = $request->title_content_url;
            $update_others->update();
            

        }else if($request->request_type == 'published'){
            $this->validate($request,[
              //  'study_mode'             => 'required', 
                // 'courses_for_status'     => 'required', 

           ]);

           if(isset($request->status_update)){
                if($request->status_update=='published'){
                    if($update_others->published==1){
                        $update_others->published=0;
                    }else{
                        $update_others->published=1;
                        $update_others->admin_published_date=Carbon::now()->format('Y-m-d H:i:s');
                    }
                }
                if($request->status_update=='featured'){
                    if($update_others->admin_featured==1){
                        $update_others->admin_featured=0;
                    }else{
                        $update_others->admin_featured=1;
                        $update_others->admin_featured=1;
                    }
                }
           }else{
                $update_others->courses_for_status          = $request->courses_for_status;
                $update_others->published_status            = $request->published_status;
                if($request->published_status==1){
                    $update_others->published_date=Carbon::now()->format('Y-m-d H:i:s');
                }
                $update_others->featured                           = $request->featured;
                
                
                if($request->courses_for_status == 1 && $update_others->restricted_access_code == null)
                    $update_others->restricted_access_code = Str::random(15);
           }
           
            $update_others->update();
            $course->update();
        }
        else if($request->request_type == 'batch-settings'){

            
            $update_others->code                    = $request->code;
            $update_others->title                   = $request->title;
            $update_others->bn_title                = $request->bn_title;
            $update_others->course_alias_name       = $request->course_alias_name;
            $update_others->certificate_alias_name  = $request->certificate_alias_name;
            $update_others->course_alias_name       = $request->course_alias_name;
            $update_others->course_alias_name_en    = $request->course_alias_name_en;
            $update_others->duration                = $request->duration;
            $update_others->duration_type           = $request->duration_type;


            $update_others->admission_status        = $request->admission_status;
            $update_others->enrolment_approval_status   = $request->enrolment_approval_status;
            $update_others->enrollment_validation_status   = $request->enrollment_validation_status;
            $update_others->study_mode                  = $request->study_mode;
            $update_others->course_requirment           = $request->course_requirment;
            
            if($request->admission_status == 1){
                $update_others->reg_start_date      = null;
                $update_others->reg_end_date        = null;
                $update_others->enroll_limit        = 0;
                $update_others->start_date          = null;
                $update_others->end_date            = null;
            }
            else if($request->admission_status == 0){
                $update_others->reg_start_date      = $request->reg_start_date;
                $update_others->reg_end_date        = $request->reg_end_date;
                $update_others->enroll_limit        = $request->limit;
                $update_others->start_date          = $request->start_date;
                $update_others->end_date            = $request->end_date;
            }

            $update_others->marks        = $request->marks;
            $crdata = $request->all();
            if($crdata['certificate']){
               if(isset($crdata['certificate'])!=null){
                $cfdata = CertificateTemplate::where('course_id',$request->id)->first();
                if($cfdata){
                    $cdata = CertificateTemplate::find($cfdata->id);
                }else{
                    $cdata = new CertificateTemplate;
                }
                
                $cdata->certificate_intro        = $crdata['certificate']['certificate_intro'];
                $cdata->text_before_student_name = $crdata['certificate']['text_before_student_name'];
                $cdata->text_before_course_name  = $crdata['certificate']['text_before_course_name'];
                $cdata->text_for_held_on         = $crdata['certificate']['text_for_held_on'];
                $cdata->background_id            = isset($crdata['certificate']['background_id'])?$crdata['certificate']['background_id']:null;
                $cdata->text_for_obtain_grade    = $crdata['certificate']['text_for_obtain_grade'];
                $cdata->start_date               = $crdata['certificate']['start_date']?$crdata['certificate']['start_date']:null;
                $cdata->course_id               =  $request->id;
                $cdata->end_date                 = $crdata['certificate']['end_date']?$crdata['certificate']['end_date']:null;
                $cdata->certificate_intro_status           = $crdata['certificate']['certificate_intro_status'];
                $cdata->text_before_student_name_status    = $crdata['certificate']['text_before_student_name_status'];
                $cdata->text_before_course_name_status     = $crdata['certificate']['text_before_course_name_status'];
                $cdata->text_for_held_on_status            = $crdata['certificate']['text_for_held_on_status'];
                $cdata->text_for_obtain_grade_status       = $crdata['certificate']['text_for_obtain_grade_status'];
                $cdata->certificate_name_status            = $crdata['certificate']['certificate_name_status'];
                $cdata->certificate_name           = $crdata['certificate']['certificate_name'];
                $cdata->barcode_position_status            = $crdata['certificate']['barcode_position_status']?$crdata['certificate']['barcode_position_status']:null;
                $cdata->barcode_position            = $crdata['certificate']['barcode_position']?$crdata['certificate']['barcode_position']:null;
                $cdata->date_status                 = $crdata['certificate']['date_status'];
                $cdata->certificate_created_date_status    = $crdata['certificate']['certificate_created_date_status'];
                
                $cdata->more_data                = json_encode($crdata['certificate']['more_data']);
                $cdata->owner_id                 = config()->get('global.owner_id');
                $cdata->created_by               = config()->get('global.user_id');
                // return $cdata;
                if($cfdata){
                    
                    $cdata->update();
                }else{
                
                    $cdata->save();
                }
                // $update_others->certificate_id              = $cdata->id;
               }
                
                
            }

            
            // $update_others->certificate_alias_name      = $request->certificate_alias_name;
            $update_others->certificate_approval_status = $request->certificate_approval_status;
            if($request->certificate_approval_status == 0)
                $update_others->certificate_approval_date   = null;
            else if($request->certificate_approval_status == 1)
                $update_others->certificate_approval_date   = $request->certificate_approval_date;

    
            if($request->payment_status == 0){
                $update_others->payment_point_status = null;
                $update_others->payment_point_amount = null;
                $update_others->discount_status = null;
                $update_others->discount_type = null;
                $update_others->discount_amount = null;
                $update_others->discount_expire_date = null;
            }else{

                $update_others->payment_status       = $request->payment_status;
                $update_others->payment_point_status = $request->payment_point_status;
                $update_others->payment_point_amount = $request->payment_point_amount;
                $update_others->discount_status      = $request->discount_status;
                $update_others->discount_type        = $request->discount_type;
                $update_others->discount_amount      = $request->discount_amount;
                $update_others->discount_expire_date = $request->discount_expire_date;


                $update_others->trainee_transaction_type = $request->trainee_transaction_type;
                $update_others->trainee_pay_type = $request->trainee_pay_type;
                $update_others->trainee_amount = $request->trainee_amount;

                $update_others->trainer_transaction_type = $request->trainer_transaction_type;
                $update_others->trainer_pay_type = $request->trainer_pay_type;
                $update_others->trainer_amount = $request->trainer_amount;
            }
            $update_others->update();
        }

        $data = CourseBatch::where('id',$update_others->id)->with('course','certificate')->first();
        $this->CourseSolrDataUpdate();
        return response()->json(['data'=> $data,
            'message' => '']);
    }

    public function clone($id){

        DB::beginTransaction();

        try {
            $current_batch = CourseBatch::find($id);
            if($current_batch->courses_for_status == 1 && $current_batch->restricted_access_code != null)
                $current_batch->restricted_access_code = Str::random(15);
            $current_batch->uuid   = Str::orderedUuid();
            $cloned_batch =  $current_batch->replicate();
            $cloned_batch->push();

            $current_syllabuses = Syllabus::where('course_batch_id',$current_batch->id)->get();

            foreach ($current_syllabuses as $key => $value) {
                $value->course_batch_id = $cloned_batch->id;
                $cloned_syllabus = $value->replicate();
                $cloned_syllabus->push();

            }

            DB::commit();
            $batch = CourseBatch::select('course_batches.course_id','course_batches.owner_id','course_batches.id','course_batches.uuid','course_batches.title','course_batches.published_status','course_batches.course_alias_name')
                    ->join('courses','courses.id','course_batches.course_id')
                    ->where('course_batches.id',$cloned_batch->id)
                    ->with('course','owner')
                    ->withCount('lessons','certificates','reviewals')->first();

            return response()->json([
                'data' => $batch,
                'message' => 'Course batch cloned successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
        }

    }

    public function featured_orderise(Request $request){
        
        $batch = CourseBatch::find($request['batch_id']);
        $batch->feature_order = $request['order'];
        $batch->update();

        return response()->json(['message' => 'orderised', 'data' => $batch]);
    }

    public function getRestrictedUser(Request $request,$id){
        $user_type = $request->user_type;

        $myaccount = config()->get('database.connections.my-account.database');

        $restrictedUsers = RestrictedUser::Select('restricted_users.*','users.name')->where('batch_id',$id)
                        ->where('user_type',$user_type)
                        ->leftJoin($myaccount.'.users', function($join){
                            $join->on('users.email', '=', 'restricted_users.email_or_phone')
                            ->orOn('users.phone', '=', 'restricted_users.email_or_phone');
                        })
                        ->with('participant_type','info')
                        ->orderBy('restricted_users.id','DESC')
                        ->paginate(1000);
        //$users = RestrictedUser::where('batch_id',$id)->where('user_type',$user_type)->orderBy('id','DESC')->paginate(1000);

        return response()->json($restrictedUsers,200);
    }

    public function addCsvRestrictedUser(Request $request){
        $info = new ManualEncodeDecode();
        $messsages = array(
            'required'=>'e_required',
            'batch_id' => 'batch_id',
            'data'   => 'data'
        );

        $rules = array(
                'batch_id'             => 'required',
                'data'                 => 'required'
            );

       $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }
        
        $user_data = [];
        $batch_id = $request->batch_id;
        $user_type = $request->user_type;
        $batch = CourseBatch::findOrFail($batch_id);
        $participant_type_id = isset($request->participant_type_id)?$request->participant_type_id:null;
        $email_or_phonearray = [];
        
        
        $qryStr = 'INSERT INTO `restricted_users` (
        name,email_or_phone,type,batch_id,participant_type_id,restricted_code,user_type) VALUES';
            foreach ($request->data as $key => $value) {
                $email_or_phone=$value['email'] ? $value['email']:$value['phone'];
                $type = filter_var($value['email'], FILTER_VALIDATE_EMAIL)?'email':'phone';
                $email_or_phone = preg_replace('/\s+/',' ', $email_or_phone);

              
                $restricted_access_code = $batch->restricted_access_code;
                
                $token = $info->encode($email_or_phone . '<:MP:>' . $restricted_access_code, env('ENCRIPTION_KEY'));
                $front_url = config()->get('global.front_url');
                $link = $front_url.'join/course?token='.$token.'&type=2';

                
                $data = [
                    'subject' => 'Invitation from muktopaath',
                    'short_name' => 'Muktopaath',
                    'body' => 'You are invited from muktopaath for joining '.$batch->course_alias_name.' For join click the link </br>'.$link,
                    'to'   => $email_or_phone,
                    'link' => $link,
                    'course_name' => $batch->course_alias_name,
                    'message' => 'Dear user! You have been invited from muktopaath to join "' .$batch->course_alias_name. '" . To join click on the following link '.$link,
                    'template' => 'courseinvite'
                ];
        
                if(is_numeric($email_or_phone)){
                    dispatch(new SendSmsJob($data));
                }else{
                    Mail::to($data['to'])->send(new MailSender($data));
                }
                
                if($key>0) $qryStr .= ',';
                $qryStr .= '("'.$value['name'].'","'.$email_or_phone.'","'.$type.'","'.$batch_id.'","'.$participant_type_id.'","'.$batch->restricted_access_code.'","'.$user_type.'")';
            }
        $qryStr .= ' ON DUPLICATE KEY UPDATE updated_at=VALUES(updated_at)';
            
        DB::select($qryStr);
        
        
        $users = RestrictedUser::where('batch_id',$batch_id)->where('user_type',$user_type)->orderBy('id','DESC')->paginate(10);
        return response()->json($users,200);
    }
    
    public function addRestrictedUser(Request $request){

        $messsages = array(
            'required'=>'e_required',
            'batch_id' => 'batch_id',
            'name'  => 'name',
            'email_or_phone'    => 'email_or_phone'
        );

        $rules = array(
                'batch_id'      => 'required',
                'name'          => 'required',
                'email_or_phone' => 'required'
            );

       $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }

        $batch_id = $request->batch_id;

        $batch = CourseBatch::find($batch_id);

        $user = new RestrictedUser;

        $user->name            = $request->name;
        $user->email_or_phone  = $request->email_or_phone;
        $user->type            = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)?'email':'phone';
        $user->batch_id        = $batch_id;
        $user->restricted_code = 1234;

        if($user->save()){
            return response()->json([
                'data' => $user,
                'message' => 'Sucessfully added'
            ],201);
        }
        else{
           return response()->json([
                'message' => 'Something wrong'
            ],401); 
        }
    }

    public function updateRestrictedUser(Request $request){

        $messsages = array(
            'required'=>'e_required',
            'id' => 'id',
            'name'  => 'name',
            'email_or_phone' => 'email_or_phone'
        );

        $rules = array(
                'id'      => 'required',
                'email_or_phone' => 'required',
                'name' => 'required'
            );

       $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }
       
        $user = RestrictedUser::find($request->id);
        $user->name            = $request->name;
        $user->email_or_phone  = $request->email_or_phone;
        $user->type            = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)?'email':'phone';
        return $user;
        if($user->update()){
            return response()->json([
                'data' => $user,
                'message' => 'Sucessfully updated'
            ],201);
        }
        else{
           return response()->json([
                'message' => 'Something wrong'
            ],401); 
        }
    }

    public function deleteRestrictedUser($id){

        $restrictedUser = RestrictedUser::where('id', $id)
            ->first();
          
        if(empty($restrictedUser)){
            return response()->json(['message' => 'restricted user not found'],404);
        }
        else{
            
            $user = User::where('email', $restrictedUser->email_or_phone)
            ->orWhere('phone', $restrictedUser->email_or_phone)
            ->value('id');

            $order_number = CourseEnrollment::join('orders', 'course_enrollments.order_id', '=', 'orders.id')
            ->where('orders.user_id', $user)
            ->where('course_enrollments.course_batch_id', '=', $restrictedUser->batch_id)
            ->value('order_id');
            
            $enroll = CourseEnrollment::where('order_id', $order_number)
                ->value('id');
            $removeEnroll = CourseEnrollment::find($enroll);
            
            $removeRestricUser = RestrictedUser::find($id);
            if($removeRestricUser){
                $removeRestricUser->delete();
            }
            if($removeEnroll){
                $removeEnroll->delete();
                
                return response()->json([
                    'data' => $removeEnroll,
                    'message' =>'successfully deleted enrollment'
                ],200);
            }else{
                return response()->json(['message' => 'Deleted but enrollment not deleted'],200);
            }
        }
        
    }

    public function destroy($id){
        $batch = CourseBatch::findOrFail($id);

        if($batch->delete()){
            return response()->json([
                'data'    => $batch,
                'message' => 'Sucessfully Deleted'
            ],200);
        }
        else{
           return response()->json([
                'message' => 'Something wrong'
            ],401); 
        }
        
    }

    public function publishFeature(Request $request){
        $messsages = array(
            'required'=>'e_required',
            'id' => 'id',
            'type'  => 'type'
        );

        $rules = array(
                'id'             => 'required',
                'type'           => 'required'
            );

       $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }

        $batch = CourseBatch::find($request->id);

        if($request->type == 'published'){
            $batch->published = !$batch->published;
        }

        else if($request->type == 'featured'){
            $batch->featured = !$batch->featured;
        }

        $batch->update();

        return response()->json($batch,200);
    }

    public function edit_name(Request $request){

        $messsages = array(
            'required'=>'e_required'
        );

        $rules = array(
                'id'      => 'required',
                'name'    => 'required'
            );

       $validator = Validator::make($request->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }

        $res = RestrictedUser::find($request['id']);
        $res->name  = $request['name'];
        $res->update();


        return response()->json(['message' => 'name updated','data' => $res]);
    }

    public function enrolled_users($batch_id){

        $db = config()->get('database.connections.my-account.database');  

        $perPage = 10;

        $res = CourseEnrollment::select('course_enrollments.id as enrollment_id','u.name','cb.course_requirment','u.email','u.phone','course_enrollments.created_at as enrolled_at','course_enrollments.course_completeness','course_enrollments.status','course_enrollments.id','u.id as user_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('orders as o','o.id','course_enrollments.order_id')
                ->join($db.'.users as u','u.id','o.user_id')
                ->where('course_enrollments.course_batch_id',$batch_id)
                ->orderBy('course_enrollments.id','DESC')
                ->with('attachments')
                ->paginate($perPage / 2);

        $enroll_ids = CourseEnrollment::join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('orders as o','o.id','course_enrollments.order_id')
                ->join($db.'.users as u','u.id','o.user_id')
                ->where('course_enrollments.course_batch_id',$batch_id)
                ->orderBy('course_enrollments.id','DESC')
                ->pluck('u.id');


        $restrictedUsers = RestrictedUser::select('id as private_id','name','email_or_phone','batch_id','participant_type_id','user_type','user_id','type','created_at')
            ->where('batch_id',$batch_id)
            ->whereNotIn('user_id', $enroll_ids)
            ->paginate($perPage - count($res));

        $result = $this->mergeit($res,$restrictedUsers);

        return response()->json($result); 
    }

    public function idcards($batch_id){
        $db = config()->get('database.connections.my-account.database');  

        $ins = InstitutionInfo::select('institution_name')->where('id',2)->first();
        $batch = CourseBatch::where('id',$batch_id)->value('course_alias_name');


        $res = CourseEnrollment::select('course_enrollments.id as enrollment_id','u.name','cb.course_requirment','u.email','u.phone','course_enrollments.created_at as enrolled_at','course_enrollments.course_completeness','course_enrollments.status','course_enrollments.id','u.id as user_id')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('orders as o','o.id','course_enrollments.order_id')
                ->join($db.'.users as u','u.id','o.user_id')
                ->where('course_enrollments.course_batch_id',$batch_id)
                ->orderBy('course_enrollments.id','DESC')
                ->with('attachments')
                ->get();

        $enroll_ids = CourseEnrollment::join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('orders as o','o.id','course_enrollments.order_id')
                ->join($db.'.users as u','u.id','o.user_id')
                ->where('course_enrollments.course_batch_id',$batch_id)
                ->orderBy('course_enrollments.id','DESC')
                ->pluck('u.id');


        $restrictedUsers = RestrictedUser::select('id as private_id','name','email_or_phone','batch_id','participant_type_id','user_type','user_id','type','created_at')
            ->where('batch_id',$batch_id)
            ->whereNotIn('user_id', $enroll_ids)
            ->get();

        $result = $res->merge($restrictedUsers);
        $data['data'] = $result;
        $data['institution'] = $ins;
        $data['batch'] = $batch;


        //$html = view('pdf.invoice',$data);
          
       $pdf = LaravelMpdf::loadView('pdf.invoice', compact('data'));
       return $pdf->stream('document.pdf');

        // $mpdf =  new LaravelMpdf;
  
           
        // $mpdf->WriteHTML($html);
         $filename = 'user-report-'.date('d-m-Y-His');
        // $data =  $mpdf->Output();
        return 21;
        return response()->download($data, $filename);


        return response()->json($result);
    }

    static public function mergeit(LengthAwarePaginator $collection1, LengthAwarePaginator $collection2)
    {
        $total = $collection1->total() + $collection2->total();

        $perPage = $collection1->perPage() + $collection2->perPage();

        $items = array_merge($collection1->items(), $collection2->items());

        $paginator = new LengthAwarePaginator($items, $total, $perPage);

        return $paginator;
    }

    public function update_status_enroll($id){

        $ce = CourseEnrollment::find($id);
        $ce->status = 1;
        $ce->update();

        return response()->json(['data' => $ce, 'message' => 'Successfully approved']);
    }

    public function learnerReport($batch_id){

        $db = config()->get('database.connections.my-account.database');

        $export = CourseEnrollment::select('users.name','users.email','users.phone','user_infos.gender','user_infos.designation','course_enrollments.created_at as enrollment_date','course_enrollments.course_completeness','orders.user_id','orders.amount','orders.created_at')
        ->join($db.'users','users.id','course_enrollments.user_id')
        ->join($db.'user_infos','user_infos.user_id','users.id')
        ->join('orders','orders.id','course_enrollments.order_id')
        ->where('course_enrollments.course_batch_id',$batch_id)
        ->get();


        return Excel::download(new TransactionReport($export),'transaction_report.xlsx');
    }

    public function transactionReport($batch_id){

        $export = CourseEnrollment::select('orders.user_id','orders.amount','orders.created_at')->join('orders','orders.id','course_enrollments.order_id')
                ->where('course_enrollments.course_batch_id',$batch_id)
                ->get();


        return Excel::download(new TransactionReport($export),'transaction_report.xlsx');
    }

    public function enrollment(Request $request){
        return 'ok';
    }


    public function certificate_info(Request $request, $batch_id) {  
        $db = config()->get('database.connections.my-account.database');
        $perPage = 20;

        $res = CertificateSubmit::select('ce.id as enrollment_id','u.name','u.email','u.phone','ce.created_at as enrolled_at','ce.course_completeness','ce.status','ce.id','u.id as user_id','certificate_submit.tracking_code','certificate_submit.status','certificate_submit.file_name')
            ->join('course_enrollments as ce','ce.id','certificate_submit.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->join($db.'.users as u','u.id','o.user_id')
            ->where('ce.course_batch_id',$batch_id)
            ->orderBy('ce.id','DESC')
            ->paginate($perPage / 2);

        return response()->json($res);
    }

    public function update_certificate_status(Request $request){

        $info = CertificateSubmit::select('*')
            ->where('course_enrollment_id',$request->course_enrollment_id)
            ->first();
        $info->status = $request->status;
        $info->save();

        return response()->json(['data' => $info, 'message' => 'Successfully updated']);
    }
}