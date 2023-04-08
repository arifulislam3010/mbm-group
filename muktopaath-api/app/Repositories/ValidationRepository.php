<?php

namespace App\Repositories;

use App\Interfaces\ValidationRepositoryInterface;
use Illuminate\Http\Request;
use Validator;


class ValidationRepository implements ValidationRepositoryInterface 
{
    public function validate($rules){

        $messsages = array(
            
            'required'   =>'e_required',
            'unique'     => 'e_unique',
            'confirmed'  => 'e_password_confirm',
            'email'      => 'e_email',
            'phone'       => 'e_phone',
            'profession_id'  => 'profession_id',
            'password'  => 'password',
            'password_confirmation' => 'password_confirmation'
        );

        if(is_numeric(Request()->get('email'))){
            $messsages['min'] = 'e_min_11';
        }
        else if(Request()->has('phone')){
            $messsages['min'] = 'e_min_11';
        }else{
            $messsages['min'] = 'e_min_8';
        }

        $validator = Validator::make(Request()->all(),$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }else{
            return;
        }
    }

    public function validateCondition($rules, $req){

        $messsages = array(
            
            'required'=>'e_required',
            'min'     => 'e_min_8',
            'unique'     => 'e_unique',
            'confirmed'     => 'e_password_confirm',
            'email'   => 'e_email',
            'profession_id'  => 'profession_id',
            'password'  => 'password',
            'password_confirmation' => 'password_confirmation'
        );

        $validator = Validator::make($req,$rules,$messsages);

       if($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'type'=>1], 400);
        }else{
            return;
        }
    }
}
