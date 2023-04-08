<?php

namespace App\Http\Controllers\AdminSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Lib\OpenEncodeDecode;

class VerifyMailController extends Controller
{
    public function sendMail($id) {
        
        $data = new OpenEncodeDecode();
        
        $token = $data->encode($id);
        $base_url = config()->get('global.api_url');
        $link = $base_url.'verifymail?token='.$token;
        $subject = 'Action required: Activate your Muktopaath account now'; 
        $data = ['name'=>"ragib",'token'=>$token,'link'=>$link];
        
        Mail::send('mail.verifyaccount', $data, function($message) {
            $message->to(env('MAIL_NOREPLY_ACCOUNT'), 'Muktopaath')->subject('Action required: Activate your Muktopaath account now');
            $message->from(env('MAIL_NOREPLY_ACCOUNT'),'user');
        });
        
        return $token;
    }
}