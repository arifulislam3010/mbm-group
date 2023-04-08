<?php

namespace App\Http\Controllers\EventManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\EventManager\AttendanceRepositoryInterface;
use App\Repositories\Validation;

class AttendanceController extends Controller
{
    private $attendanceRepository;
    private $val;

    public function __construct(AttendanceRepositoryInterface $attendanceRepository, Validation $val)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->val = $val;
    }
    
    public function index(){
        return $this->attendanceRepository->allAttendances();
    }

    public function store(Request $request){
        $rules = array(
            'event_id'    => 'required',
            'attend_time'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->attendanceRepository->createAttendance($request->all());
    }

    public function update(Request $request){
        $rules = array(
            'event_id'    => 'required',
            'attend_time'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->attendanceRepository->updateAttendance($request->all());
    }

    public function destroy($id){
        return $this->attendanceRepository->deleteAttendance($id);
    }
}