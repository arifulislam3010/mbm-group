<?php

if (!class_exists('Client')) {
    require_once dirname(__FILE__) . '/../autoload.php';
}

class IDP_Exception_Message
{
    public function missingClientId(){
        echo 'Missing Client ID.';
        exit;
    }

    public function missingClientSecret(){
        echo 'Missing Client ID.';
        exit;
    }

    public function missingRedirectUri(){
        echo 'Missing Redirect Uri.';
        exit;
    }
}
