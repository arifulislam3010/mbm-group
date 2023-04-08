<?php

namespace App\Http\Controllers\AdminSettings;

use App\Interfaces\AdminSettings\DegreeRepositoryInterface;
use App\Repositories\Validation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSettings\Degree;

class DegreeController extends Controller
{
    private  $degreeRepository;
    private $val;

    public function __construct(DegreeRepositoryInterface $degreeRepository,Validation $val) 
    {
        $this->degreeRepository = $degreeRepository;
        $this->val = $val;
    }
    
    public function degreeBylevel($level_id){
    	return $this->degreeRepository->degreeByLevel($level_id);

    }


    public function store(Request $request)
    {
        
        $rules = array(
            'title'                  => 'required',
            'education_level_id'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->degreeRepository->addDegree($request->all());
    }

   
    public function update(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'education_level_id'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }
        
        return $this->degreeRepository->updateDegree($request->all());
    }

    
    public function destroy($id)
    {
        return $this->degreeRepository->deleteDegree($id);
    }
}