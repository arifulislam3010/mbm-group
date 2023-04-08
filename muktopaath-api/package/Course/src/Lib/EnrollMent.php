<?php
namespace Muktopaath\Course\Lib;
use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use App\Models\Gamification\Gamification;
use App\Lib\helper;
use App\Lib\Ekpay;
use App\Lib\GamificationClass;
use Muktopaath\Course\Models\Course\CourseBatch;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
trait EnrollMent
{
   public function sendData($datas)
    {
        $url = 'http://103.48.16.6:8080/imlma/api/training/user-training-applicant-status-update';
//        $url = 'http://localhost:8089/test/check';
        return $this->CurlFunction($url,$datas);
    }
    public function EnrollMentById($batch_id,$user_id,$data){
        $helper = new helper();
        $user  =  User::find($user_id);
        $batch = CourseBatch::find($batch_id);
        if($user && $batch){
            if($batch->share!=null || $batch->share!=''){
                if(json_decode($batch->share)->type==1){
                  $share  = ($batch->payment_point_amount*json_decode($batch->share)->amount)/100;
                }else{
                   $share  = json_decode($batch->share)->amount;
                }
             }else{
                $share=0;
             }
    
            
            $today_date = Carbon::now()->format('Y-m-d');
            if($batch->discount_status==1 && (json_decode($batch->discount)->date >= $today_date)){
                if(json_decode($batch->discount)->type==0){
                  $amount  = ($batch->payment_point_amount*json_decode($batch->discount)->amount)/100;
                  $discount = $batch->payment_point_amount-$amount;
                }else{
                   $amount  = $batch->payment_point_amount-json_decode($batch->discount)->amount;
                   $discount = json_decode($batch->discount)->amount;
                }
             }else{
                $amount  = $batch->payment_point_amount;
                $discount = 0;
             }
            
             $record = Order::latest()->first();
             if($record){$order_number=$record->order_number;}else{$order_number='';}
            
            $order = new Order;
            $order->amount              = $amount;
            $order->discount            = $discount;
            $order->share               = $share;
            $order->order_number        = $helper->nextOrderNumber($order_number);
            $order->transactionid       = $data['tran_id'];
            $order->tran_amount = $data['store_amount'];
            $order->tran_data = json_encode($data);
            $order->payment_status      = 1;
            $order->type                = 0;
            $order->user_id             = $user->id;
            if($order->save()){
    
                $course_enrollments     = new CourseEnrollment;
                $course_enrollments->order_id           = $order->id;
                $course_enrollments->uuid           = Str::uuid();
                $course_enrollments->course_batch_id    = $batch->id;
                $course_enrollments->journey_status     = json_encode($helper->journeystatus(json_decode($batch->syllabus)));
                $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
                $course_enrollments->course_completeness = 0;

                if($batch->enrolment_approval_status==1)
                $course_enrollments->status = 0;
                else
                $course_enrollments->status = 1;

                $course_enrollments->save();
                $gamification_class = new GamificationClass;
                $gamification_class->gamificationStore('enr',$user->id,$course_enrollments->id,1);
            }

            return $order;
        }else{
            return false;
        }
        
    }
    public function EnrollMentById2($batch_id,$user_id){
        $helper = new helper();
        $user  =  User::find($user_id);
        $batch = CourseBatch::find($batch_id);
        if($user && $batch){
             if($batch->share!=null || $batch->share!=''){
                if(json_decode($batch->share)->type==1){
                  $share  = ($batch->payment_point_amount*json_decode($batch->share)->amount)/100;
                }else{
                   $share  = json_decode($batch->share)->amount;
                }
             }else{
                $share=0;
             }
    
            
            $today_date = Carbon::now()->format('Y-m-d');
            if($batch->discount_status==1 && (json_decode($batch->discount)->date >= $today_date)){
                if(json_decode($batch->discount)->type==0){
                  $amount  = ($batch->payment_point_amount*json_decode($batch->discount)->amount)/100;
                  $discount = $batch->payment_point_amount-$amount;
                }else{
                   $amount  = $batch->payment_point_amount-json_decode($batch->discount)->amount;
                   $discount = json_decode($batch->discount)->amount;
                }
             }else{
                $amount  = $batch->payment_point_amount;
                $discount = 0;
             }
            
             $record = Order::latest()->first();
             if($record){$order_number=$record->order_number;}else{$order_number='';}
            
            $order = new Order;
            $order->amount              = $amount;
            $order->discount            = $discount;
            $order->share               = $share;
            $order->order_number        = $helper->nextOrderNumber($order_number);
            $order->payment_status      = 0;
            $order->type                = 0;
            $order->user_id             = $user->id;
            // return json_encode($helper->journeystatus(json_decode($batch->syllabus)));
            if($order->save()){
    
                $course_enrollments     = new CourseEnrollment;
                $course_enrollments->order_id           = $order->id;
                $course_enrollments->uuid           = Str::uuid();
                $course_enrollments->course_batch_id    = $batch->id;
                $course_enrollments->journey_status     = json_encode($helper->journeystatus(json_decode($batch->syllabus)));
                $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
                $course_enrollments->course_completeness = 0;

                if($batch->enrolment_approval_status==1)
                $course_enrollments->status = 0;
                else
                $course_enrollments->status = 1;

                $course_enrollments->save();
                $gamification_class = new GamificationClass;
                $gamification_class->gamificationStore('enr',$user->id,$course_enrollments->id,1);
                $datas = array('email'=>$user->email,'courseId'=>$batch->course_id,'batchId'=>$batch->id,'history'=>array('enrollMent'=>1));
                // $this->sendData($datas);
                return $course_enrollments;
            }

            return $order;
        }else{
            return false;
        }
        
    }
    public function EnrollMentByIdEkpay($batch_id,$user_id,$transactionid,$tnsData){
        $helper = new helper();
         $user  =  User::find($user_id);
        $batch = CourseBatch::find($batch_id);
        if($user && $batch){
            if($batch->share!=null || $batch->share!=''){
                if(json_decode($batch->share)->type==1){
                  $share  = ($batch->payment_point_amount*json_decode($batch->share)->amount)/100;
                }else{
                   $share  = json_decode($batch->share)->amount;
                }
             }else{
                $share=0;
             }
    
            
            $today_date = Carbon::now()->format('Y-m-d');
            if($batch->discount_status==1 && (json_decode($batch->discount)->date >= $today_date)){
                if(json_decode($batch->discount)->type==0){
                  $amount  = ($batch->payment_point_amount*json_decode($batch->discount)->amount)/100;
                  $discount = $batch->payment_point_amount-$amount;
                }else{
                   $amount  = $batch->payment_point_amount-json_decode($batch->discount)->amount;
                   $discount = json_decode($batch->discount)->amount;
                }
             }else{
                $amount  = $batch->payment_point_amount;
                $discount = 0;
             }
            
             $record = Order::latest()->first();
             if($record){$order_number=$record->order_number;}else{$order_number='';}
            $order = new Order;
            $order->amount              = $amount;
            $order->discount            = $discount;
            $order->share               = $share;
            $order->order_number        = $helper->nextOrderNumber($order_number);
            $order->transactionid       = $transactionid;
            $order->tran_amount = $amount;
            if($tnsData!=1){
              $order->tran_data = json_encode($tnsData);
            }
            $order->payment_status      = 1;
            $order->payment_method      = 'ekpay';
            $order->type                = 0;
            $order->user_id             = $user->id;
            if($order->save()){
    
                $course_enrollments     = new CourseEnrollment;
                $course_enrollments->order_id           = $order->id;
                $course_enrollments->uuid           = Str::uuid();
                $course_enrollments->user_id           = $user->id;
                $course_enrollments->course_batch_id    = $batch->id;
                $course_enrollments->journey_status     = null;
                $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
                $course_enrollments->course_completeness = 0;

                if($batch->enrolment_approval_status==1)
                $course_enrollments->status = 0;
                else
                $course_enrollments->status = 1;

                $course_enrollments->save();
                $gamification_class = new GamificationClass;
                $gamification_class->gamificationStore('enr',$user->id,$course_enrollments->id,1);
            }

            return $order;
        }else{
            return false;
        }
        
    }
    
    public function CurlFunction($url,$data)
    {
        $headers = array('Accept: application/json', 'Content-Type: application/json');
        try {
            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $res = curl_exec($ch);
            return $res;
            if($e=curl_error($ch)){
                return $e;
            }else{
                $decoded = json_decode($res);
            }
            curl_close($ch);
        } catch(\Exception $e) {
            return $e;
        }

    }
}
