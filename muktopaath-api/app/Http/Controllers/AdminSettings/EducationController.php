<?php

namespace App\Http\Controllers\AdminSettings;

use App\Http\Controllers\Controller;
use App\Interfaces\AdminSettings\EduLevelRepositoryInterface;
use App\Repositories\Validation; 
use Illuminate\Http\Request;
use App\Models\AdminSettings\EducationLevel;

class EducationController extends Controller
{
    private $eduLevelRepository;
    private $val;
    
    public function __construct(EduLevelRepositoryInterface $eduLevelRepository, Validation $val)
    {
        $this->eduLevelRepository = $eduLevelRepository;
        $this->val = $val;
    }
    
    public function levels(){
    	return $this->eduLevelRepository->allLevel();
    }

    public function store(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'weight'                  => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->eduLevelRepository->addEduLevel($request->all());
    }

    public function update(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'weight'                  => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->eduLevelRepository->updateEduLevel($request->all());

    }

    
    public function destroy($id)
    {
        return $this->eduLevelRepository->deleteEduLevel($id);
    }

}