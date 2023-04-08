<?php 
namespace App\Lib\sso;
require_once 'vendor/autoload.php';
require_once 'IDP/autoload.php';

class IdpSso
{
    public function user($data){
        // return $data;
        $client = new \IDP_Client();
        $client->setClientId('nwzFcJD2Rv2hK7agoyyj');
        $client->setClientSecret('S9yGNoP1uB7lI4CsFkhq.X5hul7bXtL');
        $data = $client->responseRequest($data);
        return $data;
    }
}



