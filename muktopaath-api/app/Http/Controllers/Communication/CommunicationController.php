<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendSmsJob;
use App\Jobs\SendMailJob;
use App\Models\AdminSettings\Category;
use App\Lib\SMS;
use Illuminate\Support\Facades\Mail;

class CommunicationController extends Controller
{
    public function mail(){

    }
}