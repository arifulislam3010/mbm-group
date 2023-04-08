<?php

namespace Subscription\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Subscription\Interfaces\ProductInterface;
use App\Repositories\Validation;

class ProductController extends Controller
{

    private  $productRepository;
    private  $val;

    public function __construct(ProductInterface $productRepository, Validation $val) 
    {
        $this->productRepository = $productRepository;
        $this->val = $val;
    }

    public function index()
    {
        return $this->productRepository->view();
    }

    public function store(Request $request){

        $rules = array(
            'title'              => 'required',
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->productRepository->store($request->all());
    }

    public function update(Request $request){

        $rules = array(
            'title'              => 'required',
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }
        
        return $this->productRepository->update($request->all());
    }

    public function delete($id){
        return $this->productRepository->delete($id);
    }
}