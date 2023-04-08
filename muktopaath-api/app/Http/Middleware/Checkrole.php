<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Myaccount\AssessmentsRole;
use App\Models\Myaccount\ContentbankRole;
use App\Models\Myaccount\SystemRole;
use App\Models\Myaccount\FilemanagerRole;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use Yaml;

class Checkrole
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
        if ($request->hasHeader('roleid')) {
            $role_id = $request->header('roleid');


            $yaml = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));
          
          
            $route =  $request->segments();

            $exceptions = [
                'switch-role',
                'switch-account',
                'view-info'
            ];

            $extend = [
                'view',
                'update',
                'delete'
            ];
            
            $size =  count($route);
            
            if (!in_array($route[$size-1], $exceptions)){

                $roles = SystemRole::select('role','access')->where('id',$role_id)->where('user_id',config()->get('global.user_id'))->first();

                if($roles){

                    $role = $roles->role;
                    config()->set('global.currentrole', $role);
                    if(str_starts_with($role,'sys-')){
                        config()->set('global.sysrole', true);
                    }else{
                        config()->set('global.role', true);
                    }

                    $access = (array)json_decode($roles->access);

           


                //see if user has view all/update all etc. access in a route.
                if(array_key_exists($route[1], $access)){

                    //return response()->json($access[$route[1]]);
                    if(array_key_exists($route[2], $access[$route[1]])){


                   // if(in_array($route[$size-1],$extend)){
                    $hit =  $route[$size-1]."_all";

                    if(array_key_exists($hit, $access[$route[1]])){


                        //view_all is initially false. when it comes to checking, it checks the specific route if its true in database dn config to true

                        if($access[$route[1]]->{$hit}==true){

                            config()->set('global.view_all', true);
                        }else{
                         if($request->isMethod('PUT')){
                                $yml =  Yaml::parse(file_get_contents(resource_path('yaml/models/schemas.yaml')));
                                $model = $yml[$route[1]];
                                $check = $model::where('id',$request['id'])
                                ->where('created_by',config()->get('global.user_id'))->first();
                                if(!$check){
                                    return response()->json(['messege' => 'Your can not update this particular module']);

                                }
                            }
                        }
                    }
                // }

                if($access[$route[1]]->{$route[2]}!==true){
                    return response()->json(['messege' => 'You have no access in this particular module']);
                }  
            }


                }


          
            }
        }

            

        }


            config()->set('global.role_id', $request->header('role_id'));
        

        $response = $next($request);

        // Post-Middleware Action

        return $response;
    }
}
