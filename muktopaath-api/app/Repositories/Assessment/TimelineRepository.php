<?php

namespace App\Repositories\Assessment;
use App\Interfaces\Assessment\TimelineRepositoryInterface;
use App\Models\Assessment\Timeline;
use App\Models\Assessment\TimelineComment;
use DB;

class TimelineRepository implements TimelineRepositoryInterface
{

    public function viewall(){
        $take = Request()->take;
        $skip = Request()->skip;

        $db = config()->get('database.connections.my-account.database');

        if(config()->get('global.owner_id')){
            $res = Timeline::select('timelines.*','u.name as creator','u.photo_id','ui.photo_name','ui.photo_version')
                   ->join($db.'.users as u','u.id','timelines.created_by')
                   ->join($db.'.user_infos as ui','ui.user_id','u.id') 
                   ->where('timelines.course_batch_id',Request()->course_batch_id)
                   ->with('comments','photo')
                   ->with('files')
                   ->orderBy('timelines.created_at','DESC')
                   ->skip($skip)
                   ->take($take)
                   ->get();

            $data['data'] = $res;
        }else{
            $res = Timeline::select('timelines.*','u.name as creator','ui.photo_name','ui.photo_version')
                   ->join($db.'.users as u','u.id','timelines.created_by')
                   ->join($db.'.user_infos as ui','ui.user_id','u.id') 
                   ->where('timelines.course_batch_id',Request()->course_batch_id)
                   ->with('comments','photo')
                   ->with('files')
                   ->orderBy('timelines.created_at','DESC')
                   ->skip($skip)
                   ->take($take)
                   ->get();
                   
            $data['data'] = $res;
        }

        return response()->json($data);
    }

    public function view($id){

        $db = config()->get('database.connections.my-account.database');

        $res = Timeline::select('timelines.*','u.name as creator')
                ->join($db.'.users as u','u.id','timelines.created_by')
                ->where('timelines.id',$id)
                ->first();

        return response()->json($res);
    }

    public function store($request){

        $db = config()->get('database.connections.my-account.database');

        $data = Request();
        $timeline = new Timeline;
        $timeline->post = $request['post'];
        $timeline->type = $request['type'];
        $timeline->file_id = isset($request['file_id'])?$request['file_id']:0;
        $timeline->course_batch_id = $request['course_batch_id'];
        $timeline->syllabus_id = isset($request['syllabus_id'])?$request['syllabus_id']:null;
        $timeline->parent_id = isset($request['parent_id'])?$request['syllabus_id']:null;
        $timeline->created_by = config()->get('global.user_id');
        $timeline->save();

        $data = Timeline::select('timelines.*','u.name as creator','u.photo_id')
            ->join($db.'.users as u','u.id','timelines.created_by')
            ->where('timelines.id',$timeline->id)
            ->with('files','photo')
            ->first();

        return response()->json(['data' => $data, 'message' => 'Posted in timeline successfully'],201);
    }

    public function comment(){

        $db = config()->get('database.connections.my-account.database');

        $data = Request();
        $comment = new TimelineComment;
        $comment->comment = $data['comment'];
        $comment->created_by = config()->get('global.user_id');
        $comment->timeline_id = $data['timeline_id'];
        $comment->save();

        $data = TimelineComment::select('timeline_comments.*','u.photo_id','u.id as user_id','u.name as creator')
        ->join($db.'.users as u','u.id','timeline_comments.created_by')
        ->where('timeline_comments.id',$comment->id)
        ->first();

        $comment['user']['id']  =   $data->user_id;
        $comment['user']['name']  = $data->creator;
        $comment['user']['photo_id']  =   $data->user_id;


        return response()->json(['data' => $comment, 'message' => 'Commented successfully'],201);
    }

    public function updateComment($id){

        $db = config()->get('database.connections.my-account.database');

        $data = Request();

        $comment = TimelineComment::find($id);
        $comment->comment = $data['comment'];
        $comment->created_by = config()->get('global.user_id');
        $comment->timeline_id = $data['timeline_id'];
        $comment->update();

        $data = TimelineComment::select('timeline_comments.*','u.photo_id','u.id as user_id','u.name as creator')
        ->join($db.'.users as u','u.id','timeline_comments.created_by')
        ->where('timeline_comments.id',$comment->id)
        ->first();

        $comment['user']['id']  =   $data->user_id;
        $comment['user']['name']  = $data->creator;
        $comment['user']['photo']  =   $data->user_id;

        $comment['user']['id']  =   $data->user_id;
        $comment['user']['name']  = $data->creator;
        $comment['user']['photo']  =   $data->user_id;


        return response()->json(['data' => $comment, 'message' => 'Comment updated successfully'],200);

    }

    public function delete($id){

        $res = Timeline::find($id);
        if($res){
            $res->delete();
        }

        return response()->json(['data' => $res,'message' => 'Post deleted successfully']);

    }

    public function deleteComment($id){

        $res = TimelineComment::find($id);
        if($res){
            $res->delete();
        }

        return response()->json(['data' => $res,'message' => 'Comment deleted successfully']);

    }

    public function update($id){

        $db = config()->get('database.connections.my-account.database');

        $data = Request();
        $timeline = Timeline::find($id);
        $timeline->post = $data['post'];
        $timeline->type = $data['type'];
        //$timeline->content_url = $data['content_url'];
        $timeline->update();

        $data = Timeline::select('timelines.*','u.name as creator')
        ->join($db.'.users as u','u.id','timelines.created_by')
        ->where('timelines.id',$timeline->id)
        ->with('comments')
        ->first();

        return response()->json(['data' => $data, 'message' => 'Post updated in timeline'],201);

    }

    
}