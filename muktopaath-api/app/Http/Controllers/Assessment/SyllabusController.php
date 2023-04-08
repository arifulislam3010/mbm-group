<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment\Syllabus;
use App\Models\Assessment\PartyConferenceIntegration;
use App\Models\Assessment\ConferenceToolsDetails;
use App\Models\Myaccount\Sharing;
use App\Models\Assessment\SyllabusStatus;
use App\Models\Question\Question;
use App\Interfaces\Assessment\TimelineRepositoryInterface;
use App\Repositories\Validation;
use App\Http\Resources\Assessment\SyllabusResource as SyllabusResources;
use App\Http\Resources\Assessment\SyllabusResourceAdmin as SyllabusResourceAdmin;
use Carbon\Carbon;
 
use DB;
use App\Lib\ContentBank;
class SyllabusController extends Controller
{

    private $val;
    private $timelineRepository;
    use ContentBank;
    
    public function __construct(Validation $val,TimelineRepositoryInterface $timelineRepository)
    {
        $this->val = $val;
        $this->timelineRepository = $timelineRepository;
    }

    public function index(Request $request,$id){

        // $date_time = date("Y-m-d H:i:s", );
        $date_time = date("Y-m-d H:i:s");

        $type = $request->has('type')?$request->type:null;
        $syllabus = SyllabusResources::collection(Syllabus::where('parent_id',null)->where('course_batch_id',$id)
         ->when($type, function ($query) use($type,$date_time) {
          if($type=='over'){ 
              return $query->where('syllabuses.end_date','<',$date_time);
          }else if($type=='now'){
              return $query->where('syllabuses.end_date','>=',$date_time);
          }
        })
        ->orderBy('syllabuses.start_date','ASC')->get());
        return response()->json($syllabus);
    }

    public function createOne(Request $request){

        $data = $request->all();

        $rules = array(
            'course_batch_id'        => 'required',
            'title'                  => 'required',
            'start_date'             => 'required',
            'get_hrs'                => 'required',
            'get_mins'               => 'required',
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        //$credentials = $data['credentials'];

        $duration = (int)$request['get_hrs'] * 60;
        $duration += (int)$request['get_mins'];

        $startdate  = date('Y-m-d H:i:s',strtotime($request['start_date']));
        $data['end_date'] = date('Y-m-d H:i:s',strtotime($startdate . ' +' . $duration . ' Minutes'));

        $db = config()->get('database.connections.my-account.database');

        $duration = (int)$request['get_hrs'] * 60;
        $duration += (int)$request['get_mins'];

       


        $syllabus = new Syllabus;
        $syllabus->title               = $request['title'];
        $syllabus->parent_id           = null;
        $syllabus->status              = 1;
        $syllabus->order_number        = 1;
        $syllabus->course_batch_id     = $request['course_batch_id'];
        $syllabus->live_class_url      = $request['live_class_url'];
        $syllabus->live_class_url_type = isset($request['live_class_url_type'])?$request['live_class_url_type']:null;
        $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($request['start_date']));
        $syllabus->end_date = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $duration . ' Minutes'));
        $syllabus->created_by = config()->get('global.user_id');
        $syllabus->duration = $duration;
        $syllabus->save();

        if( isset($data['live_class_url_type']) && $data['live_class_url_type'] == 3){


                
                $user_id = config()->get('global.user_id');
                $data_check = PartyConferenceIntegration::where('user_id', $user_id)->first();


                $chk =  json_decode($data_check->credential_info);   

                $zoom_data['email']             = $chk->email;
                $zoom_data['zoom_api_key']      = $chk->zoom_api_key;
                $zoom_data['zoom_api_secret']   = $chk->zoom_api_secret;
                $zoom_data['topic']             = $data['title'];
                $zoom_data['start_date_time']   = $data['start_date'];
                $zoom_data['end_date_time']     = $data['end_date'];  

                gmdate('d.m.Y H:i', strtotime('2012-06-28 23:55'));

                if(!empty($data_check)){
                    
                    $zoom_data['user_id']   = $chk->zoom_user_id;
                    
                    $schedule_content_data = ConferenceToolsDetails::where([
                            ['app_type', '=' ,'zoom'],
                            ['syllabus_id', $syllabus->id]
                        ])
                        ->whereNotNull('meeting_id')
                        ->first();
                
                    $zoom_meeting = new ZoomMeeting;
                    if(empty($schedule_content_data)){

                        $metting = $zoom_meeting->createZoomMeeting($zoom_data);


                        $metting = json_decode($metting->getBody());

                        //return response()->json($metting);
                        
                        $schedule_content = ConferenceToolsDetails::updateOrCreate([
                            'syllabus_id' => $syllabus->id,
                            'app_type'      => 'zoom'
                        ],[
                            'external_link'     => $metting->join_url,
                            'meeting_id'        => $metting->id,
                            'meeting_password'  => $metting->password,
                            'api_key'           => $chk->zoom_api_key,
                            'created_by'        => config()->get('global.user_id')
                        ]);

                    }
                    else{
                        $zoom_data['password'] = $schedule_content_data['meeting_password'];
                        $metting = $zoom_meeting->updateZoomMeeting($zoom_data,$schedule_content_data['meeting_id']);
                        // $metting = json_decode($metting->getBody());
                        
                        // $schedule_content = ClassScheduleContent::updateOrCreate([
                        //     'class_schedule_id' => $update_others->id,
                        //     'content_type'      => 'live'
                        // ],[
                        //     'external_link'     => $metting->join_url,
                        //     'meeting_id'        => $metting->id,
                        //     'meeting_password'  => $metting->password,
                        //     'api_key'           => $data['zoom_api_key']
                        // ]);
                    }
                }
                
            }

        $data = Syllabus::select('syllabuses.*','u.name as creator')
                ->join($db.'.users as u','u.id','syllabuses.created_by')
                ->where('syllabuses.id',$syllabus->id)
                ->first();

        $req['post']  = $data['creator'].' created a new session named '.$data['title'];
        $req['type']  = 'schedule';
        $req['content_url']  = '';
        $req['course_batch_id'] = $request['course_batch_id'];
        $req['syllabus_id'] = $syllabus->id;

        if($this->timelineRepository->store($req)){
            return response()->json(['data' => $data,'message' => 'Session created successfully'],201);
        };

    }

    public function configure_api(Request $request){

        $data = $request->all();

        $rules = array(
            'zoom_api_key'        => 'required',
            'zoom_api_secret'     => 'required',
            'email'          => 'required'
        );

    // DB::beginTransaction();
    // try{

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }


        $zoom_data['email']             = $data['email'];
        $zoom_data['zoom_api_key']      = $data['zoom_api_key'];
        $zoom_data['zoom_api_secret']   = $data['zoom_api_secret'];


        $zoom_meeting = new ZoomMeeting;
            
        $metting = $zoom_meeting->getZoomUserInfo($zoom_data);
        if($metting->getBody()){
            $metting = json_decode($metting->getBody());
        }else{
            $metting = null;
        }

        $zoom_data['zoom_user_id'] = $metting->id;


        if($metting->id != ''){

            $res = PartyConferenceIntegration::updateOrCreate([
                'user_id' => config()->get('global.user_id')
            ],[
                'credential_info'   => json_encode($zoom_data),
                'app_name'          => 'zoom',
                'created_by'        => config()->get('global.user_id'),
                'updated_by'        => config()->get('global.user_id'),
                'user_id'           => config()->get('global.user_id'),
            ]);
        }
        // DB::commit();
        return response()->json(
            [
                'message' => 'Api configured successfully',
                'data' => $res,
                'status'=>1,
            ]);
        
        // }catch (\Exception $e) {
        //     DB::rollback();
        //     return response()->json(
        //     [
        //         'message' => 'Api configured fail',
        //         'status'=>2,
        //     ]);
        // }
    }

    public function check_configuration(){

        $res = PartyConferenceIntegration::where('user_id',config()
            ->get('global.user_id'))
            ->where('app_name','zoom')
            ->first();

        return response()->json($res);
    }

    public function classwork_createOne(Request $request){

        $rules = array(
            'course_batch_id'       => 'required',
            'title'                 => 'required',
            // 'learning_content_id'   => 'required',
            'content_type'          => 'required',
            // 'cotitle'         => 'required'
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $db = config()->get('database.connections.my-account.database');

        $duration = 0;
        
        $data = $request->all();
        if(isset($data['content'])){
            $contentdata = $this->insertContent($data);
            if($contentdata!=''){
                $learning_content_id = $contentdata->id;
                $duration = $contentdata->duration;
            }
        }else{
            if(isset($request['duration'])){
                $duration = $request['duration'];

                $timearray = explode(":",$duration);

                $h = isset($timearray[0])?$timearray[0]:0;
                $m = isset($timearray[1])?$timearray[1]:0;
                $s = isset($timearray[2])?$timearray[2]:0;

                $durationCount = (int)$h * 60;
                $durationCount += (int)$m;
            }else{
                $duration = (int)$request['get_hrs'] * 60;
                $duration += (int)$request['get_mins'];

                $durationCount = $duration;
            }

           
            $learning_content_id = $request['learning_content_id'];
        }


        

        $syllabus = new Syllabus;
        $syllabus->title               = $request['title'];
        $syllabus->parent_id           = $request['parent_id'];
        $syllabus->status              = 1;
        $syllabus->order_number        = 1;
        $syllabus->course_batch_id     = $request['course_batch_id'];
        if($request['start_date']!=null){
            $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($request['start_date']));
            $syllabus->end_date = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $durationCount . ' Minutes'));
        }
        $syllabus->learning_content_id = $learning_content_id;
        $syllabus->content_title       = $request['content_title'];
        $syllabus->content_type        = $request['content_type'];
        $syllabus->created_by = config()->get('global.user_id');
        $syllabus->duration = $duration;
        $syllabus->save();

        $data =  Syllabus::select('syllabuses.*','u.name as creator')
                    ->join($db.'.users as u','u.id','syllabuses.created_by')
                    ->where('syllabuses.id',$syllabus->id)
                    ->first();

        $req['post']  = 'A new classwork '.$syllabus->content_type.' named '.$data['title'].' added';
        $req['type']  = 'classwork';
        $req['content_url']  = '';
        $req['course_batch_id'] = $request['course_batch_id'];
        $req['syllabus_id'] = $syllabus->id;
        $req['parent_id'] = $request['parent_id'];

        if($this->timelineRepository->store($req)){
            return new SyllabusResources($data);
            return response()->json(['data' => $data,'message' => 'Classwork created successfully'],201);
        };

    }

    public function viewOne($id){

        $db = config()->get('database.connections.my-account.database');
        $res = Syllabus::select('syllabuses.*','u.name as creator')
                ->join($db.'.users as u','u.id','syllabuses.created_by')
                ->first();

        return response()->json($res);
    }

    public function classwork_viewOne($id){

        $db = config()->get('database.connections.my-account.database');
        $res = Syllabus::select('syllabuses.*','u.name as creator')
                ->join($db.'.users as u','u.id','syllabuses.created_by')
                ->first();

        return response()->json($res);
    }

    public function deleteOne($id){
        $res = Syllabus::find($id);

        if($res->delete()){
            return response()->json(['message' => 'Session deletred successfully','data' => $res]);
        }
    }

    public function updateOne(Request $request){

        $rules = array(
            'course_batch_id'        => 'required',
            'title'                  => 'required',
            'start_date'             => 'required',
            'get_hrs'                => 'required',
            'get_mins'               => 'required',
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $db = config()->get('database.connections.my-account.database');


        $duration = (int)$request['get_hrs'] * 60;
        $duration += (int)$request['get_mins'];

        $syllabus = Syllabus::find($request->id);


        $syllabus->title               = $request['title'];
        $syllabus->parent_id           = null;
        $syllabus->status              = 1;
        $syllabus->order_number        = 1;
        $syllabus->course_batch_id     = $request['course_batch_id'];
        $syllabus->live_class_url      = $request['live_class_url'];
        $syllabus->learning_content_id = $request['learning_content_id'];
        $syllabus->content_title       = $request['content_title'];
        $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($request['start_date']));
        $syllabus->end_date = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $duration . ' Minutes'));
        $syllabus->content_type        = $request['content_type'];
        $syllabus->created_by = config()->get('global.user_id');
        $syllabus->duration = $duration;

        $syllabus->update();


        $data = Syllabus::select('syllabuses.*','u.name as creator')
        ->join($db.'.users as u','u.id','syllabuses.created_by')
        ->where('syllabuses.id',$syllabus->id)
        ->first();

        return response()->json(['data' => $data,'message' => 'Classwork updated successfully'],200);

    }

    public function classwork_updateOne(Request $request){

        $rules = array(
            'id'                     => 'required',
            'course_batch_id'        => 'required',
            'title'                  => 'required',
            'start_date'             => 'required',
            // 'get_hrs'                => 'required',
            // 'get_mins'               => 'required',
        );
        
        $data = $request->all();
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $db = config()->get('database.connections.my-account.database');
        $db2 = config()->get('database.connections.content-bank.database');

        $syllabus = Syllabus::find($request->id);

        if(isset($request['request_type']) && $request['request_type']=='date'){
            $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($request['start_date']));
            $syllabus->update();

            return new SyllabusResources($data = Syllabus::select('syllabuses.*','u.name as creator','lc.description as content_description')
            ->join($db.'.users as u','u.id','syllabuses.created_by')
            ->join($db2.'.learning_contents as lc','lc.id','syllabuses.learning_content_id')
            ->where('syllabuses.id',$syllabus->id)
            ->first());

            return response()->json(['data' => $data,'message' => 'Session time updated successfully'],200);
        }
        if(isset($request['request_type']) && $request['request_type']=='parent'){
            $syllabus->parent_id          =$request['parent_id'];
            $syllabus->update();

            return new SyllabusResources($data = Syllabus::select('syllabuses.*','u.name as creator','lc.description as content_description')
            ->join($db.'.users as u','u.id','syllabuses.created_by')
            ->join($db2.'.learning_contents as lc','lc.id','syllabuses.learning_content_id')
            ->where('syllabuses.id',$syllabus->id)
            ->first());

            return response()->json(['data' => $data,'message' => 'Class Assign successfully'],200);
        }

        if(isset($data['content'])){
            $contentdata = $this->insertContent($data);
            if($contentdata!=''){
                $learning_content_id = $contentdata->id;
                $duration = $contentdata->duration;
            }
        }else{
            $duration = $request['duration'];
            $learning_content_id = $request['learning_content_id'];
        }
        $timearray = explode(":",$duration);

        $h = isset($timearray[0])?$timearray[0]:0;
        $m = isset($timearray[1])?$timearray[1]:0;
        $s = isset($timearray[2])?$timearray[2]:0;

        $durationCount = (int)$h * 60;
        $durationCount += (int)$m;


        $syllabus->title               = $request['title'];
        $syllabus->course_batch_id     = $request['course_batch_id'];
        $syllabus->parent_id           = $request['parent_id'];
        $syllabus->status              = 1;
        $syllabus->order_number        = 1;
        $syllabus->live_class_url       = $request['live_class_url'];
        if($request['start_date']!=null){
            $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($request['start_date']));
            $syllabus->end_date = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $durationCount . ' Minutes'));
        }
        $syllabus->updated_by = config()->get('global.user_id');
        $syllabus->duration = $duration;

        $syllabus->update();


        return new SyllabusResources($data = Syllabus::select('syllabuses.*','u.name as creator','lc.description as content_description')
        ->join($db.'.users as u','u.id','syllabuses.created_by')
        ->join($db2.'.learning_contents as lc','lc.id','syllabuses.learning_content_id')
        ->where('syllabuses.id',$syllabus->id)
        ->first());

        return response()->json(['data' => $data,'message' => 'Session updated successfully'],200);

    }
    
    public function useranswers($id){

        $db = config()->get('database.connections.my-account.database');
        
        $db1 = config()->get('database.connections.content-bank.database');

        $res = SyllabusStatus::select('o.user_id','u.name as creator','syllabus_statuses.answers','syllabus_statuses.status','syllabus_statuses.file_id','syllabus_statuses.feedback_arr','syllabus_statuses.mark','lc.more_data_info','syllabus_statuses.ques_ids','syllabus_statuses.obtain_marks','syllabus_statuses.id','syllabus_statuses.created_at as submission_date')
                ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
                ->join('orders as o','o.id','ce.order_id')
                ->join('syllabuses as s','s.id','syllabus_statuses.syllabus_id')
                ->join($db1.'.learning_contents as lc','lc.id','s.learning_content_id')
                ->join($db.'.users as u','u.id','o.user_id')
                ->where('syllabus_statuses.id',$id)
                ->with('files')
                ->first();
        if($res && $res->ques_ids!=null){
            $questions = Question::wherein('id',json_decode($res->ques_ids))->get();
            $res['questions'] = $questions;
        }
        return response()->json($res);        
    }

    public function classworks(Request $request,$batch_id){
      
        $db = config()->get('database.connections.my-account.database');

        $db2 = config()->get('database.connections.content-bank.database');
        
    
        $class_id = ($request->has('class_id') && $request['class_id']!='undefined')?$request['class_id']:null;

        if(config()->get('global.owner_id')){
            
            $res = Syllabus::select('syllabuses.*','u.name as creator','lc.description as content_description','cb.total_enrollment')
                ->where('course_batch_id',$batch_id)
                ->join($db.'.users as u','u.id','syllabuses.created_by')
                ->join($db2.'.learning_contents as lc','lc.id','syllabuses.learning_content_id')
                ->join('course_batches as cb','syllabuses.course_batch_id','cb.id')
                ->when($class_id,function($query) use($class_id) {
                    return $query->where('syllabuses.parent_id',$class_id);
                })
                ->when($request->date,function($query) use($request) {
                    return $query->where('syllabuses.created_at',$request->date);
                })
                ->when($request->content_type,function($query) use($request) {
                    return $query->where('syllabuses.content_type',$request->content_type);
                })
                ->where('parent_id','!=',null)
                ->orderBy('syllabuses.id','DESC')
                ->with('class')
                ->withCount('participations')
                ->paginate(10);
            
        }else{
            $res = Syllabus::select('syllabuses.*','u.name as creator','lc.description as content_description','cb.total_enrollment')
            ->join($db.'.users as u','u.id','syllabuses.created_by')
            ->join($db2.'.learning_contents as lc','lc.id','syllabuses.learning_content_id')
            ->join('course_batches as cb','syllabuses.course_batch_id','cb.id')
            ->join('course_enrollments as ce','ce.course_batch_id','cb.id')
            ->join('orders as o','o.id','ce.order_id')
            ->when($class_id,function($query) use($class_id) {
                return $query->where('syllabuses.parent_id',$class_id);
            })
            ->when($batch_id!=0,function($query) use($batch_id) {
                return $query->where('syllabuses.course_batch_id',$batch_id);
            })
            ->when($request->date,function($query) use($request) {
                return $query->where('syllabuses.created_at',$request->date);
            })
            ->when($request->content_type,function($query) use($request) {
                return $query->where('syllabuses.content_type',$request->content_type);
            })
            ->where('parent_id','!=',null)
            ->where('o.user_id',config()->get('global.user_id'))
            ->with('class','submissions')
            ->orderBy('syllabuses.id','DESC')
            ->paginate(10);
        }

        return SyllabusResourceAdmin::collection($res);
    }

    public function results(Request $request,$id){

        $db = config()->get('database.connections.my-account.database');
        
        $db2 = config()->get('database.connections.content-bank.database');

        if(config()->get('global.owner_id')){
            $res = SyllabusStatus::select('u.name','u.email','syllabus_statuses.created_at as submission_date','lc.more_data_info','u.id as user_id','s.title','syllabus_statuses.mark as obtain_mark','syllabus_statuses.status','syllabus_statuses.id')
            ->where('syllabus_statuses.course_batch_id',$id)
            ->when($request->class_session_id,function($query) use($request) {
                return $query->where('s.parent_id',$request->class_session_id);
            })
            ->when($request->class_work_id,function($query) use($request) {
                return $query->where('s.id',$request->class_work_id);
            })
            ->join('syllabuses as s','s.id','syllabus_statuses.syllabus_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->join($db.'.users as u','u.id','o.user_id')
            ->join($db2.'.learning_contents as lc','lc.id','s.learning_content_id')
            ->orderby('syllabus_statuses.mark','DESC')
            ->paginate(10);
        }else{
            $res = SyllabusStatus::select('u.name','u.email','syllabus_statuses.created_at as submission_date','lc.more_data_info','u.id as user_id','s.title','syllabus_statuses.mark as obtain_mark','syllabus_statuses.status','syllabus_statuses.id')
            ->where('syllabus_statuses.course_batch_id',$id)
            ->when($request->class_session_id,function($query) use($request) {
                return $query->where('s.parent_id',$request->class_session_id);
            })
            ->when($request->class_work_id,function($query) use($request) {
                return $query->where('s.id',$request->class_work_id);
            })
            ->join('syllabuses as s','s.id','syllabus_statuses.syllabus_id')
            ->join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->join('orders as o','o.id','ce.order_id')
            ->join($db.'.users as u','u.id','o.user_id')
            ->join($db2.'.learning_contents as lc','lc.id','s.learning_content_id')
            ->where('o.user_id',config()->get('global.user_id'))
            ->orderby('syllabus_statuses.mark','DESC')
            ->paginate(10);
        }
        return response()->json($res);
    }


    public function all(Request $request){
        // return 1;
        $db = config()->get('database.connections.my-account.database');
        $datetime = gmdate("Y-m-d H:i:s",strtotime('+6 Hours'));
        if(config()->get('global.owner_id')){
            if($request->pagination=='none'){
                $syllabus = Syllabus::select('syllabuses.*','u.id','u.name as creator')->with('batch')->join('course_batches','course_batches.id','syllabuses.course_batch_id')
                    ->join($db.'.users as u','u.id','syllabuses.created_by')
                    ->where('syllabuses.parent_id',null)
                    ->where('course_batches.owner_id',config()->get('global.owner_id'))
                    ->addSelect(DB::raw("'$datetime' as current_date_time"))
                    ->addSelect(DB::raw("syllabuses.created_at as created_date_time"))
                    ->orderBy('syllabuses.start_date', 'DESC')->get();
            }else{
                $type = $request->has('type')?$request->type:null;

                $sharable = Sharing::where('user_id',config()->get('global.user_id'))
                            ->where('table_name','course_batches')
                            ->pluck('table_id');

                $syllabus = Syllabus::select('syllabuses.*','u.id as user_id','u.name as creator')
                   ->with('batch')
                   ->join('course_batches','course_batches.id','syllabuses.course_batch_id')
                   ->join('courses','courses.id','course_batches.course_id')
                   ->join($db.'.users as u','u.id','syllabuses.created_by')
                   ->where('syllabuses.parent_id',null)
                   ->where('courses.deleted_at',null)
                   ->where('course_batches.owner_id',config()->get('global.owner_id'))
                   ->when(Config('global.view_all')==false , function ($query, $id) use($sharable) {
                        if($sharable){
                            $query->whereIn('course_batches.id',$sharable)
                            ->orwhere('course_batches.created_by',config()->get('global.user_id'));
                        }else{
                            $query->where('course_batches.created_by',config()->get('global.user_id'));
                        }
                    
                    })
                ->when($type, function ($query, $field) use($type) {
                  if($type=='over'){
                     $date = date("Y-m-d H:i:s");
                    return $query->where(function($q) use($field,$date,$type){
                        $q->where('syllabuses.end_date','<',$date);
                    });
                  }else if($type=='now'){
                      $date = date("Y-m-d H:i:s");
                    return $query->where(function($q) use($field,$date,$type){
                        $q->where('syllabuses.end_date','>',$date);
                    });
                  }
                })
                ->addSelect(DB::raw("'$datetime' as current_date_time"))
                ->addSelect(DB::raw("syllabuses.created_at as created_date_time"))
                ->orderBy('syllabuses.start_date', 'ASC')
                ->groupby('syllabuses.course_batch_id')
                ->paginate(2);
            }
        }else{
            $type = $request->has('type')?$request->type:null;
            if($request->pagination=='none'){
                $year = $request->year;
                $syllabus = Syllabus::select('syllabuses.*','at.start_time as attendance_start_time')
                ->with('batch')
                ->join('course_batches','course_batches.id','syllabuses.course_batch_id')
                ->join('course_enrollments as ce','ce.course_batch_id','course_batches.id')
                ->join('orders as o','o.id','ce.order_id')
                ->leftjoin('attendances as at','at.syllabus_id','syllabuses.id')
                ->when($request->month, function ($query, $field) use($year) {
                    return $query->where(function($q) use($field,$year){
                            $q->whereMonth('syllabuses.start_date', '=', $field)
                                ->whereYear('syllabuses.start_date','=',$year);
                        });
                    })
                ->where('o.user_id',config()->get('global.user_id'))
                ->addSelect(DB::raw("'$datetime' as current_date_time"))
                ->addSelect(DB::raw("syllabuses.created_at as created_date_time"))
                ->where('syllabuses.parent_id',null)
                ->orderBy('syllabuses.start_date', 'ASC')
                ->get(); 
            }else{
                $syllabus = Syllabus::select('syllabuses.*','at.start_time as attendance_start_time')
                ->with('batch')
                ->join('course_batches','course_batches.id','syllabuses.course_batch_id')
                ->join('course_enrollments as ce','ce.course_batch_id','course_batches.id')
                ->join('orders as o','o.id','ce.order_id')
                ->leftjoin('attendances as at','at.syllabus_id','syllabuses.id')
                ->when($type, function ($query, $field) use($type) {
                    if($type=='over'){
                        $date = date("Y-m-d H:i:s");
                        //$date = date("Y-m-d H:i:s",strtotime('+6 Hours'));
                        return $query->where(function($q) use($field,$date,$type){
                            $q->where('syllabuses.end_date','<',$date);
                        });
                    }else if($type=='now'){
                        //$date = date("Y-m-d H:i:s",strtotime('+6 Hours'));
                        $date = date("Y-m-d H:i:s");
                        return $query->where(function($q) use($field,$date,$type){
                            $q->where('syllabuses.end_date','>',$date);
                        });
                    }
                    })
                ->where('o.user_id',config()->get('global.user_id'))
                ->where('syllabuses.parent_id',null)
                ->orderBy('syllabuses.start_date', 'ASC')
                //   ->groupby('syllabuses.start_date', 'ASC')
                ->addSelect(DB::raw("'$datetime' as current_date_time"))
                ->addSelect(DB::raw("syllabuses.created_at as created_date_time"))
                ->groupby('syllabuses.course_batch_id')
                ->paginate(4);  
            }
        }
        
        return response()->json($syllabus);
    }

    public function nextclass($batch_id){

        $syllabus = Syllabus::select('syllabuses.*')
              ->with('batch')
              ->join('course_batches','course_batches.id','syllabuses.course_batch_id')
            //   ->join('course_enrollments as ce','ce.course_batch_id','course_batches.id')
            //   ->join('orders as o','o.id','ce.order_id')
              ->where('syllabuses.course_batch_id',$batch_id)
              ->where('syllabuses.parent_id',null)
              ->where('syllabuses.end_date','>',Carbon::today())
              ->orderBy('syllabuses.start_date','ASC')
              ->first();

        return response()->json($syllabus);
    }
    
    public function child(Request $request){
        
        $check = $request->has('id')?$request->id:null;
        
        if(config()->get('global.owner_id')){
            $syllabus = Syllabus::select('syllabuses.*')
            ->with('batch')
            ->join('course_batches','course_batches.id','syllabuses.course_batch_id')
                ->when($check, function ($query, $field) {
                    return $query->where(function($q) use($field){
                        $q->where('syllabuses.parent_id',Request()->id);
                    });
                })
                ->where('syllabuses.parent_id','!=',null)
                ->where('course_batches.owner_id',Config('global.owner_id'))
            ->orderBy('syllabuses.created_at', 'ASC')
            ->paginate(10); 
        }else{
            $syllabus = Syllabus::select('syllabuses.*')
            ->with('batch')
            ->join('course_batches','course_batches.id','syllabuses.course_batch_id')
            ->join('course_enrollments as ce','ce.course_batch_id','course_batches.id')
            ->join('orders as o','o.id','ce.order_id')
            ->where('o.user_id',config()->get('global.user_id'))
                ->when($check, function ($query, $field) {
                    return $query->where(function($q) use($field){
                        $q->where('syllabuses.parent_id',Request()->id);
                    });
                })
                ->where('syllabuses.parent_id','!=',null)
            ->orderBy('syllabuses.created_at', 'ASC')
            ->paginate(10); 
        }
        
        return response()->json($syllabus);
    }

    public function update(Request $request,$id){

        $data = $request->all();

        if(count($data['delete_array'])>0){
            Syllabus::whereIn('id',$data['delete_array'])->delete();
        }

        $this->insertData($data['syllabus'],null,$id);
        $timestamp = time();
        $date_time = date("Y-m-d H:i:s", $timestamp);
        $syllabus = SyllabusResources::collection(Syllabus::where('parent_id',null)->where('course_batch_id',$id)->where('syllabuses.end_date','>=',$date_time)->get());
        return response()->json($syllabus);
    }

    private function insertData($data,$parent_id,$batch_id){
        foreach ($data as $key => $datum) {
            $syllabus = $datum['id'] ? Syllabus::find($datum['id']) : new Syllabus;
            $syllabus->title               = $datum['title'];
            $syllabus->parent_id           = $parent_id;
            $syllabus->status              = $datum['status'];
            $syllabus->order_number        = $key+1;
            $syllabus->course_batch_id     = $batch_id;
            $syllabus->learning_content_id = $datum['learning_content_id'];
            $syllabus->content_title       = $datum['content_title'];
            $syllabus->live_class_url       = $datum['live_class_url'];
            $syllabus->content_type        = $datum['content_type'];
            $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($datum['start_date']));
            $syllabus->end_date            = date('Y-m-d H:i:s',strtotime($datum['end_date']));
            $syllabus->save();
            if(count($datum['children'])>0){
                $this->insertData($datum['children'],$syllabus->id,$batch_id);
            }
        }
    }

    public function updatemarks(Request $request,$id){

        $student_marks = SyllabusStatus::find($id);
        $student_marks->feedback_arr = $request->feedback_arr;
        $student_marks->mark = $request->mark;
        $student_marks->obtain_marks = $request->obtain_marks;
        $student_marks->update();

        return response()->json(['data' => $student_marks,'message' => 'Marks updated successfully']);

    }

    public function classRecording(Request $request){

        $find = Syllabus::find($request->id);

        $find->recording_url  = $request->recording_url;

        $find->update();

        $req['post']  = 'A new class recording uploaded';
        $req['type']  = 'recording';
        $req['content_url']  = '';
        $req['course_batch_id'] = $find->course_batch_id;
        $req['syllabus_id'] = $find->id;

        if($this->timelineRepository->store($req)){ 
             return response()->json(['message' => 'recording uploaded successfully','data' => $find]);
        };

    }

}

