<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Myaccount\FilemanagerRole;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use DB;


class RedisCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Pre-Middleware Action
        //    return $route =  $request->segments();
       $request_method = Request()->Method();
       
        $full_path = $request->url();
        // var_dump($full_path);
        $without_htcom = parse_url($full_path, PHP_URL_PATH);
        // $find = array("/","-");
        $find = array("/","-",",");
        $replace = array("_","_","_");
        $clean_string = str_replace($find,$replace,$without_htcom);    
        $req_key = $request->all();
        
        if($req_key ==''){
            $request_key = '';
        }else{
            $request_key = implode('_',$req_key);
        }
    
        $redisKey = $clean_string.'_'.$request_method.'_'.$request_key; 
        
        config()->set('global.redis_key', $redisKey);

       
        // if (Cache::store('redis')->has($redisKey)) {
        //     $content = Cache::store('redis')->get($redisKey);
        //     $content = json_decode($content, true);
        //     $data = (object) [];
        //     if(isset($content[$redisKey]))
        //         $data->data = $content[$redisKey];
        //         $data->data = $content;
        //         return response()->json($data);
        // }else{
        //     $response = $next($request);

        //     // Post-Middleware Action

        //     return $response;
        // }
        
    }

    
}
