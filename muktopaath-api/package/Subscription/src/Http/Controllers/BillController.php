<?php

namespace Subscription\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Subscription\Interfaces\BillInterface;
use App\Repositories\Validation;

class BillController extends Controller
{
    private  $billRepository;
    private  $val;

    public function __construct(BillInterface $billRepository, Validation $val) 
    {
        $this->billRepository = $billRepository;
        $this->val = $val;
    }

    public function index()
    {
        return $this->billRepository->view();
    }

    public function show($id){
        return $this->billRepository->show($id);
    }

    public function store(Request $request){

        $rules = array(
            'package_id'           => 'required',
            'month'           => 'required',
            'amount'               => 'required',
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->billRepository->store($request->all());
    }

    public function update(Request $request){

        $rules = array(
            'title'              => 'required',
            'product_id'        => 'required',
            'id'           => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }
        
        return $this->billRepository->update($request->all());
    }

    public function approve(Request $request){
         $rules = array(
            'id'           => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->billRepository->approve($request);
    }

    public function delete($id){
        return $this->billRepository->delete($id);
    }
}