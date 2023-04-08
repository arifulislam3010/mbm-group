<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Interfaces\AccountsInterface;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Repositories\Validation;

use Auth;
 
class IdcardController extends Controller
{ 



    public function print_id($id){
        return  1;
    }

}