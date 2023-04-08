<?php

namespace App\Http\Controllers\MyAccount;

use App\Http\Controllers\Controller;
use App\Models\Myaccount\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function show($id){
        $logs = ActivityLog::join('users','users.id','activity_logs.user_id')
            ->where('activity_logs.user_id',$id)
            ->orderby('activity_logs.id','DESC')
            ->paginate(10);
            
        return response()->json($logs);
    }

    public function index(){

      if(config()->get('global.type')==1){
        $logs = ActivityLog::select('activity_logs.*','users.name')
        ->join('users','users.id','activity_logs.user_id')
        ->orderby('id','DESC')->paginate(10);
      }else{
        $logs = ActivityLog::select('activity_logs.*','users.name')
        ->join('users','users.id','activity_logs.user_id')
        ->where('owner_id',config()->get('global.owner_id'))
                ->orderby('id','DESC')
                ->paginate(10);
      }

      return response()->json($logs);
    }
}
