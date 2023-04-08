<?php

namespace App\Http\Controllers\Assessment;

use App\Mail\MailSender;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment\Course;
use App\Models\Assessment\CourseBatch;
use App\Models\Assessment\RestrictedUser;
use App\Models\Myaccount\User;
use App\Models\CourseTag;
use App\Models\Assessment\Order;
use App\Models\Assessment\CourseEnrollment;
use App\Models\Tag;
use App\Lib\ManualEncodeDecode;
use App\Http\Resources\Assessment\DetailsResource;
use Illuminate\Support\Facades\Mail;
use Validator;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;
use DB;
use App\Lib\SMS;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class CourseBatchController extends Controller
{
    public function index($id){
        
        $batches = CourseBatch::where('course_id',$id)->with('course')->orderBy('id','DESC')->paginate(100);
        return response()->json($batches);
    }


    public function find($id){
        
        $batch = CourseBatch::with('course')->findOrFail($id);
        $batch->course->tag = json_decode($batch->course->tag);
        $batch->occurs_on = json_decode($batch->occurs_on);

        return response()->json($batch);
    }

    public function joininglist($id){

        $db = config()->get('database.connections.my-account.database');

        $res = CourseEnrollment::select('u.name','u.id as user_id','u.email','u.phone','course_enrollments.id','course_enrollments.created_at','course_enrollments.course_completeness')
            ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
            ->join('orders as o','o.id','course_enrollments.order_id')
            ->join($db.'.users as u','u.id','o.user_id')
            ->where('cb.id',$id)
            ->paginate(10);

        return response()->json($res);
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
            $user_id = Auth::user()->id;
            
            $this->validate($request, [
                     'course_alias_name'             => 'required',
                     'id'                            => 'required',
            ]);
            
            $update_others = CourseBatch::find($request->id);
            $course = Course::find($update_others->course_id);
            
            $course->title                  = $request->course_alias_name;
            
            $course->update();
            
            $duration = (int)$request['get_hrs'] * 60;
            $duration += (int)$request['get_mins'];
            
            
            $update_others->duration                 = $duration;
            $update_others->repeat_status            = $request->repeat;
            $update_others->live_link                = $request->live_link;
            $update_others->repeat_num               = $request->repeat_num;
            $update_others->repeat_period            = $request->repeat_period;
            $update_others->occurs_on                = json_encode($request->occurs_on);
            $update_others->occurrences              = $request->occurrences;
            $update_others->occurs_end_type          = $request->occurs_end_type;
            $update_others->start_date               = date('Y-m-d H:i:s',strtotime($request['start_date'])); 
            $update_others->end_date                 = $request->end_date?date('Y-m-d H:i:s',strtotime($request['end_date'])):null;
            $update_others->course_alias_name        = $request->course_alias_name;
            $update_others->certificate_alias_name   = $request->course_alias_name;
            $update_others->updated_by               = $user_id;
                
            $update_others->update();
            
    
            return response()->json($update_others);
            
    }

    public function getRestrictedUser(Request $request,$id){
        $user_type = $request->user_type;

        $myaccount = config()->get('database.connections.my-account.database');

        $restrictedUsers = RestrictedUser::Select('restricted_users.*')
                        ->where('batch_id',$id)
                        ->where('user_type',$user_type)
                        ->leftJoin($myaccount.'.users', function($join){
                            $join->on('users.email', '=', 'restricted_users.email_or_phone')
                            ->orOn('users.phone', '=', 'restricted_users.email_or_phone');
                        })
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
        $email_or_phonearray = [];
        
        
        $qryStr = 'INSERT INTO `restricted_users` (name,email_or_phone,type,batch_id,restricted_code,user_type) VALUES';
            foreach ($request->data as $key => $value) {
                $email_or_phone=$value['email'] ? $value['email']:$value['phone'];
                $type = filter_var($value['email'], FILTER_VALIDATE_EMAIL)?'email':'phone';
                $email_or_phone = preg_replace('/\s+/',' ', $email_or_phone);

              
                $restricted_access_code = $batch->restricted_access_code;
                
                $token = $info->encode($email_or_phone . '<:MP:>' . $restricted_access_code, env('ENCRIPTION_KEY'));
                $front_url = config()->get('global.front_url');
                $link = $front_url.'join/class?token='.$token.'&type=2';

                
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
                $qryStr .= '("'.$value['name'].'","'.$email_or_phone.'","'.$type.'","'.$batch_id.'","'.$batch->restricted_access_code.'","'.$user_type.'")';
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

        $batch = CourseBatch::findOrFail($batch_id);

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
        $user = RestrictedUser::findOrFail($request->id);

        $user->name            = $request->name;
        $user->email_or_phone  = $request->email_or_phone;
        $user->type            = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)?'email':'phone';

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
                return response()->json(['message' => 'Enrollment to be deleted not found'],404);
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

    public function enrollment(Request $request){
        return 'ok';
    }
}