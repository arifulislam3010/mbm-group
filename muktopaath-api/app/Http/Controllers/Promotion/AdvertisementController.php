<?php

namespace App\Http\Controllers\Promotion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\Promotion\PromotionInterface;
use App\Repositories\Validation;
use Validator;

class AdvertisementController extends Controller
{

    private $promotionRepository;
    private $val;
    
    public function __construct(PromotionInterface $promotionRepository, Validation $val)
    {
        $this->promotionRepository = $promotionRepository;
        $this->val = $val;
    }

    public function list(){
        return $this->promotionRepository->list();
    }

    public function showsingle($id){
        
        return $this->promotionRepository->showsingle($id);
    }

    public function show(Request $request){

        $rules = array(
            'ad_for'                 => 'required',
            'table_id'               => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }
        return $this->promotionRepository->show($request->all());
    }

    public function store(Request $request){

        $rules = array(
            'title'                 => 'required',
            'type'                  => 'required',
            'description'           => 'required',
            'start_date'            => 'required',
            'end_date'              => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->promotionRepository->store($request->all());
    } 



    public function update(Request $request){
        $rules = array(
            'id'                    => 'required',
            'title'                 => 'required',
            'type'                  => 'required',
            'description'           => 'required',
            'start_date'            => 'required',
            'end_date'              => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->promotionRepository->update($request->all());
    } 

    public function approve($id){
        if(config()->get('global.owner_id')==1){
            return $this->promotionRepository->approve($id);
        }
    }


    public function delete($id){
            return $this->promotionRepository->delete($id);
    }

}
