<?php

namespace App\Repositories\AdminSettings;
use App\Models\AdminSettings\Language;
use App\Models\AdminSettings\LangValue;

use App\Interfaces\AdminSettings\LangRepositoryInterface;

class LangRepository implements LangRepositoryInterface 
{
    public function language(array $request)
    {
        $res = LangValue::select('universal','value')
        ->where('language_id', $request['lang_id'])
        ->get();
        $lan = [];
        foreach ($res as $r) {
            $lan[$r->universal] = $r->value;
        }
        
        $lan['id'] = $request['lang_id'];

        return response()->json($lan);    
    }
    
    public function updateLanguage(array $request)
    {
        $lang = Language::find($request['id']);

       
        $lang->title = $request['title'];
        $lang->prefix = $request['prefix'];


        if($lang->update()){
            return response()->json([
                'message' => 'Successfully updated',
                'data'   => $lang
            ],201);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

    public function addLanguage(array $request)
    {
        $lang = new Language;
        
        $lang->title = $request['title'];
        $lang->prefix = $request['prefix'];
        
        if($lang->save()){
            return response()->json([
                'message' => 'Language added successfully',
                'data'  => $lang
            ]);

        }
    }

    public function addLanguageValue(array $request)
    {

        $lang = new LangValue;
        
        $lang->language_id = $request['language_id'];
        $lang->universal = $request['universal'];
        $lang->value = $request['value'];
        $lang->message = $request['message'];
        

        $langVal = LangValue::where('language_id','=', $lang->language_id)
        ->where('universal', $lang->universal)
        ->first();

        if(empty($langVal)){
           
            if($lang->save()){
            return response()->json([
                'message' => 'Language values created successfully',
                'data'  => $lang
            ]);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
        
        }else{
            return response()->json([
                'message' => 'Already exists!'
            ], 403);
        }
        
        
    }

    public function updateLanguageValue(array $request)
    {
        $lang = LangValue::find($request['id']);

       
        $lang->language_id = $request['language_id'];
        $lang->universal = $request['universal'];
        $lang->value = $request['value'];
        $lang->message = $request['message'];


        if($lang->update()){
            return response()->json([
                'message' => 'Successfully updated',
                'data'   => $lang
            ],201);
        }else{
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

    public function deleteLanguage(int $id)
    {
        $del = Language::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted language'],200);
        }else{
            return response()->json(['message' => 'Language to be deleted not found'],404);
        }
    }

    public function deleteLanguageValue(int $id)
    {
        $del = LangValue::find($id);
        if($del){
            $del->delete();
            return response()->json(['message' =>'successfully deleted language value'],200);
        }else{
            return response()->json(['message' => 'Language value to be deleted not found'],404);
        }
    }
}