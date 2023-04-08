<?php

namespace Muktopaath\Course\Lib;

// use Cake\Core\Configure;
use App\Lib\ManualEncodeDecode;

class Ekpay
{
    private $ekPayServer = 'https://pg.ekpay.gov.bd/ekpaypg/';
    private $ekPayTokenUrl;
    private $merRegId;
    private $merPasKey;
    private $requestId;
    private $refTranNo;
    private $refTraDate;

    private $accounts='';
    private $amount='';
    private $totalAmount='';

    private $sUrl;
    private $fUrl;
    private $cUrl;

    public function __construct()
    {
        $this->merRegId = 'muktopath';
        $this->merPasKey = 'Test@123';

        $this->ekPayTokenUrl = $this->ekPayServer.'v1/merchant-api';
    }

    public function verifyTransaction(){

    }
    
    public function BeforeEnroll($user,$batch,$amount,$discount,$share){
        
        $ManualEncodeDecode = new ManualEncodeDecode; 
        $order_info = $user->id.'-'.$batch->id.'-'.rand(10,100);
        $tran_id = $ManualEncodeDecode->encode($order_info,'mukto123');
        $this->sUrl = config('global.api_url').'/paymentStatus'.$tran_id.'/ekpay/success';
        $this->sUrlIpn = config('global.api_url').'/paymentStatus'.$tran_id.'/ekpay/success/api';
        $this->fUrl = config('global.api_url').'/paymentStatus'.$tran_id.'/ekpay/fail';
        $this->cUrl = config('global.api_url').'/paymentStatus'.$tran_id.'/ekpay/cancel';
        

        $this->requestId = $tran_id;
        $this->refTranNo = $tran_id;
        $this->refTraDate = date('Y-m-d H:i:s');

        

        $this->accounts= $tran_id;
        $this->amount= $amount;
        $this->totalAmount= $amount;

        $param = array();

        $param['cust_info'] = array("cust_email"=>(filter_var($user->email, FILTER_VALIDATE_EMAIL)?$user['email']:''),"cust_id"=>$user->id, "cust_mail_addr"=>"eKsheba","cust_mobo_no"=>"+88".$user->phone,"cust_name"=>$user->name);
        $param["feed_uri"] = array("s_uri"=>$this->sUrl,"f_uri"=>$this->fUrl,"c_uri"=>$this->cUrl);
        $param["ipn_info"] = array("ipn_channel" => "1","ipn_email" => "ariful30@gmail.com","ipn_uri" => $this->sUrlIpn);
        $param["mac_addr"] = "203.112.220.38";
        $param["mer_info"] = array("mer_reg_id"=>$this->merRegId, "mer_pas_key"=>$this->merPasKey);
        $param["req_timestamp"] = $this->refTraDate." GMT+6";
        $param["trns_info"] = array("ord_det"=>"From-eKsheba","ord_id"=>$tran_id,"trnx_amt"=>"$this->totalAmount","trnx_currency"=>"BDT","trnx_id"=>"$this->refTranNo");

        // $param = '{"cust_info":{"cust_email":"munir019@yahoo.com","cust_id":233,"cust_mail_addr":"eKsheba","cust_mobo_no":"+8801717303200","cust_name":"MD. MUNIR hossain"},"feed_uri":{"s_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/success","f_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/fail","c_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/cancel"},"ipn_info":{"ipn_channel":"3","ipn_email":"munir019@gmail.com","ipn_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatusupdate\/000178.0000008.20200107.1\/ekpay\/success"},"mac_addr":"103.48.18.34","mer_info":{"mer_reg_id":"muktopath","mer_pas_key":"Test@123"},"req_timestamp":"2020-01-07 12:40:01 GMT+6","trns_info":{"ord_det":"From-eKsheba","ord_id":"000178.0000008.20200107.1","trnx_amt":"20","trnx_currency":"BDT","trnx_id":"888f51780f0176b93bab1c5d4283f54f"}}';
        // echo '<pre>';
        // print_r($param);
        // echo '</pre>';die();
       
        
        $param = json_encode($param);

       

        $secret_key = $this->createSecretKey($param);

        
        
        if($secret_key){
            $redirectUrl = 'https://pg.ekpay.gov.bd/ekpaypg/v1?sToken='.$secret_key.'&trnsID='.$this->refTranNo;
            return $redirectUrl;
        }else{
            
            return '';
        }
        

    }
    public function AfterEnroll($user,$batch,$amount,$discount,$share,$tran_id){
        
        $tran_id = $tran_id;
        $this->sUrl = config('global.api_url').'/paymentStatus'.$tran_id.'/ekpay/success';
        $this->sUrlIpn = config('global.api_url').'/paymentStatus'.$tran_id.'/ekpay/success/api';
        $this->fUrl = config('global.api_url').'/paymentStatus'.$tran_id.'/ekpay/fail';
        $this->cUrl = config('global.api_url').'/paymentStatus'.$tran_id.'/ekpay/cancel';
        

        $this->requestId = $tran_id;
        $this->refTranNo = $tran_id;
        $this->refTraDate = date('Y-m-d H:i:s');

        

        $this->accounts= $tran_id;
        $this->amount= $amount;
        $this->totalAmount= $amount;

        $param = array();

        $param['cust_info'] = array("cust_email"=>(filter_var($user->email, FILTER_VALIDATE_EMAIL)?$user['email']:''),"cust_id"=>$user->id, "cust_mail_addr"=>"eKsheba","cust_mobo_no"=>"+88".$user->phone,"cust_name"=>$user->name);
        $param["feed_uri"] = array("s_uri"=>$this->sUrl,"f_uri"=>$this->fUrl,"c_uri"=>$this->cUrl);
        $param["ipn_info"] = array("ipn_channel" => "1","ipn_email" => "ariful30@gmail.com","ipn_uri" => $this->sUrlIpn);
        $param["mac_addr"] = "203.112.220.38";
        $param["mer_info"] = array("mer_reg_id"=>$this->merRegId, "mer_pas_key"=>$this->merPasKey);
        $param["req_timestamp"] = $this->refTraDate." GMT+6";
        $param["trns_info"] = array("ord_det"=>"From-eKsheba","ord_id"=>$tran_id,"trnx_amt"=>"$this->totalAmount","trnx_currency"=>"BDT","trnx_id"=>"$this->refTranNo");

        // $param = '{"cust_info":{"cust_email":"munir019@yahoo.com","cust_id":233,"cust_mail_addr":"eKsheba","cust_mobo_no":"+8801717303200","cust_name":"MD. MUNIR hossain"},"feed_uri":{"s_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/success","f_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/fail","c_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/cancel"},"ipn_info":{"ipn_channel":"3","ipn_email":"munir019@gmail.com","ipn_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatusupdate\/000178.0000008.20200107.1\/ekpay\/success"},"mac_addr":"103.48.18.34","mer_info":{"mer_reg_id":"muktopath","mer_pas_key":"Test@123"},"req_timestamp":"2020-01-07 12:40:01 GMT+6","trns_info":{"ord_det":"From-eKsheba","ord_id":"000178.0000008.20200107.1","trnx_amt":"20","trnx_currency":"BDT","trnx_id":"888f51780f0176b93bab1c5d4283f54f"}}';
        // echo '<pre>';
        // print_r($param);
        // echo '</pre>';die();
       
        
        $param = json_encode($param);

       

        $secret_key = $this->createSecretKey($param);

        
        
        if($secret_key){
            $redirectUrl = 'https://pg.ekpay.gov.bd/ekpaypg/v1?sToken='.$secret_key.'&trnsID='.$this->refTranNo;
            return $redirectUrl;
        }else{
            
            return '';
        }
        

    }
    
    public function payment($order,$user,$course_enrollments){

        $this->sUrl = config('global.api_url').'/learner-course/paymentStatus/'.$order->order_number.'/ekpay/success';
        $this->fUrl = config('global.api_url').'/learner-course/paymentStatus/'.$order->order_number.'/ekpay/fail';
        $this->cUrl = config('global.api_url').'/learner-course/paymentStatus/'.$order->order_number.'/ekpay/cancel';

        // $this->sUrl = 'http://training.eksheba.gov.bd/application/paymentStatus/'.$order->order_number.'/ekpay/success';
        // $this->fUrl = 'http://training.eksheba.gov.bd/application/paymentStatus/'.$order->order_number.'/ekpay/fail';
        // $this->cUrl = 'http://training.eksheba.gov.bd/application/paymentStatus/'.$order->order_number.'/ekpay/cancel';

        $this->requestId = $order->transactionid;
        $this->refTranNo = $order->transactionid;
        $this->refTraDate = date('Y-m-d H:i:s');

        

        $this->accounts= $order->transactionid;
        $this->amount= $order->amount;
        $this->totalAmount= $order->amount;

        $param = array();
        $param['cust_info'] = array("cust_email"=>(filter_var($user->email, FILTER_VALIDATE_EMAIL)?$user['email']:''),"cust_id"=>$user->id, "cust_mail_addr"=>"eKsheba","cust_mobo_no"=>"+88".$user->phone,"cust_name"=>$user->name);
        $param["feed_uri"] = array("s_uri"=>$this->sUrl,"f_uri"=>$this->fUrl,"c_uri"=>$this->cUrl);
        $param["ipn_info"] = array("ipn_channel" => "2","ipn_email" => "munir019@gmail.com","ipn_uri" => "http://muktopaath.gov.bd/");
        $param["mac_addr"] = "103.48.18.34";
        $param["mer_info"] = array("mer_reg_id"=>$this->merRegId, "mer_pas_key"=>$this->merPasKey);
        $param["req_timestamp"] = $this->refTraDate." GMT+6";
        $param["trns_info"] = array("ord_det"=>"From-eKsheba","ord_id"=>$order->order_number,"trnx_amt"=>"$this->totalAmount","trnx_currency"=>"BDT","trnx_id"=>"$this->refTranNo");
        // $param["partner_name"] = $course_enrollments->courseBatch->owner->institution_name;
        // $param["parter_username"] = $course_enrollments->courseBatch->owner->username;
        
        return $param = json_encode($param);

        // $param = '{"cust_info":{"cust_email":"munir019@yahoo.com","cust_id":233,"cust_mail_addr":"eKsheba","cust_mobo_no":"+8801717303200","cust_name":"MD. MUNIR hossain"},"feed_uri":{"s_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/success","f_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/fail","c_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatus\/000178.0000008.20200107.1\/ekpay\/cancel"},"ipn_info":{"ipn_channel":"3","ipn_email":"munir019@gmail.com","ipn_uri":"http:\/\/training.eksheba.gov.bd\/application\/paymentstatusupdate\/000178.0000008.20200107.1\/ekpay\/success"},"mac_addr":"103.48.18.34","mer_info":{"mer_reg_id":"muktopath","mer_pas_key":"Test@123"},"req_timestamp":"2020-01-07 12:40:01 GMT+6","trns_info":{"ord_det":"From-eKsheba","ord_id":"000178.0000008.20200107.1","trnx_amt":"20","trnx_currency":"BDT","trnx_id":"888f51780f0176b93bab1c5d4283f54f"}}';
        // echo '<pre>';
        // print_r($param);
        // echo '</pre>';die();

        return $secret_key = $this->createSecretKey($param);

        

        if($secret_key){
            $redirectUrl = 'https://pg.ekpay.gov.bd/ekpaypg/v1?sToken='.$secret_key.'&trnsID='.$this->refTranNo;
            return $redirectUrl;
        }else{
            
            return '';
        }
        //return $secret_key;
        // if(!empty($result)){
        //     if($result['msg_code']){
        //         $responsA = array();
        //         $responsA['code'] = $result['msg_code'];
        //         $responsA['url'] = 'https://pg.ekpay.gov.bd/ekpaypg/v1?sToken='.$result['secure_token'].'&trnsID='.$this->refTranNo;
        //        return $responsA;
        //     }else{
        //         return 0;
        //     }
            
        // }else{
        //     return 0;
        // }

    }

    private function createSecretKey($body){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->ekPayTokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = curl_exec($ch);
        $resultdata = json_decode($result,true);
        // echo '<pre>';
        // print_r($resultdata);
        // echo '</pre>';die();

        return $resultdata['secure_token'];
        if(isset($resultdata['secure_token']))
            $secret_key = $resultdata['secure_token'];
        else $secret_key = '';

        return $secret_key;

        // if (curl_errno($ch)) {
        //     echo 'Request Error:' . curl_error($ch);
        // }
        // $result = json_decode($result,true);


          

        // if(isset($result['secure_token']))
        //     $secret_key = $result['secure_token'];
        // else $secret_key = '';

        // return $result;
    }
}