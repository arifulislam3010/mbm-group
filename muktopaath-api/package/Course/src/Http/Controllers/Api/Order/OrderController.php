<?php

namespace Muktopaath\Course\Http\Controllers\Api\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Models\Course\Order;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Illuminate\Support\Str;
use Muktopaath\Course\Http\Resources\Order as OrderResource;
use Auth;
use DB;
use Carbon\Carbon;
use App\Lib\ManualEncodeDecode;
use App\Models\Gamification\Gamification;
use Muktopaath\Course\Lib\helper;
use Muktopaath\Course\Lib\Ekpay;
use Muktopaath\Course\Lib\EnrollMent;
use Muktopaath\Course\Lib\GamificationClass;
use Muktopaath\Course\Models\Course\CourseBatch;

use App\Models\Myaccount\User;
use App\Mail\verifyEmail;
use File;
use Mail;
use Uzzal\SslCommerz\Client;
use Uzzal\SslCommerz\Customer;
use Uzzal\SslCommerz\IpnNotification;
use Muktopaath\Course\Lib\Sms;
class OrderController extends Controller
{
     use EnrollMent,Sms;
     public $successStatus = 200;
     public function referral(){
         $referral_user = User::where('ref_user_id',Auth::user()->id)->count();
         // $referral_point = Gamification::where('user_id',Auth::user()->id)->where('slug_name','srl')->sum('points');
         return response()->json([
            'api_info'    => 'Referral',
            'referral_user'        => $referral_user,
         ] , $this->successStatus);
     }
     public function purchase(Request $request){
        $quary     = ($request->has('quary'))?$request['quary']:null;
        $user = Auth::user();
        $order = Order::where('user_id',$user->id)->when($quary,function($q) use($quary){return $q->where('order_number',$quary);})->paginate(10);
        return OrderResource::collection($order);
    }
    
    public function purchaseDetails($id){
        $user = Auth::user();
        $order = Order::where('user_id',$user->id)->where('id',$id)->first();
        if($order){
          return new OrderResource($order);
        }else{
            return response()->json(['errors'=>['status' => false , 'message' => 'তথ্য পাওয়া যায়নি','type'=>5]] , 400);
        }
    }
    
    public function payment(Request $request){
        $helper = new helper;
        $data = $request->all();
        $user_id = json_decode($data['cart_json'])->user_id;
        $batch_id = json_decode($data['cart_json'])->batch_id;


        $user  =  User::find($user_id);
        $batch = CourseBatch::find($batch_id);
        
        $partner_name = $batch->owner->institution_name; 
        $partner_username = $batch->owner->username;
        $course_name = $batch->course_alise_name;
        $share = '';

        $order = Order::where('user_id',$user->id)->select('orders.*')->with('courseBatch')->whereHas('courseBatch', function($q)use($batch_id){
            $q->where('course_batch_id','=',$batch_id);
        })->first();
        $today_date = Carbon::now()->format('Y-m-d');
        if($batch->discount_status==1 && (json_decode($batch->discount)->date >= $today_date)){
            if(json_decode($batch->discount)->type==0){
                if(is_numeric(json_decode($batch->discount)->amount) && json_decode($batch->discount)->amount!=''){
                    $amount  = ($batch->payment_point_amount*json_decode($batch->discount)->amount)/100;
                    $discount = $batch->payment_point_amount-$amount;
                }else{
                    $amount  = $batch->payment_point_amount;
                    $discount = 0;
                }
              
            }else{
                if(is_numeric(json_decode($batch->discount)->amount) && json_decode($batch->discount)->amount!=''){
                    $amount  = $batch->payment_point_amount-json_decode($batch->discount)->amount;
                    $discount = json_decode($batch->discount)->amount;
                }else{
                    $amount  = $batch->payment_point_amount;
                    $discount = 0;   
                }
               
            }
         }else{
            $amount  = $batch->payment_point_amount;
            $discount = 0;
         }
        
    
         if($batch->share){
            if(json_decode($batch->share)->type==0){
                if(is_numeric(json_decode($batch->share)->amount) && json_decode($batch->share)->amount!=''){
                    $shareamount  = ($amount*json_decode($batch->share)->amount)/100;
                    $share = json_decode($batch->share)->amount-$shareamount;
                }else{
                    $share = 0;
                }
              
            }else{
                if(is_numeric(json_decode($batch->share)->amount) && json_decode($batch->share)->amount!=''){
                     $share  = json_decode($batch->share)->amount;
                }else{
                     $share  = 0;
                }
             
            }
         }else{
            $share = 0;
         }

        if($order){
            if($order->payment_status==1){
                 $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
                 return redirect(config('global.front_url').'payment/success/'.$course_enrollments->id);
            }else{

                $resp = Client::verifyTransaction($order->transactionid);
                if($resp->get('status')=="VALIDATED"){

                    $order->tran_amount = $resp->get('store_amount');
                    $order->tran_data = json_encode($resp->get('1'));
                    $order->payment_status = 1;
                    $order->update();
                    $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
                    return redirect(config('global.front_url').'payment/success/'.$course_enrollments->id);

                }else{
                   
                    
                    $ManualEncodeDecode = new ManualEncodeDecode; 
                    $order_info = $user->id.'-'.$batch->id.'-'.date('YmdHis');
        
                    $record = Order::latest()->first();
                    if($record){$order_number=$record->order_number;}else{$order_number='';}
                    
                    
                    $customer = new Customer($user->name, $user->id,$user->email);
                    $resp = Client::initSession($customer,$amount);
                    $order->order_number = $helper->nextOrderNumber($order_number);
                    $order->transactionid = $ManualEncodeDecode->encode($order_info,'mukto123');
                    $order->update();
                     
                       $helper->payment($user,$order,$batch,$amount,$discount,$share);
                      return redirect()->to($resp->getGatewayUrl());
                }
            }   
        }else{
            
               
            $helper->paymentBeforEnroll($user,$batch,$amount,$discount,$share);
            return redirect()->to($resp->getGatewayUrl());
        } 

    }
   

    public function ekpayPayment($batch_id,$user_id){
        
        $helper = new helper;
        $Ekpay = new Ekpay;

        $user  =  User::find($user_id);
        if (strlen($user->name) != strlen(utf8_decode($user->name)))
        {
            return redirect(config('global.front_url').'400/আপনার নামটি ইংলিশ এ লিখে আবার চেষ্টা করুন।');
        }
        if($user->phone==null || $user->phone==''){
            return redirect(config('global.front_url').'400/আপনার ফোন নম্বর প্রোফাইল এ অ্যাড করে আবার চেষ্টা করুন।');
        }
        $batch = CourseBatch::find($batch_id);
        
        $user  =  User::find($user_id);
        $batch = CourseBatch::find($batch_id);
        
        $partner_name = $batch->owner->institution_name; 
        $partner_username = $batch->owner->username;
        $course_name = $batch->course_alise_name;
        $share = '';

        $order = Order::where('user_id',$user->id)->select('orders.*')->with('courseBatch')->whereHas('courseBatch', function($q)use($batch_id){
            $q->where('course_batch_id','=',$batch_id);
        })->first();
        $today_date = Carbon::now()->format('Y-m-d');

        if($batch->discount_status==1 && (json_decode($batch->discount)->date >= $today_date)){
            if(json_decode($batch->discount)->type==0){
                if(is_numeric(json_decode($batch->discount)->amount) && json_decode($batch->discount)->amount!=''){
                    $amount  = $batch->payment_point_amount-json_decode($batch->discount)->amount;
                    $discount = json_decode($batch->discount)->amount;
                }else{
                    $amount  = $batch->payment_point_amount;
                    $discount = 0;   
                }
              
            }else{
                 if(is_numeric(json_decode($batch->discount)->amount) && json_decode($batch->discount)->amount!=''){
                    $discount = $batch->payment_point_amount*((json_decode($batch->discount)->amount)/100);
                    $amount  = $batch->payment_point_amount-$discount;
                }else{
                    $amount  = $batch->payment_point_amount;
                    $discount = 0;
                }
               
            }
         }else{
            $amount  = $batch->payment_point_amount;
            $discount = 0;
         }
         if($batch->share){
            if(json_decode($batch->share)->type==0){
                if(is_numeric(json_decode($batch->share)->amount) && json_decode($batch->share)->amount!=''){
                    $shareamount  = ($amount*json_decode($batch->share)->amount)/100;
                    $share = json_decode($batch->share)->amount-$shareamount;
                }else{
                    $share = 0;
                }
              
            }else{
                if(is_numeric(json_decode($batch->share)->amount) && json_decode($batch->share)->amount!=''){
                     $share  = json_decode($batch->share)->amount;
                }else{
                     $share  = 0;
                }
             
            }
         }else{
            $share = 0;
         }
         
         
        if($order){
           
            $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
            if($order->payment_status==1){
                 return redirect(config('global.front_url').'course/journey/'.$course_enrollments->uuid);
            }else{
                $ManualEncodeDecode = new ManualEncodeDecode; 
                $order_info = $user->id.'-'.$batch->id.'-'.rand(10,100);
                $tran_id = $ManualEncodeDecode->encode($order_info,'mukto123');
                $order->transactionid = $tran_id;
                $order->update();

                $ekpayPaymentResponse = $Ekpay->AfterEnroll($user,$batch,$amount,$discount,$share,$tran_id);
                // return $ekpayPaymentResponse;
                 if($ekpayPaymentResponse){
                    return redirect($ekpayPaymentResponse);
                 }else{
                    return redirect(config('global.front_url').'payment/fail/'.$course_enrollments->course_batch_id);
                 }

            }   
        }else{
            
            $url = $Ekpay->BeforeEnroll($user,$batch,$amount,$discount,$share);
            if($url){
                return redirect()->to($url);
            }else {
             return redirect(config('global.front_url').'400/পেমেন্ট গেটওয়েতে প্রযুক্তিগত সমস্যা। এটি শীঘ্রই সমাধান করা হবে');
            }
        }
    }
    
    public function ekpaySuccess($id){
        try{
            $transactionid = $id;
            $tnsData = 1;
            $ManualEncodeDecode = new ManualEncodeDecode;
            $helper = new helper;
            $order = order::where('transactionid',$transactionid)->first();
            // $order->delete();

            if($order){

                $order->payment_status = 1;
                // $order->tran_amount = $order->amount;
                $order->update();
                
                if($helper->paymentSuccessMessage($order)){

                    $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
                    return redirect(config('global.front_url').'payment/success/'.$course_enrollments->uuid);
                }
                
            }else {
                $order_info = $ManualEncodeDecode->decode($transactionid,'mukto123');
                $order_info_array = explode("-", $order_info);
                $batch_id   = $order_info_array[1];
                $user_id    = $order_info_array[0];
                $order = $this->EnrollMentByIdEkpay($batch_id,$user_id,$transactionid,$tnsData);
                if($order){
                    if($helper->paymentSuccessMessage($order)){
                        $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
                        return redirect(config('global.front_url').'payment/success/'.$course_enrollments->uuid);
                    }
                }else{
                    return redirect(config('global.front_url').'payment/fail/');
                }
                return redirect(config('global.front_url'));
            }
        }
        catch(\Exception $e){
        
            return redirect(config('global.front_url').'400/টেকনিকাল সমস্যা');
        }
    }
    public function ekpaySuccessPost(Request $request,$id){
        $file_name = $id.'.json';
        $dir = 'payments/'.date('Y/m/d');
        $d = explode('/', $dir);
        $t = '';
        foreach($d as $v){
            if($t!='')
                $t = $t.'/';
            $t = $t.$v;
            if(!is_dir($t))
                mkdir($t);
        }

       File::put($dir.'/'.$file_name,json_encode($request->all()));
       // return $id;
        $tnsData = $request->all();
        // return json_decode($tnsData,true);
       if($tnsData['msg_code'] == 1020){


        
        $order = order::where('transactionid',$transactionid)->first();
        // $order->delete();
        if($order){
            $order->payment_status = 1;
            $order->tran_amount = $order->amount;
            $order->tran_data = json_encode($tnsData);
            $order->update();
        }else {
            
            $transactionid = $id;
            $ManualEncodeDecode = new ManualEncodeDecode;
            $helper = new helper;
            
            
            $order_info = $ManualEncodeDecode->decode($transactionid,'mukto123');

            $order_info_array = explode("-", $order_info);
            $batch_id   = $order_info_array[1];
            $user_id    = $order_info_array[0];
            $order = $this->EnrollMentByIdEkpay($batch_id,$user_id,$transactionid,$tnsData);
        }

            
            
       }
        
    }
    public function ekpayFail($id){

        try{
            $ManualEncodeDecode = new ManualEncodeDecode;
            $order_info = $ManualEncodeDecode->decode($id,'mukto123');
            $order_info_array = explode("-", $order_info);
            $batch_id   = $order_info_array[1];
            $user_id    = $order_info_array[0];
            $order_number  = $order_info_array[2];
            $course = CourseBatch::Where('id',$batch_id)->first();
            return redirect(config('global.front_url').'payment/cancel/'.$course->uuid);
        }
        catch(\Exception $e){
            return redirect(config('global.front_url'));
        }
    }
    public function ekpayCancel($id){
        try{
            $ManualEncodeDecode = new ManualEncodeDecode;
            $order_info = $ManualEncodeDecode->decode($id,'mukto123');
            $order_info_array = explode("-", $order_info);
            $batch_id   = $order_info_array[1];
            $user_id    = $order_info_array[0];
            $order_number  = $order_info_array[2];
            $course = CourseBatch::Where('id',$batch_id)->first();
            return redirect(config('global.front_url').'payment/cancel/'.$course->uuid);
        }
        catch(\Exception $e){
            return redirect(config('global.front_url'));
        }
    }
    public function purchasePayment($batch_id,$user_id){
        $helper = new helper;
        $user  =  User::find($user_id);
        $batch = CourseBatch::find($batch_id);
        
        $partner_name = $batch->owner->institution_name; 
        $partner_username = $batch->owner->username;
        $course_name = $batch;
        $share = 0;
        $order = Order::where('user_id',$user->id)->select('orders.*')->with('courseBatch')->whereHas('courseBatch', function($q)use($batch_id){
            $q->where('course_batch_id','=',$batch_id);
        })->first();

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

        if($order){
            if($order->payment_status==1){
                 $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
                 return redirect(config('global.front_url').'payment/success/'.$course_enrollments->uuid);
            }else{

                $resp = Client::verifyTransaction($order->transactionid);
                if($resp->get('status')=="VALIDATED"){
                    $order->tran_amount = $resp->get('store_amount');
                    $order->tran_data = json_encode($resp->get('1'));
                    $order->payment_status = 1;
                    $order->update();
                    $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
                    return redirect(config('global.front_url').'payment/success/'.$course_enrollments->uuid);
                }else{
                    $customer = new Customer($user->name, $user->id,$user->email);
                    $resp = Client::initSession($customer,$amount);
                    $order->transactionid = $resp->getTransactionId();
                    $order->update();
                    return redirect()->to($resp->getGatewayUrl());
                }
            }   
        }else{
             
            $customer = new Customer($user->name, $user->id, $user->email);
            $resp = Client::initSession($customer,$amount);

            $record = Order::latest()->first();
            if($record){$order_number=$record->order_number;}else{$order_number='';}

            $order = new Order;
            $order->amount              = $amount;
            $order->order_number        = $helper->nextOrderNumber($order_number);
            $order->transactionid       = $resp->getTransactionId();
            $order->payment_status      = 0;
            $order->type                = 0;
            $order->user_id             = $user->id;
            if($order->save()){
                $course_enrollments     = new CourseEnrollment;
                $course_enrollments->uuid               = Str::uuid();
                $course_enrollments->order_id           = $order->id;
                $course_enrollments->course_batch_id    = $batch->id;
                $course_enrollments->journey_status     = '';
                $course_enrollments->extra_assessment_attempt = '[{"exam":0,"quiz":0,"assignment":0}]';
                $course_enrollments->course_completeness = 0;
                $course_enrollments->status = $batch->enrolment_approval_status;
                $course_enrollments->save();
                $gamification_class = new GamificationClass;
                $gamification_class->gamificationStore('enr',$user->id,$course_enrollments->id,1);
            }

            return redirect()->to($resp->getGatewayUrl());
        } 

        
    }
    
    
    
    public function PaymentCheck($trid){
        // return $trid;
        $ManualEncodeDecode = new ManualEncodeDecode;
        $order_info = $ManualEncodeDecode->decode($trid,'mukto123');
        $order_info_array = explode("-", $order_info);
        $batch_id   = $order_info_array[1];
        $user_id    = $order_info_array[0];
        $order_number  = $order_info_array[2];
        $transection_number = 0;
        return 'batch_id='.$batch_id.'---Userid='.$user_id;
      
        
    }
    
    public function paymentSuccess(Request $request)
    {
        $ManualEncodeDecode = new ManualEncodeDecode;
        $helper = new helper;
        $data = $request->all();
        $orderC = order::where('transactionid',$data['tran_id'])->first();
        if($request->status == 'VALID' && $request->risk_level == 0) {
            if($orderC){
                $orderC->tran_amount = $data['store_amount'];
                $orderC->tran_data = json_encode($request->all());
                $orderC->payment_status = 1;
                $orderC->update();
                if($helper->paymentSuccessMessage($orderC)){
                    $course_enrollments     = CourseEnrollment::where('order_id',$orderC->id)->first();
                    return redirect(config('global.front_url').'payment/success/'.$course_enrollments->uuid);
                }
            }else{
                $order_info = $ManualEncodeDecode->decode($data['tran_id'],'mukto123');
                $order_info_array = explode("-", $order_info);
                $batch_id   = $order_info_array[1];
                $user_id    = $order_info_array[0];
                $order = $this->EnrollMentById($batch_id,$user_id,$data);
                if($order){
                    if($helper->paymentSuccessMessage($order)){
                        $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
                        return redirect(config('global.front_url').'payment/success/'.$course_enrollments->uuid);
                    }
                }else{
                    return redirect(config('global.front_url').'payment/fail/');
                }

            }
            
        }else {
            
            $verify = Client::verifyOrder($data['val_id']);
            if ($verify->getStatus() == 'VALID' || $verify->getStatus() == 'VALIDATED') {
                if($orderC){
                    $orderC->tran_amount = $data['store_amount'];
                    $orderC->tran_data = json_encode($request->all());
                    $orderC->payment_status = 1;
                    $orderC->update();
                    if($helper->paymentSuccessMessage($orderC)){
                        $course_enrollments     = CourseEnrollment::where('order_id',$orderC->id)->first();
                        return redirect(config('global.front_url').'payment/success/'.$course_enrollments->uuid);
                    }
                }else{
                    $order_info = $ManualEncodeDecode->decode($data['tran_id'],'mukto123');
                    $order_info_array = explode("-", $order_info);
                    $batch_id   = $order_info_array[1];
                    $user_id    = $order_info_array[0];
                    $order = $this->EnrollMentById($batch_id,$user_id,$data);
                    if($order){
                        if($helper->paymentSuccessMessage($order)){
                            $course_enrollments     = CourseEnrollment::where('order_id',$order->id)->first();
                            return redirect(config('global.front_url').'payment/success/'.$course_enrollments->uuid);
                        }
                    }else{
                        return redirect(config('global.front_url').'payment/fail/');
                    }
    
                }
            } else {
                return redirect(config('global.front_url').'payment/fail');
            }
        }
       
    }

    public function paymentFail(Request $request)
    {
        $ManualEncodeDecode = new ManualEncodeDecode;
        $helper = new helper;
        
        $data = $request->all();
        $order_info = $ManualEncodeDecode->decode($data['tran_id'],'mukto123');
        $order_info_array = explode("-", $order_info);
        $batch_id   = $order_info_array[1];
        $user_id    = $order_info_array[0];
        return redirect(config('global.front_url').'payment/fail/'.$batch_id);
       
    }

    public function paymentCancel(Request $request)
    {
        $ManualEncodeDecode = new ManualEncodeDecode;
        $helper = new helper;
        
        $data = $request->all();
        $order_info = $ManualEncodeDecode->decode($data['tran_id'],'mukto123');
        $order_info_array = explode("-", $order_info);
        $batch_id   = $order_info_array[1];
        $user_id    = $order_info_array[0];
        $course = CourseBatch::Where('id',$batch_id)->first();
        return redirect(config('global.front_url').'payment/cancel/'.$course->uuid);
       
       
    }

    public function paymentIpn(Request $request)
    {
       return "ss";
    }

    public function successful() {
        return "success full";
       
    }
   
}
