<?php

namespace App\Http\Controllers\EventManager;

use App\Http\Controllers\Controller;
use App\Models\EventManager\Review;
use Illuminate\Http\Request;
use App\Models\EventManager\Event;
use App\Interfaces\EventManager\ReviewRepositoryInterface;
use App\Repositories\Validation;


class ReviewController extends Controller
{
    private $reviewRepository;
    private $val;

    public function __construct(ReviewRepositoryInterface $reviewRepository, Validation $val)
    {
        $this->reviewRepository = $reviewRepository;
        $this->val = $val;
    }
    
    public function index()
    {
        return $this->reviewRepository->allReview();
    }
    
    public function store(Request $request)
    {
        $rules = array(
            'event_id'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->reviewRepository->addReview($request->all());
    }

    public function update(Request $request){
        $rules = array(
            'event_id'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->reviewRepository->updateReview($request->all());
    }

    
    public function destroy($id){
        return $this->reviewRepository->deleteReview($id);
    }
    
}