<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Interfaces\SAttendanceRepositoryInterface;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Auth;
 
class AttendanceController extends Controller
{

    private  $attendanceRepository;

    public function __construct(SAttendanceRepositoryInterface $attendanceRepository) 
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function index($syllabus_id){
        return  $this->attendanceRepository->list($syllabus_id);
    }

    public function download($syllabus_id){
        $data  = $this->attendanceRepository->list($syllabus_id);
        
        return Excel::download(new AttendanceExport($data), 'users.xlsx');
    }

    public function store(Request $request,$syllabus_id){
       return  $this->attendanceRepository->attend($syllabus_id);
    }

    public function update(Request $request,$syllabus_id){
       return  $this->attendanceRepository->update($syllabus_id);
    }
}