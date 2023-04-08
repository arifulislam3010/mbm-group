<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Interfaces\SAttendanceRepositoryInterface;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Muktopaath\Course\Models\Course\CertificateTemplate;
use App\Repositories\Validation;
use Auth;
 
class CertificateController extends Controller
{
    private $val;
    
    public function __construct(Validation $val)
    {
        $this->val = $val;
    }

    //return certificate template list
    public function index()
    {
        $res = CertificateTemplate::where('owner_id',config()->get('global.owner_id'))
            ->with('background')
            ->get();
        return response()->json($res);
    }

    //return certificate template by id
    public function show($id)
    {
        $res = CertificateTemplate::find($id);
        return response()->json($res);
    }


    public function store(Request $request){

        $rules = array(
            // 'certificate_intro'                     => 'required',
            // 'text_before_student_name'              => 'required'
        );

        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        $data = new CertificateTemplate;
        $data->certificate_intro        = $request->certificate_intro;
        $data->text_before_student_name = $request->text_before_student_name;
        $data->text_before_course_name  = $request->text_before_course_name;
        $data->text_for_held_on         = $request->text_for_held_on;
        $data->background_id            = $request->file_id;
        $data->text_for_obtain_grade    = $request->text_for_obtain_grade;
        $data->start_date               = $request->start_date;
        $data->end_date                 = $request->end_date;
        $data->owner_id                 = config()->get('global.owner_id');
        $data->created_by               = config()->get('global.user_id');
        $data->save();

        return response()->json([
            'data'      => $data,
            'message'   => 'Successfully created template'
        ]);



    }
}