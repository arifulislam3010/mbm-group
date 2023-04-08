<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Interfaces\Assessment\TimelineRepositoryInterface;
use App\Repositories\Validation;
use Illuminate\Http\Request;

class TimelineCommentsController extends Controller
{

    private $timelineRepository;
    private $val;
    
    public function __construct(TimelineRepositoryInterface $timelineRepository, Validation $val)
    {
        $this->timelineRepository = $timelineRepository;
        $this->val = $val;
    }

    public function view(){

        return $this->timelineRepository->viewall();
    }

    public function store(Request $request){

        $rules = array(
            'comment'                  => 'required',
            'timeline_id'               => 'required',
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->timelineRepository->comment();
    }

    public function commentUpdate(Request $request){

        $rules = array(
            'comment'                  => 'required',
            'timeline_id'               => 'required',
            'id'                       => 'required'
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->timelineRepository->updateComment($request['id']);

    }

    public function update(Request $request,$id){

        $rules = array(
            'type'                  => 'required',
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->timelineRepository->update($id);
    }

    public function show($id){
        return $this->timelineRepository->view($id);
    }
}
