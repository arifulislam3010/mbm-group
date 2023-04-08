<?php

namespace App\Repositories\AdminSettings\Validation;

use App\Repositories\ValidationRepository;


class Lang extends ValidationRepository 
{
    public function langvalidate(){

        $rules = array(
            'title'                  => 'required',
            'prefix'                  => 'required',
        );

            return $this->validate($rules);
    }
}
