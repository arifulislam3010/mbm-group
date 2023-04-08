<?php

namespace Subscription\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Subscription\Interfaces\PackageInterface;
use App\Repositories\Validation;

class PackageController extends Controller
{
    private  $packageRepository;
    private  $val;

    public function __construct(PackageInterface $packageRepository, Validation $val) 
    {
        $this->packageRepository = $packageRepository;
        $this->val = $val;
    }

    public function index()
    {
        return $this->packageRepository->view();
    }

    public function show($id){
        return $this->packageRepository->show($id);
    }

    public function store(Request $request){

        $rules = array(
            'title'              => 'required',
            'product_id'        => 'required'
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->packageRepository->store($request->all());
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
        
        return $this->packageRepository->update($request->all());
    }

    public function delete($id){
        return $this->packageRepository->delete($id);
    }
}