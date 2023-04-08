<?php

namespace App\Http\Controllers\EventManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\EventManager\EventUserRepositoryInterface;
use App\Repositories\Validation;

class EventUserController extends Controller
{
    private $eventUserRepository;
    private $val;

    public function __construct(EventUserRepositoryInterface $eventUserRepository, Validation $val)
    {
        $this->eventUserRepository = $eventUserRepository;
        $this->val = $val;
    }
    
    public function index(){
        return $this->eventUserRepository->allUserEvents();
    }

    public function store(Request $request){
        $rules = array(
            'event_id'   => 'required',
            'user_id'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->eventUserRepository->createUserEvent($request->all());
    }

    public function update(Request $request){
        $rules = array(
            'event_id'   => 'required',
            'user_id'    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->eventUserRepository->updateUserEvent($request->all());
    }

    public function destroy($id){
        return $this->eventUserRepository->deleteUserEvent($id);
    }
}