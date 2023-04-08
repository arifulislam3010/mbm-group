<?php

namespace App\Http\Controllers\AdminSettings;

use App\Interfaces\AdminSettings\WorkingFieldRepositoryInterface;
use App\Repositories\Validation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkingFieldController extends Controller
{
    private  $workingFieldRepository;
    private $val;

    public function __construct(WorkingFieldRepositoryInterface $workingFieldRepository,Validation $val) 
    {
        $this->workingFieldRepository = $workingFieldRepository;
        $this->val = $val;
    }

    public function store(Request $request)
    {
        
        $rules = array(
            'title'                  => 'required',
            'status'                  => 'required',
            'profession_id'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->workingFieldRepository->addWf($request->all());
    }

   
    public function update(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'status'                  => 'required',
            'profession_id'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }
        
        return $this->workingFieldRepository->updateWf($request->all());
    }

    
    public function destroy($id)
    {
        return $this->workingFieldRepository->deleteWf($id);
    }
}