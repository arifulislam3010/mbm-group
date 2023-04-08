<?php

namespace App\Http\Controllers\MyAccount;

use App\Http\Controllers\Controller;
use App\Models\Myaccount\PasswordReset;
use App\Interfaces\Myaccount\RatingFeedbackInterface;
use App\Repositories\Validation;
use Illuminate\Http\Request;

class RatingFeedbackController extends Controller
{

	private  $ratingFeedbackRepository;
    private $val;

    public function __construct(RatingFeedbackInterface $ratingFeedbackRepository, Validation $val) 
    {
        $this->ratingFeedbackRepository = $ratingFeedbackRepository;
        $this->val = $val;
    }
    
    public function view()
    {
        return $this->ratingFeedbackRepository->view();
    }

    public function view_all()
    {
        return $this->ratingFeedbackRepository->view_all();
    }

    public function approve($id){
        return $this->ratingFeedbackRepository->approve($id);
    }

   
    public function store(Request $request){

    	$rules = array(
            'feedback'        => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->ratingFeedbackRepository->store($request->all());
    }

    public function update(Request $request){

    	$rules = array(
            'feedback'        => 'required',
            'id'		      => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->ratingFeedbackRepository->update($request->all());
    }
}