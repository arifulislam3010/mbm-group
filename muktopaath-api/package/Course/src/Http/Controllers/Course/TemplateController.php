<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Muktopaath\Course\Models\Course\Template;
use Illuminate\Support\Str;
use Muktopaath\Course\Http\Resources\TemplateResource as TemplateResources;


class TemplateController extends Controller
{
    public function index(Request $request){

        $syllabus = TemplateResources::collection(Template::paginate(10));
        return $syllabus;
    }

    public function showpdf(){
        return 21;

    }

    public function store(Request $request){

        $template = new Template;

        $exploded = explode(',', $request->image);
        $file = base64_decode($exploded[1]);
        
        $safeName = Str::random(10).'.'.'png';
        $success = file_put_contents(public_path().'/storage/uploads/'.$safeName, $file);

        if($success){
            $template->title = $request->title;
            $template->json  = json_encode($request->json);
            $template->image = $safeName;
            $template->type  = $request->type;
        }

        if($template->save()){
            return response()->json($template);
        }
    }

    public function edit($id){

        $template = Template::find($id);

        return response()->json($template);
    }

    public function update(Request $request){

        $template = Template::find($request->id);

        $exploded = explode(',', $request->image);
        $file     = base64_decode($exploded[1]);
        
        $safeName = Str::random(10).'.'.'png';
        $success  = file_put_contents(public_path().'/uploads/'.$safeName, $file);

        if($success){
            $template->title = $request->title;
            $template->json  = json_encode($request->json);
            $template->image = $safeName;
            $template->type  = $request->type;
        }

        if($template->update()){
            return response()->json($template);
        }
    }

    public function destroy($id){
        $template = Template::find($id);

        if($template->delete()){
            return response()->json($template);
        }
    }

}