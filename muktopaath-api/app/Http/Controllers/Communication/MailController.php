<?php

namespace App\Http\Controllers\Communication;

use App\Mail\MailSender;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Lib\OpenEncodeDecode;

class MailController extends Controller
{
    public function sendMail() {
        
        $data = new OpenEncodeDecode();
        
        $token = $data->encode('234');
        $base_url = config()->get('global.api_url');
        $link = $base_url.'verifymail?token='.$token;
        //return $data->decode('84349464d4f4a4f4t5', $this->stringKey);
        $subject = 'Action required: Activate your Muktopaath account now'; 
        $data = ['name'=>"ragib",'token'=>$token,'link'=>$link];
        
        Mail::send('mail.verifyaccount', $data, function($message) {
            $message->to(env('MAIL_NOREPLY_ACCOUNT'), 'Muktopaath')->subject('Action required: Activate your Muktopaath account now');
            $message->from(env('MAIL_NOREPLY_ACCOUNT'),'user');
        });
        echo "Email Sent. Check your inbox.";
    }
}