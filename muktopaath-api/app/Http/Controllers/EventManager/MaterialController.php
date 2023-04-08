<?php

namespace App\Http\Controllers\EventManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\EventManager\MaterialRepositoryInterface;
use App\Repositories\Validation;

class MaterialController extends Controller
{
    private $materialRepository;
    private $val;

    public function __construct(MaterialRepositoryInterface $materialRepository, Validation $val)
    {
        $this->materialRepository = $materialRepository;
        $this->val = $val;
    }
    
    public function index(){
        return $this->materialRepository->allMaterials();
    }

    public function store(Request $request){
        $rules = array(
            'event_id'    => 'required',
            'user_id'    => 'required',
            'material_url'    => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->materialRepository->createMaterial($request->all());
    }

    public function update(Request $request){
        $rules = array(
            'event_id'    => 'required',
            'user_id'    => 'required',
            'material_url'    => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->materialRepository->updateMaterial($request->all());
    }

    public function destroy($id){
        return $this->materialRepository->deleteMaterial($id);
    }
}