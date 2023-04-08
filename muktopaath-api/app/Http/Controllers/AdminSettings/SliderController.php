<?php

namespace App\Http\Controllers\AdminSettings;

use App\Models\AdminSettings\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Google\Service\Docs\Response;

class SliderController extends Controller
{
    
    public function index()
    {
        $slider = Slider::customPaginate();
        return response()->json($slider);
    }

    public function allSlider()
    {
        $slider = Slider::customPaginate();
        return response()->json($slider);
    }

    public function front(){
        $data = slider::with('photo')->get()->take(3);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $slider = new Slider;
        
        $slider->title = $request['title'];
        $slider->bn_title = $request['bn_title'];
        $slider->url = $request['url'];
        $slider->description = $request['description'];
        $slider->button_text = $request['button_text'];
        $slider->button_url = $request['button_url'];
        $slider->content_id = $request['content_id'];
        $slider->created_by = config()->get('global.user_id');
        if($slider->save()){
           return response()->json([
             'message' => 'New slider added successfully',
             'data'  => $slider
           ],201);
        }
    }

    public function update(Request $request, Slider $slider)
    {
        $slider = Slider::find($request['id']);
        
        $slider->title = $request['title'];
        $slider->bn_title = $request['bn_title'];
        $slider->url = $request['url'];
        $slider->description = $request['description'];
        $slider->button_text = $request['button_text'];
        $slider->button_url = $request['button_url'];
        $slider->content_id = $request['content_id'];
        $slider->updated_by  =  config()->get('global.user_id');
        if($slider->save()){
           return response()->json([
             'message' => 'Slider updated successfully',
             'data'  => $slider
           ]);
        }
    }

    public function destroy(int $id, Slider $slider)
    {
        $del = Slider::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted slider'],200);
        }else{
            return response()->json(['message' => 'Slider to be deleted not found'],404);
        }
    }
}