<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Finance\BalanceRepositoryInterface;
use App\Repositories\Validation;

class BalanceController extends Controller
{
    private $balanceRepository;
    private $val;

    public function __construct(BalanceRepositoryInterface $balanceRepository, Validation $val)
    {
        $this->balanceRepository = $balanceRepository;
        $this->val = $val;
    }

    public function index(){
        return $this->balanceRepository->allBalance();
    }
    
    public function store(Request $request){
        $rules = array(
            'total_earn'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->balanceRepository->addBalance($request->all());
    }

    public function update(Request $request){
        $rules = array(
            'total_earn'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->balanceRepository->updateBalance($request->all());

    }

    public function deleteBalance($id)
    {
        return $this->balanceRepository->deleteBalance($id);
    }
}