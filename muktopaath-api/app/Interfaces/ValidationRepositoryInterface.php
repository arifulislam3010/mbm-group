<?php

namespace App\Interfaces;

interface ValidationRepositoryInterface 
{
    // public function getAllOrders();
    public function validate(array $rules);
    public function validateCondition(array $rules, array $req);
}