<?php

namespace App\Repositories;

use App\Repositories\ValidationRepository;


class Validation extends ValidationRepository 
{
    public function validateRequest($rules){

            return $this->validate($rules);
    }

    public function validateRequestCondition($rules, $req){

        return $this->validateCondition($rules, $req);
}
}