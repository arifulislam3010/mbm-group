<?php

namespace Muktopaath\Course\Lib;
use App\Mail\PaymentConfirm;
use Illuminate\Support\Facades\File;
use App\Models\Myaccount\InstitutionInfo;
use App\Mail;
use App\Lib\ManualEncodeDecode;
use Muktopaath\Course\Lib\SmsClass;
class helper
{
    public $successStatus = 200;
    protected $api_base_url = 'http://bulksms.teletalk.com.bd/link_sms_send.php?op=SMS';
    protected $sms_api_user = 'mukto_user';
    protected $sms_api_pwd = 'RbaQuYng';
    protected $pre_verify_txt_user = 'Payment Success user :';
    protected $pre_verify_txt_partner = 'Payment Success partner :';
    protected $pre_verify_txt_mukto = 'Payment Success mukto:';

    public function journeystatus($syllabus)
    {
        $array_syllabus = (array) $syllabus;
        $journey_status = [];
        foreach($array_syllabus['units'] as $uk => $uv){
            // return $uk;
            // return $array_syllabus[0];
            if(isset($array_syllabus[$uk])){
               $less_arr = (array)$array_syllabus[$uk];
                foreach($less_arr['lessons'] as $lk => $lv){
                    $journey_status[$uk][$lk] = [];
                    $journey_status[$uk][$lk]['start'] = 0;
                    $journey_status[$uk][$lk]['completeness'] = 0;
                } 
            }
            
        }
        
        return $journey_status;
    }
    
    public function nextOrderNumber($value){
         $expNum = explode('-', $value);
                
        //check first day in a year
        if ($expNum[0]<date('Y') || $expNum[0]==''){
            $nextOrderNumber = date('Y').'-00001';
        } else {
            $nextOrderNumber = $expNum[0].'-'.sprintf('%05d', $expNum[1]+1);
        }  
        return $nextOrderNumber;
    }

    public function paymentSuccessMessage($order){
        $muktopaath = InstitutionInfo::find(1);
        $SmsClass = new SmsClass();
        $SmsClass->send($order->User->phone,urlencode('Congratulation!, Your payent of TK'.$order->amount.' for '.$order->enrollment->courseBatch->course_alias_name.' has been successfully received. Thanks Muktopaath Team'));

            
        $SmsClass->send($order->enrollment->courseBatch->owner->phone,urlencode('Congratulation!, You have received TK'.$order->tran_amount.' for '.$order->enrollment->courseBatch->course_alias_name.' from '.$order->User->name));

        $SmsClass->send($muktopaath->phone,urlencode('Congratulation, You have received'.$order->tran_amount.' BDT for '.$order->enrollment->courseBatch->course_alias_name.' from '.$order->User->name));
        
        // if($order->User->email){
        //      Mail::to($order->User->email)->send(new PaymentConfirm($order,1));
        // }

        // if($order->enrollment->courseBatch->owner->email){
        //      Mail::to($order->enrollment->courseBatch->owner->email)->send(new PaymentConfirm($order,2));
        // }

        // if($muktopaath->email){
        //     Mail::to($muktopaath->email)->send(new PaymentConfirm($order,3));
        // }

        return true;
    }

   
    public function payment($user,$order,$batch,$amount,$discount,$share){
        
        $post_data = array();
        $post_data['store_id'] = "muktopaathlive";
        $post_data['store_passwd'] = "5DCAB292446D483486";
        $post_data['total_amount'] = $amount;
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = $order->transactionid;
        $post_data['success_url'] = "http://api.muktopaath.gov.bd/payment/success";
        $post_data['fail_url'] = "http://api.muktopaath.gov.bd/payment/fail";
        $post_data['cancel_url'] = "http://api.muktopaath.gov.bd/payment/cancel";

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $user->name;
        $post_data['cus_email'] = $user->email;
        $post_data['cus_add1'] = "Dhaka";
        $post_data['cus_add2'] = "Dhaka";
        $post_data['cus_city'] = "Dhaka";
        $post_data['cus_state'] = "Dhaka";
        $post_data['cus_postcode'] = "1000";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $user->phone;
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1 '] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = $batch->course_alias_name;
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = $batch->owner->institution_name;
        $post_data['value_b '] = $batch->owner->username;
        $post_data['value_c'] = $share;
        $post_data['value_d'] = $batch->course_alias_name;

        # EMI STATUS
        $post_data['emi_option'] = "1";

        # CART PARAMETERS
        $post_data['cart'] = json_encode(array(
            array("product"=>"DHK TO BRS AC A1","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A2","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A3","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A4","amount"=>"200.00")
        ));
        $post_data['product_amount'] = $amount;
        $post_data['vat'] = "5";
        $post_data['discount_amount'] = $discount;
        $post_data['convenience_fee'] = "3";


        // $post_data['allowed_bin'] = "3,4";
        // $post_data['allowed_bin'] = "470661";
        // $post_data['allowed_bin'] = "470661,376947";

        $post_data;
        # REQUEST SEND TO SSLCOMMERZ
        $direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
        // $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


        $content = curl_exec($handle );

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

         
        if($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            return "FAILED TO CONNECT WITH SSLCOMMERZ API";
            exit;
        }

        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true );
        //var_dump($sslcz); exit;

        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="") {
            # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
            # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            // echo "<meta http-equiv='refresh' content='0;url=".str_replace("https://epaydev.sslcommerz.com/",
            //         "http://localhost:4200/",$sslcz['GatewayPageURL'])."'>";
            return  json_encode(['status' => 'success', 'data' => $sslcz['GatewayPageURL'], 'logo' => 'http://admin.muktopaath.gov.bd/public/logo.png' ]);
            # header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            return  json_encode(['status' => 'fail', 'data' => null, 'message' => "JSON Data parsing error!"]);
            exit;
        }
    }

    public function paymentBeforEnroll($user,$batch,$amount,$discount,$share){
        // $order;
        $ManualEncodeDecode = new ManualEncodeDecode; 
        $order_info = $user->id.'-'.$batch->id.'-'.date('YmdHis');
        $post_data = array();
        $post_data['store_id'] = "muktopaathlive";
        $post_data['store_passwd'] = "5DCAB292446D483486";
        $post_data['total_amount'] = $amount;
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = $ManualEncodeDecode->encode($order_info,'mukto123');
        $post_data['success_url'] = "https://v3api.muktopaath.gov.bd/payment/success";
        $post_data['fail_url'] = "https://v3api.muktopaath.gov.bd/payment/fail";
        $post_data['cancel_url'] = "https://v3api.muktopaath.gov.bd/payment/cancel";

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $user->name;
        $post_data['cus_email'] = $user->email;
        $post_data['cus_add1'] = "Dhaka";
        $post_data['cus_add2'] = "Dhaka";
        $post_data['cus_city'] = "Dhaka";
        $post_data['cus_state'] = "Dhaka";
        $post_data['cus_postcode'] = "1000";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $user->phone;
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1 '] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = $batch->course_alias_name;
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = $batch->owner->institution_name;
        $post_data['value_b '] = $batch->owner->username;
        $post_data['value_c'] = $share;
        $post_data['value_d'] = $batch->course_alias_name;

        # EMI STATUS
        $post_data['emi_option'] = "1";

        # CART PARAMETERS
        $post_data['cart'] = json_encode(array(
            array("product"=>"DHK TO BRS AC A1","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A2","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A3","amount"=>"200.00"),
            array("product"=>"DHK TO BRS AC A4","amount"=>"200.00")
        ));
        $post_data['product_amount'] = $amount;
        $post_data['vat'] = "5";
        $post_data['discount_amount'] = $discount;
        $post_data['convenience_fee'] = "3";


        // $post_data['allowed_bin'] = "3,4";
        // $post_data['allowed_bin'] = "470661";
        // $post_data['allowed_bin'] = "470661,376947";

        $post_data;
        # REQUEST SEND TO SSLCOMMERZ
        $direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
        // $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


        $content = curl_exec($handle );

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

         
        if($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            return "FAILED TO CONNECT WITH SSLCOMMERZ API";
            exit;
        }

        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true );
        //var_dump($sslcz); exit;

        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="") {
            # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
            # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            // echo "<meta http-equiv='refresh' content='0;url=".str_replace("https://epaydev.sslcommerz.com/",
            //         "http://localhost:4200/",$sslcz['GatewayPageURL'])."'>";
            return  json_encode(['status' => 'success', 'data' => $sslcz['GatewayPageURL'], 'logo' => 'http://admin.muktopaath.gov.bd/public/logo.png' ]);
            # header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            return  json_encode(['status' => 'fail', 'data' => null, 'message' => "JSON Data parsing error!"]);
            exit;
        }
    }

    
}