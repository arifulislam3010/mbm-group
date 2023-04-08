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
 
class AccountsController extends Controller
{ 

    private  $accountsRepository;
    private  $val;

    public function __construct(AccountsInterface $accountsRepository, Validation $val) 
    {
        $this->accountsRepository = $accountsRepository;
        $this->val = $val;
    }

    public function payments(){
        return  $this->accountsRepository->payments();
    }

    public function batch_payments(){
        return  $this->accountsRepository->batch_payments();
    }

    public function payment_status($batch_id){
        return  $this->accountsRepository->payment_status($batch_id);
    }

    public function approve($id){
        return  $this->accountsRepository->approve($id);
    }

    public function reject($id){
        return  $this->accountsRepository->reject($id);
    }

    public function delete($id){
        return  $this->accountsRepository->delete($id);
    }

    public function overall_transactions(){
        return  $this->accountsRepository->overall_transactions();
    }

    public function view_all_requests(){
        return  $this->accountsRepository->view_all_requests();
    }

    //request function to store payment
    public function request(Request $request){
        
        $rules = array(
            'course_id'              => 'required',
            'amount'                 => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return  $this->accountsRepository->storePayment($request);
    }
}