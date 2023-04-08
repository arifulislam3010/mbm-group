<?php

namespace App\Http\Controllers\EventManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\EventManager\EventRepositoryInterface;
use App\Repositories\Validation;
use App\Models\EventManager\Event;
use App\Models\EventManager\EventUser;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    private $eventRepository;
    private $val;

    public function __construct(EventRepositoryInterface $eventRepository, Validation $val)
    {
        $this->eventRepository = $eventRepository;
        $this->val = $val;
    }

    public function details($id)
    {
        $event = Event::find($id);
        return $event;
    }

    public function allUsers($event_id)
    {
      
        
        $myaccount = config()->get('database.connections.my-account.database');
        $event = config()->get('database.connections.event.database');
        
        //$event=DB::SELECT("SELECT b1.event_id as event_id, participant.name as participant,a1.photo as photo, a1.name as createdBy,a2.name as updatedBy,a1.username as createdBy_un,a2.username as updatedBy_un FROM v3_myaccount.users as participant, v3_myaccount.users as a1,v3_myaccount.users as a2,v3_event_manager.event_users as b1 where participant.id=b1.user_id and a1.id=b1.created_by and a2.id=b1.updated_by AND b1.event_id=30;");
        $event = DB::table($myaccount.'.users as participant')
        ->select('b1.event_id as event_id','participant.name as participant','a1.photo as photo','a1.name as createdBy','a2.name as updatedBy','a1.username as createdBy_un','a2.username as updatedBy_un')
        ->crossJoin($myaccount.'.users as a1')
        ->crossJoin($myaccount.'.users as a2')
        ->crossJoin($event.'.event_users as b1')
        ->whereRaw('participant.id = b1.user_id')
        ->whereRaw('a1.id = b1.created_by')
        ->whereRaw('a2.id = b1.updated_by')
        ->where('b1.event_id','=',$event_id)
        ->get();

        
        return $event;
    }

    public function allMaterials($event_id){
        $material = DB::table('materials')
        ->where('event_id', '=', $event_id)
        ->get();
                
        return $material;
    }
    

    public function all()
    {
        $res = Event::customPaginate();
        return response()->json($res);
    }
    
    public function index(Request $request){
        return $this->eventRepository->allEvents($request->all());
    }

    public function store(Request $request){
        $rules = array(
            'title'    => 'required',
            'type'    => 'required',
            'instructor'    => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->eventRepository->createEvent($request->all());
    }

    public function update(Request $request){
        $rules = array(
            'title'   => 'required',
            'type'    => 'required',
            'instructor'    => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->eventRepository->updateEvent($request->all());
    }

    public function destroy($id){
        return $this->eventRepository->deleteEvent($id);
    }

    public function onlyTrashed()

    {

        $event = Event::onlyTrashed()->get();

        return response()->json($event, 200);

    }
     
        
}