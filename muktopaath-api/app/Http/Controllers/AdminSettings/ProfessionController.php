<?php

namespace App\Http\Controllers\AdminSettings;

use App\Interfaces\AdminSettings\ProfessionRepositoryInterface;
use App\Repositories\Validation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSettings\Profession;
use App\Models\AdminSettings\WorkingField;

class ProfessionController extends Controller
{
    private  $professionRepository;
    private $val;

    public function __construct(ProfessionRepositoryInterface $professionRepository,Validation $val) 
    {
        $this->professionRepository = $professionRepository;
        $this->val = $val;
    }
    
    public function professions()
    {
        return $this->professionRepository->allProfession();
    }

    public function fieldByprofession($profession_id){
        
        return $this->professionRepository->getfields($profession_id);

    }

    
    public function store(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'status'                  => 'required',
            'order_number'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->professionRepository->addProfession($request->all());
    }

  
    public function update(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'status'                  => 'required',
            'order_number'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->professionRepository->updateProfession($request->all());
    }

    
    public function destroy($id)
    {
        return $this->professionRepository->deleteProfession($id);
    }
}