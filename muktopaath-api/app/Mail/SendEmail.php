<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject, $short_name, $body, $template;
    public function __construct($subject, $short_name, $template, $body)
    {
        $this->subject = $subject;
        $this->short_name = $short_name;
        $this->template = $template;
        $this->body = $body;
    }

    
    public function build()
    {
        return $this->from(env('MAIL_NOREPLY_ACCOUNT'), $this->short_name)
        ->subject($this->subject)
        ->view('mail.'.$this->template)->with([
            'data' => $this->body,
        ]);
    }
}