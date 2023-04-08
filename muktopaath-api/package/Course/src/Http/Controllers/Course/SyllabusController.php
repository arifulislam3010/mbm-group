<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Muktopaath\Course\Models\Course\Syllabus;
use Muktopaath\Course\Models\Course\CourseBatch;
use App\Models\Myaccount\Sharing;
use Muktopaath\Course\Models\Course\SyllabusStatus;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use App\Models\Question\Question;
use Muktopaath\Course\Interfaces\JourneyInterface;
use App\Repositories\Validation;
use App\Models\ContentBank\LearningContent;
use Muktopaath\Course\Http\Resources\SyllabusResourceAdmin;
use Carbon\Carbon;
use DB;
use App\Lib\ContentBank;
class SyllabusController extends Controller
{ 
    private $val;
    use ContentBank; 
    public function __construct(Validation $val, JourneyInterface $journey)
    {
        $this->val = $val;
        $this->journey = $journey;
    }


    public function index(Request $request,$id){

        $date_time = date("Y-m-d H:i:s");
        $type = $request->has('type')?$request->type:null;
        $syllabus = SyllabusResourceAdmin::collection(Syllabus::select('syllabuses.*')->with('lessons')->join('course_batches','course_batches.id','syllabuses.course_batch_id')
        // $syllabus = SyllabusResourceAdmin::collection(Syllabus::join('course_batches','course_batches.id','syllabuses.course_batch_id')
            ->where('parent_id',null)->where('course_batch_id',$id)
            // ->where(function($q) {
            //     $q->where('course_batches.created_by',config()->get('global.user_id'))
            //     ->orWhere('course_batches.owner_id',config()->get('global.owner_id'));
            // })
        ->orderBy('syllabuses.order_number','ASC')->get());
        return response()->json($syllabus);
    }

    public function completed_lesson(Request $request,$id){

        $syllabus = Syllabus::where('parent_id',null)->where('course_batch_id',$id)->get();


        $payments = CourseBatch::select('trainee_transaction_type','trainee_pay_type','trainee_amount','trainer_transaction_type','trainer_pay_type','trainer_amount')
            ->where('id',$id)
            ->first();

        $data['syllabus'] = $syllabus;
        $data['payments']  = $payments;

        return response()->json($data);

    }

    public function createOne(Request $request){

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

    public function classwork_createOne(Request $request){

        $rules = array(
            'course_batch_id'       => 'required',
            'title'                 => 'required',
            'learning_content_id'   => 'required',
            'content_type'          => 'required',
            'content_title'         => 'required'
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $db = config()->get('database.connections.my-account.database');

        $duration = (int)$request['get_hrs'] * 60;
        $duration += (int)$request['get_mins'];

        $syllabus = new Syllabus;
        $syllabus->title               = $request['title'];
        $syllabus->parent_id           = $request['parent_id'];
        $syllabus->status              = 1;
        $syllabus->order_number        = 1;
        $syllabus->course_batch_id     = $request['course_batch_id'];
        $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($request['start_date']));
        $syllabus->end_date = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $duration . ' Minutes'));
        $syllabus->learning_content_id = $request['learning_content_id'];
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
            'get_hrs'                => 'required',
            'get_mins'               => 'required',
        );

        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $db = config()->get('database.connections.my-account.database');

        $syllabus = Syllabus::find($request->id);

        $duration = (int)$request['get_hrs'] * 60;
        $duration += (int)$request['get_mins'];

        $syllabus->title               = $request['title'];
        $syllabus->course_batch_id     = $request['course_batch_id'];
        $syllabus->parent_id           = $request['parent_id'];
        $syllabus->status              = 1;
        $syllabus->order_number        = 1;
        $syllabus->live_class_url       = $request['live_class_url'];
        $syllabus->start_date          = date('Y-m-d H:i:s',strtotime($request['start_date']));
        $syllabus->end_date = date('Y-m-d H:i:s',strtotime($syllabus->start_date . ' +' . $duration . ' Minutes'));
        $syllabus->updated_by = config()->get('global.user_id');
        $syllabus->duration = $duration;

        $syllabus->update();


        $data = Syllabus::select('syllabuses.*','u.name as creator')
        ->join($db.'.users as u','u.id','syllabuses.created_by')
        ->where('syllabuses.id',$syllabus->id)
        ->first();

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

    public function courseworks(Request $request,$batch_id){ 
      

        $db = config()->get('database.connections.my-account.database');

        $db2 = config()->get('database.connections.content-bank.database');
        
    
        $class_id  = ($request->has('class_id') && $request['class_id']!='undefined')?$request['class_id']:null;

        if(config()->get('global.owner_id')){

         $res = Syllabus::select('syllabuses.*','u.name as creator','lc.description as content_description','cb.total_enrollment')
            ->where('course_batch_id',$batch_id)
            ->leftjoin($db.'.users as u','u.id','syllabuses.created_by')
            ->leftjoin($db2.'.learning_contents as lc','lc.id','syllabuses.learning_content_id')
            ->leftjoin('course_batches as cb','syllabuses.course_batch_id','cb.id')
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

        return response()->json($res);
    }

    public function results(Request $request,$id){

        $db = config()->get('database.connections.my-account.database');
        
        $db2 = config()->get('database.connections.content-bank.database');

    
    if(config()->get('global.owner_id')){

        if($request['type']=='OfflineExam'){
            config()->set('global.syllabus_id',$id);
            
            $res = CourseEnrollment::select('course_enrollments.id','course_enrollments.course_batch_id','u.name','u.email','lc.more_data_info')
                    ->join('orders as o','o.id','course_enrollments.order_id')
                    ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                    ->join('syllabuses as s','s.course_batch_id','cb.id')
                    ->join($db.'.users as u','u.id','o.user_id')
                    ->join($db2.'.learning_contents as lc','lc.id','s.learning_content_id')
                    ->with('manual_submissions')
                    ->where('s.id',$id)
                    ->paginate(10);
        }else{
            $res = SyllabusStatus::select('u.name','u.email','syllabus_statuses.created_at as submission_date','lc.more_data_info','u.id as user_id','s.title','syllabus_statuses.extra_attempt','syllabus_statuses.mark as obtain_mark','syllabus_statuses.status','syllabus_statuses.id')
            ->where('s.id',$id)
            ->leftjoin('syllabuses as s','s.id','syllabus_statuses.syllabus_id')
            ->leftjoin('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->leftjoin('orders as o','o.id','ce.order_id')
            ->leftjoin($db.'.users as u','u.id','o.user_id')
            ->leftjoin($db2.'.learning_contents as lc','lc.id','s.learning_content_id')
            ->orderby('syllabus_statuses.mark','DESC')
            ->paginate(10);
        }

        
    }else{
        $res = SyllabusStatus::select('u.name','u.email','syllabus_statuses.created_at as submission_date','lc.more_data_info','u.id as user_id','s.title','syllabus_statuses.mark as obtain_mark','syllabus_statuses.status','syllabus_statuses.id')
        ->where('s.id',$id)
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

    public function manual_mark(Request $request){

        $data = SyllabusStatus::where('syllabus_id',$request['syllabus_id'])
            ->where('course_enrollment_id',$request['course_enrollment_id'])->first();

        if(!$data){
            $data = new SyllabusStatus;
        }
        
        $data->course_enrollment_id = $request['course_enrollment_id'];
        $data->course_batch_id = $request['course_batch_id'];
        $data->syllabus_id = $request['syllabus_id'];
        $data->pass_mark = $request['pass_mark'];
        $data->total_marks = $request['total_mark'];
        $data->mark = $request['mark'];
        $data->save();
    }


    public function all(Request $request){

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
                   ->when(!Config('global.view_all') , function ($query, $id) use($sharable) {
                        if($sharable){
                            $query->whereIn('course_batches.id',$sharable)
                            ->orwhere('course_batches.created_by',config()->get('global.user_id'));
                        }else{
                            $query->where('course_batches.created_by',config()->get('global.user_id'));
                        }
                    
                    })
                ->when($type, function ($query, $field) use($type) {
                  if($type=='over'){
                      $date = Carbon::now();
                    return $query->where(function($q) use($field,$date,$type){
                        $q->where('syllabuses.end_date','<',$date);
                    });
                  }else if($type=='now'){
                      $date = Carbon::now();
                    return $query->where(function($q) use($field,$date,$type){
                        $q->where('syllabuses.end_date','>',$date);
                    });
                  }
                })
                ->addSelect(DB::raw("'$datetime' as current_date_time"))
                ->addSelect(DB::raw("syllabuses.created_at as created_date_time"))
                ->orderBy('syllabuses.start_date', 'ASC')
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
                      $date = Carbon::now();
                    return $query->where(function($q) use($field,$date,$type){
                        $q->where('syllabuses.end_date','<',$date);
                    });
                  }else if($type=='now'){
                      $date = Carbon::now();
                    return $query->where(function($q) use($field,$date,$type){
                        $q->where('syllabuses.end_date','>',$date);
                    });
                  }
                })
              ->where('o.user_id',config()->get('global.user_id'))
              ->where('syllabuses.parent_id',null)
              ->orderBy('syllabuses.start_date', 'ASC')
              ->addSelect(DB::raw("'$datetime' as current_date_time"))
              ->addSelect(DB::raw("syllabuses.created_at as created_date_time"))
              ->groupby('syllabuses.id')
              ->paginate(10);  
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
      
    // DB::beginTransaction();
    // try{
        if(count($data['delete_array'])>0){
            Syllabus::whereIn('id',$data['delete_array'])->delete();
        }

        $this->insertData($data['syllabus'],null,$id);
        $timestamp = time();
        $date_time = date("Y-m-d H:i:s", $timestamp);
        $syllabus = SyllabusResourceAdmin::collection(Syllabus::where('parent_id',null)->orderBy('order_number','ASC')->where('course_batch_id',$id)->get());
        // DB::commit();
        return response()->json($syllabus);
        // }catch (\Exception $e) {
        //     DB::rollback();
        // }
    }

    public function increase_attempt(Request $request){

        $rules = array(
            'id'        => 'required'
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $res = SyllabusStatus::find($request['id']);

        $res->extra_attempt+=1;
        $res->update();

        return response()->json(['message' => 'mark updated successfully',
                                    'data' => $res]);
    }

    private function insertData($data,$parent_id,$batch_id,$lesson=0){
        
        foreach ($data as $key => $datum) {
            
            $learning_content_id = null;
            if($lesson==1){
                if($datum['content_type']=='discussion' && $datum['content_type']=='presentation' && $datum['content_type']=='interview'){

                }else{
                    $contentdata = $this->insertContent($datum);
                    if($contentdata!=''){
                        $learning_content_id = $contentdata->id;
                    }  
                }
            }
            

            $syllabus = $datum['id'] ? Syllabus::find($datum['id']) : new Syllabus;
            $syllabus->title               = $datum['title'];
            $syllabus->parent_id           = $parent_id;
            $syllabus->status              = $datum['status'];
            $syllabus->preview             = $datum['preview'];
            $syllabus->type                = $datum['type'];
            $syllabus->suggested_lesson    = $datum['suggested_lesson'];
            $syllabus->order_number        = $key+1;
            $syllabus->course_batch_id     = $batch_id;
            if($lesson==1){$syllabus->learning_content_id = $learning_content_id;}
            $syllabus->content_title       = $datum['content_title'];
            $syllabus->live_class_url      = $datum['live_class_url'];
            $syllabus->live_class_url_type = $datum['live_class_url_type'];
            if(isset($datum['content']) && isset($datum['content']['description'])){
                $syllabus->description         = $datum['content']['description'];
            }
            if(isset($datum['content']) && isset($datum['content']['instruction'])){
                $syllabus->instruction         = $datum['content']['instruction'];
            }
            
            $syllabus->content_type        = $datum['content_type'];
            $syllabus->duration            = $datum['duration'];
            $syllabus->start_date          = $datum['start_date']?date('Y-m-d H:i:s',strtotime($datum['start_date'])):null;
            // $syllabus->duration            = $duration;
            $syllabus->end_date = $datum['end_date']?date('Y-m-d H:i:s',strtotime($datum['end_date'])):null;
            $syllabus->save();
            if(isset($datum['children']) && count($datum['children'])>0){
               $this->insertData($datum['children'],$syllabus->id,$batch_id,1);
            }
        }
    }
    
    
    public function updatemarks(Request $request,$id){

        $student_marks = SyllabusStatus::find($id);
        $student_marks->feedback_arr = $request->feedback_arr;
        $student_marks->mark = $request->mark;
        $student_marks->obtain_marks = $request->obtain_marks;
        $student_marks->update();



        $data= [];
        $data['user_id'] = $this->finduser($id);
        $data['syllabus_id']  = $student_marks->syllabus_id;
        $data['completeness']  = 100;

        return $this->journey->update($data);

        if($this->journey->update($data)){
             return response()->json(['data' => $student_marks,'message' => 'Marks updated successfully']);
        }


    }

    public function finduser($id){
        $user = SyllabusStatus::join('course_enrollments as ce','ce.id','syllabus_statuses.course_enrollment_id')
            ->where('syllabus_statuses.id',$id)
            ->join('orders as o','o.id','ce.order_id')
            ->value('o.user_id');

        return $user;
    }

    public function classRecording(Request $request){

        $find = Syllabus::find($request->id);

        $find->recording_url  = $request->recording_url;

        $find->update();

        return response()->json(['message' => 'recording uploaded successfully','data' => $find]);

    }

}

