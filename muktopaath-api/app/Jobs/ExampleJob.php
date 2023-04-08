<?php

namespace App\Jobs;
use App\Lib\SMS;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExampleJob extends Job
{
    // public $queue;
    // protected $phone;
    // protected $message;
    //protected $to;
    protected $data;
    
    public function __construct($data)
    {
        // $this->phone = $phone;
        
        // $this->message = $message;
        // $this->to = $to;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        
        Log::info($this->to);
        //SMS::send($this->phone, $this->message);
        // Mail::send('mail.restrictenroll', $data, function($message) use($data){
        //     $message->to($data['email'])->subject('Action required: You are invited to enroll this Muktopaath course
        //     now');
        //     $message->from(env('MUKTOPAATH_NOREPLY_ACCOUNT'),'Muktopaath');
        // });

        Mail::from(env('MUKTOPAATH_NOREPLY_ACCOUNT'),'Muktopaath')
        ->subject('Confirmation Email')
        ->view('mail.restrictenroll')->with(['details'=>$this->details]);
        
    }
}