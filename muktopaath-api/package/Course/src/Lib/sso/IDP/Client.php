<?php

if (!class_exists('Client')) {
    require_once dirname(__FILE__) . '/autoload.php';
}

class IDP_Client
{
    private $config;

    public function __construct(){
        $this->config();
        $this->setConfig('app_host',$_SERVER['HTTP_HOST']);
    }

    public function setClientId($clientId){
        $this->setConfig('clientId',$clientId);
    }

    public function setClientSecret($clientSecret){
        $this->setConfig('clientSecret',$clientSecret);
    }

    public function setRedirectUri($redirectUri){
        $this->setConfig('redirectUri',$redirectUri);
    }

    public function loginRequest()
    {
        if($this->checkConfig()){
            $auth = new IDP_Auth_OAuth();
            return $auth->createAuthUrl($this->config);
        }
    }

    public function logoutRequest()
    {
        if($this->checkConfig()){
            $auth = new IDP_Auth_OAuth();
            return $auth->createLogoutUrl($this->config);
        }
    }

    public function responseRequest($request){
        $exception = new IDP_Exception_Message();
        if(!$this->config['clientId']) $exception->missingClientId();
        else if(!$this->config['clientSecret']) $exception->missingClientSecret();

        $auth = new IDP_Auth_OAuth();
        return $auth->responseRequest($this->config,$request);
    }

    private function setConfig($key,$data){
        $this->config[$key] = $data;
    }

    private function config(){
        $this->config = array(
            'clientId'=>'',
            'clientSecret'=>'',
            'redirectUri'=>'',
            'app_host'=>''
        );
    }

    private function checkConfig(){
        $exception = new IDP_Exception_Message();
        if(!$this->config['clientId']) $exception->missingClientId();
        else if(!$this->config['clientSecret']) $exception->missingClientSecret();
        else if(!$this->config['redirectUri']) $exception->missingRedirectUri();
        else return true;
        return false;
    }
}
