<?php

namespace Subscription\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Subscription\Interfaces\CustomerPackageInterface;
use App\Repositories\Validation;

class CustomerPackageController extends Controller
{
    private  $customerPackageRepository;
    private  $val;

    public function __construct(CustomerPackageInterface $customerPackageRepository, Validation $val) 
    {
        $this->customerPackageRepository = $customerPackageRepository;
        $this->val = $val;
    }

    public function index()
    {
        return $this->customerPackageRepository->view();
    }

    public function show($id){
        return $this->customerPackageRepository->show($id);
    }

    public function store(Request $request){

        $rules = array(
            'package_id'           => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->customerPackageRepository->store($request->all());
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
        
        return $this->customerPackageRepository->update($request->all());
    }

    public function approve(Request $request){
         $rules = array(
            'id'           => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->customerPackageRepository->approve($request);
    }

    public function delete($id){
        return $this->customerPackageRepository->delete($id);
    }
}