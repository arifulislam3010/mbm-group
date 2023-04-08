<?php

namespace Muktopaath\Course\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Myaccount\User;
use App\Models\Myaccount\InstitutionInfo;
use Muktopaath\Course\Models\Course\CourseEnrollment;
use Auth;
use Muktopaath\Course\Http\Resources\Institution as Institutions;
class UserController extends Controller
{
    public function info($username){

        $db = config()->get('database.connections.admin-settings.database');

        $user = User::select('users.name','users.gamification_point','p.title as profession_en','p.bn_title as profession_bn','users.photo_id')
                    ->where('users.username',$username)
                    ->join('user_infos as ui','ui.id','ui.user_id')
                    ->leftJoin($db.'.professions as p','p.id','ui.profession_id')
                    ->with('photo')
                    ->first();

        return response()->json($user);
    }

    public function user_courses($username){

        $db = config()->get('database.connections.my-account.database');

        $res = CourseEnrollment::select('cb.*')
                ->join('course_batches as cb','cb.id','course_enrollments.course_batch_id')
                ->join('orders as o','o.id','course_enrollments.order_id')
                ->join($db.'.users as u','u.id','o.user_id')
                ->where('u.Username',$username)
                ->paginate(10);

        return response()->json($res);

    }

    public function institutionsInfos($username){

        $InstitutionInfo = InstitutionInfo::where('username',$username)->first();
        if($InstitutionInfo){
            return new Institutions($InstitutionInfo);
        }else{
            return 1;
        }

    }

}
