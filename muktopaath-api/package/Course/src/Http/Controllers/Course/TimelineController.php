<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Muktopaath\Course\Interface\TimelineRepositoryInterface;
use App\Repositories\Validation;
use Illuminate\Http\Request;

class TimelineController extends Controller
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
            'type'                  => 'required',
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->timelineRepository->store($request);
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

    public function delete($id){
        return $this->timelineRepository->delete($id);
    }

    public function deleteComment($id){
        return $this->timelineRepository->deleteComment($id);
    }

    public function show($id){
        return $this->timelineRepository->view($id);
    }
}
