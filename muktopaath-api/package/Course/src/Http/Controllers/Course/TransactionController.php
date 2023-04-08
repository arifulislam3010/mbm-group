<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Interfaces\TransactionInterface;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Repositories\Validation;

use Auth;
 
class TransactionController extends Controller
{ 

    private  $transactionRepository;
    private  $val;

    public function __construct(TransactionInterface $transactionRepository, Validation $val) 
    {
        $this->transactionRepository = $transactionRepository;
        $this->val = $val;
    }


    //request function to store payment
    public function create(Request $request){
        
        $rules = array(
            'course_batch_id'        => 'required',
            'syllabus_id'            => 'required',
            'person'                 => 'required',
            'type'                   => 'required',
            'amount'                 => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return  $this->transactionRepository->create($request);
    }

    public function delete($id){

        return  $this->transactionRepository->delete($id);
    }
}