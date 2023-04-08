<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Finance\PaymentRequestRepositoryInterface;
use App\Repositories\Validation;

class PaymentRequestController extends Controller
{
    private $paymentRequestRepository;
    private $val;

    public function __construct(PaymentRequestRepositoryInterface $paymentRequestRepository, Validation $val)
    {
        $this->paymentRequestRepository = $paymentRequestRepository;
        $this->val = $val;
    }

    public function index(){
        return $this->paymentRequestRepository->allPaymentRequest();
    }
    
    public function store(Request $request){
        $rules = array(
            'balance_id'    => 'required',
            'request_amount'    => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->paymentRequestRepository->addPaymentRequest($request->all());
    }

    public function grant(Request $request){
        $rules = array(
            'status'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->paymentRequestRepository->updatePaymentRequest($request->all());

    }

    public function deleteBalance($id)
    {
        return $this->paymentRequestRepository->deletePaymentRequest($id);
    }
}