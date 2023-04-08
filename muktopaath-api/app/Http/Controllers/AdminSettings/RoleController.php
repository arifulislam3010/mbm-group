<?php

namespace App\Http\Controllers\AdminSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yaml;

class RoleController extends Controller
{
    public function create(Request $request){

        if($request->has('original')){
            $name = str_replace(" ","-",strtolower($request->original));
            $arr[$request->original]['name'] = $request->role_name;
            $arr[$request->original]['slug'] = $request->original;
            if($request->activeRole=='sys'){
                $ch = str_replace("sys-", "",$request->activeRole);
                $arr[$name]['usage'] = 'System admin';
            }else if($request->activeRole=='teacher'){
                $arr[$name]['usage'] = 'Teachers';
            }
            else if($request->activeRole=='blogger'){
                $arr[$name]['usage'] = 'Bloggers';
            }
            else{
                $arr[$name]['usage'] = 'Partners';
            }
        }else{
            $name = str_replace(" ","-",strtolower($request->role_name));
            if($request->activeRole=='sys'){
                $name = 'sys-'.$name;
                $arr[$name]['usage'] = 'System admin';
            }else if($request->activeRole=='teacher'){
                $name = 'teacher-'.$name;
                $arr[$name]['usage'] = 'Teachers';
            }
            else if($request->activeRole=='blogger'){
                $name = 'blogger-'.$name;
                $arr[$name]['usage'] = 'Bloggers';
            }
            else{
                $arr[$name]['usage'] = 'Partners';
            }

            $arr[$name]['name'] = $request->role_name;
            $arr[$name]['slug'] = $name;
        }  

        foreach ($request['myarray'] as $key => $value) {
            $arr[$name]['service'][$value['service']] = $value;
        }

        $assess = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));

        $assess[$name] = $arr[$name];

        $new_yaml = Yaml::dump($assess,5);

        file_put_contents(resource_path('yaml/role/role.yaml'),$new_yaml);
        
        return response()->json('successfully updated role');
    }

    public function show($role){
        $assess = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));
        return $assess[$role];

    }

    public function schemas(){
        $yaml = Yaml::parse(file_get_contents(resource_path('yaml/role/service_access.yaml')));
            $access = [];

            foreach ($yaml as $key => $value) {
            foreach ($value['access'] as $key1 => $value1) {
                foreach ($value['access'] as $key2 => $value2) {
                    $access[$key2] = $value2;
                }
            }
        }
            return response()->json($access);
    }

    public function rolelist(){

        $assess = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));
        return response()->json($assess);
    }

    public function serviceroles(){

        $access = Yaml::parse(file_get_contents(resource_path('yaml/role/service_access.yaml')));

        return $access;

    }
}
