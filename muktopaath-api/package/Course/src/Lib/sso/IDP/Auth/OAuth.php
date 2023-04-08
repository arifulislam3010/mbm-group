<?php

use Firebase\JWT\JWT;

class IDP_Auth_OAuth
{
    private $config;
    private $token;
    private $request;
    private $key;
    const OAUTH2_REVOKE_URI = 'https://idp.eksheba.gov.bd/oauth2/revoke';
    const OAUTH2_TOKEN_URI = 'https://idp.eksheba.gov.bd/oauth2/token';
    const OAUTH2_AUTH_URL = 'https://idp.eksheba.gov.bd/oauth2/auth';
    const OAUTH2_LOGOUT_URL = 'https://idp.eksheba.gov.bd/oauth2/logout';

    public function createAuthUrl($config){
        $this->setConfig($config);
        $this->generateToken();
        return $this->buildLoginRequest();
    }

    public function createLogoutUrl($config){
        $this->setConfig($config);
        $this->generateToken();
        return $this->buildLogoutRequest();
    }

    private function setConfig($config){
        $this->config = $config;
    }

    private function buildLoginRequest(){
        $this->setRequest();
        return self::OAUTH2_AUTH_URL . '?' . http_build_query($this->request);
    }

    private function buildLogoutRequest(){
        $this->setRequest();
        return self::OAUTH2_LOGOUT_URL . '?' . http_build_query($this->request);
    }

    private function setRequest(){
        $this->request['client_id'] = $this->config['clientId'];
        $this->request['redirect_uri'] = $this->config['redirectUri'];
        $this->request['token'] = $this->token;
        //$this->request['key'] = $this->key;
    }

    private function generateToken(){
        $this->token = array('clientId'=>$this->config['clientId'],
            'appHost' => $this->config['app_host'],
            'ipAddress' => $this->ipAddress(),
            'time' => time());
            
        $this->key = $this->getToken(10);
        $this->token = $this->encrypt($this->token,$this->config['clientSecret']);
    }

    public function responseRequest($config,$request){
        try {
            return $this->decrypt($request['token'], $request['key'] . md5($config['clientSecret']));
        }catch (\Exception $ex){
            return null;
        }
    }

    private function encrypt($data, $key){
        return JWT::encode($data,$key,'HS256');
    }

    private function decrypt($data, $key){
        return JWT::decode($data,$key,array('HS256'));
    }

    private function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
        }

        return $token;
    }

    private function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min;
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd > $range);
        return $min + $rnd;
    }

    private function ipAddress()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }
}