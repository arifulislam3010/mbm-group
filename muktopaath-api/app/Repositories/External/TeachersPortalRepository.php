<?php

namespace App\Repositories\External;

use App\Models\Finance\Balance;
use App\Interfaces\External\TeachersPortalRepositoryInterface;

class TeachersPortalRepository implements TeachersPortalRepositoryInterface
{
    public function login(){
        $ch = curl_init();

        $headers = array(
        'Accept: application/json',
        'Content-Type: application/json'

        );
        curl_setopt($ch, CURLOPT_URL, 'http://103.69.149.41/sso/Services/Security/PublicUser/MerchantSignIn');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $body['UserName'] = 'MyGov';
        $body['Password'] = '1234567856';

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $authToken = curl_exec($ch);

        return $authToken;
    }

    public function verify(){
        return 21;
    }
    
}