<?php

namespace App\Http\Controllers\AdminSettings;

use App\Interfaces\AdminSettings\LangRepositoryInterface;
use App\Repositories\Validation;
use App\Http\Controllers\Controller;
use App\Models\AdminSettings\Language;
use App\Models\AdminSettings\LangValue;
use Illuminate\Http\Request;
use Validator;
use Cache;

class LanguageController extends Controller
{
    private  $langRepository;
    private $val;

    public function __construct(LangRepositoryInterface $langRepository,Validation $val) 
    {
        $this->langRepository = $langRepository;
        $this->val = $val;
    }

    public function test_solr(){

        // $ch = curl_init();

        // $body = Request()->all();

        // curl_setopt($ch, CURLOPT_URL,"https://searchapi.muktopaath.gov.bd/solr/demo/dataimport");
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        // // In real life you should use something like:
        // // curl_setopt($ch, CURLOPT_POSTFIELDS, 
        // //          http_build_query(array('postvar1' => 'value1')));

        // // Receive server response ...
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $server_output = curl_exec($ch);

        // curl_close($ch);

        // return $server_output;

        // $ch = curl_init();

        // $headers = array(
        // 'Accept: application/json',
        // 'Content-Type: application/json',
        // 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMjBlOTE3ZDNlZjJiNTQ1NmJiODE4MTM2MzYyNzFmNjkzYWM2NmU3YmY4OTg2ZTgyNjRhNzYzNDg2ZWYyMTdmMGEwNDBkYTRiMDczNWEyNjMiLCJpYXQiOjE2NzAyMTc0MzQuNTIwMDU0LCJuYmYiOjE2NzAyMTc0MzQuNTIwMDYsImV4cCI6MTcwMTc1MzQzNC40ODY1MDcsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.CVr-meMQikQCo0blL7I-gSw2Mvo8B0xUqe66mK0EfEcqhH03e9gBOMslMg1XgwBYCTUnIt7m1D7lTUQ4V5gaiQwpbDW-87azconA8dh-TEh9rGjXTBoMyyfXgHvytVMleefT_m9jQKRb5qCpbNSZcWOXXSwt6dbgQ4TLAOKpKx4UkXurUemJycoGh1a2XpSKRx3LILVb9_AAHVi-mGmdS8z0ZrJYy7d0dIFVdRwC0aIY9aLx7akoNkfsOOn8GckfdKWAbXXIhh8vqGUIq4Gy3y5Mo-ccpE0gHEeHRoZgmCGVwCL6TrH0zckgM-JDYsve1lq0Luf7papswQulLmJn0W2UVM8cuKcIVjvfkS7CKS3RWv9-GyrBpRus6U1dgJPWyFy3u8YxKGlOjlfweAoqhVWb1Y6sN3O8wewtVZ4Zuf3TmXwH-dB9JY5GEoH6lwcMTyE3bIE0QBlazMefnl1vqPLOo4sM0yWecHj_R5ZTiszCtWpVq2hso4a_P8x5_pbcml0sPG7cQRotYzNZ02KUxPAk5BtfPdIcC5iFFzyCYovq4M3opqD6zgBGwHQDtk7ou1G-kJQJd2FQeAi1x1lNUOty0B6o3vLWREzgwHSDjRikPRodmd3t2YO-DcaEuw4MnoZYyD3vDaVFNG0Pb-YKu6FX9UKctmLxMT9TWAnPigo'
        // );
        
        // curl_setopt($ch, CURLOPT_URL, 'https://searchapi.muktopaath.gov.bd/solr/demo/dataimport');
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_HEADER, 0);
        // $body = Request()->all();
        // // $body = [];
        // // $body['command'] = 'full-import';
        // // $body['verbose'] = 'false';
        // // $body['clean'] = 'true';
        // // $body['commit'] = 'true';
        // // $body['core'] = 'demo';
        // // $body['name'] = 'dataimport';


        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
        // curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // // Timeout in seconds
        // curl_setopt($ch, CURLOPT_TIMEOUT, -1);

        // $authToken = curl_exec($ch);

        // return $authToken;
    }

    public function allLanguages(){
        $lang = Language::all();
        return $lang;
    }

    public function langValues(){
        
        $langVal = LangValue::when(Request()->search, function ($query, $field) {
            return $query->where(function($q) use($field){
                $q->where('lang_values.universal','like','%'.$field.'%')
                ->orWhere('lang_values.value','like','%'.$field.'%');
            });
        })
        
        ->when(Request()->lang_id, function ($query, $lang_id) {
            return $query->where(function($q) use($lang_id){
                $q->where('lang_values.language_id',$lang_id);
            });
        })->orderBy('id','DESC')->customPaginate();
        return response()->json($langVal);
    }
    
    public function language(Request $request){
         //return $this->langRepository->language($request->all());


         $res = LangValue::select('universal','value')
        ->where('language_id', $request->lang_id)
        ->get();
        $lan = [];
        foreach ($res as $r) {
            $lan[$r->universal] = $r->value;
        }
        
        $lan['id'] = $request->lang_id;

        return response()->json($lan); 
    
    }
    
    public function addLang(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'prefix'                  => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->langRepository->addLanguage($request->all());
    }

    public function addLangValue(Request $request)
    {
        $rules = array(
            'language_id'            => 'required',
            'universal'              => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->langRepository->addLanguageValue($request->all());

    }

    public function updateLang(Request $request)
    {
        $rules = array(
            'title'                  => 'required',
            'prefix'                    => 'required'
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->langRepository->updateLanguage($request->all());
    }

    public function updateLangValue(Request $request)
    {
        $rules = array(
            'language_id'                  => 'required',
            'universal'                  => 'required',
        );
        
        if($this->val->validateRequest($rules)){
            return $this->val->validateRequest($rules);
        }

        return $this->langRepository->updateLanguageValue($request->all());
    }

    public function deleteLang($id)
    {
        return $this->langRepository->deleteLanguage($id);
    }

    public function deleteLangValue($id)
    {
        return $this->langRepository->deleteLanguageValue($id);
    }

}