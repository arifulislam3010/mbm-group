<?php

namespace App\Http\Controllers\AdminSettings;

use App\Interfaces\AdminSettings\CategoryRepositoryInterface;
use App\Repositories\Validation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSettings\Category;

class CategoryController extends Controller
{
    private  $categoryRepository;
    private $val;

    public function __construct(CategoryRepositoryInterface $categoryRepository,Validation $val) 
    {
        $this->categoryRepository = $categoryRepository;
        $this->val = $val;
    }
   
    public function index()
    {
    	return $this->categoryRepository->allCategories();
    }

    public function disabilities(){
        return $this->categoryRepository->disabilities();
    }

    
    public function store(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'bn_title'                  => 'required',
            'status'                  => 'required',
            'order_number'                  => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->categoryRepository->addCategory($request->all());
    }

    
    public function update(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'bn_title'                  => 'required',
            'status'                  => 'required',
            'order_number'                  => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->categoryRepository->updateCategory($request->all());

    }

    
    public function destroy($id)
    {
        return $this->categoryRepository->deleteCategory($id);
    }
}