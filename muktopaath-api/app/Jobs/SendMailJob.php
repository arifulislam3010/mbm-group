<?php

namespace App\Jobs;
use App\Mail\MailSender;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMailJob implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    public function __construct($data)
    { 
        $this->data = $data;
    }

    public function handle()
    {
//        $email = new SendEmail($this->data['subject'], $this->data['short_name'],$this->data['template'], $this->data);
//        Mail::to($this->data['to'])->send($email);

        //$details = new TestMail($this->data['subject'], $this->data['short_name'],$this->data['template'], $this->data);

//        $details = [
//            'title' => 'Mail from ItSolutionStuff.com',
//            'body' => 'This is for testing email using smtp'
//        ];
//        Mail::to($this->data['to'])->send($details);
//        return "Basic Email Sent. Check your inbox.";
        $email = new MailSender($this->data['subject'], $this->data['short_name'],$this->data['template'], $this->data);
        Mail::to($this->data['to'])->send($email);
    }
}