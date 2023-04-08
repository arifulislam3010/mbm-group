<?php

namespace App\Jobs;
use App\Lib\SMS;

class SendSmsJob extends Job
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        SMS::send($this->data['to'], $this->data['message']);
    }
}