<?php

namespace Muktopaath\Course\Lib;


Class SmsClass
{
	public $successStatus = 200;
    protected $api_base_url = 'http://bulkmsg.teletalk.com.bd/api/sendSMS';
    protected $sms_api_user = 'a2iAspire';
    protected $sms_api_pwd = 'A2ist2#0155';
    protected $pre_verify_txt = 'Muktopaath verification code :';

  
    public function send($phone,$message)
    {

        $json = array();
        $json['auth'] = array("username"=>"a2iAspire",
            "password"=>"A2ist2#0155",
            "acode"=>"1005072");
        $json["smsInfo"] = array("message"=>urldecode($message),
            "masking" => "16345",//"01552146224",
            "msisdn" => [$phone]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://bulkmsg.teletalk.com.bd/api/sendSMS",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($json,JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => array(
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
    }
    
}