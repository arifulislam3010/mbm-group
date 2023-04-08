<?php

namespace App\Interfaces\AdminSettings;

interface WorkingFieldRepositoryInterface 
{
    public function addWf(array $request);
    public function updateWf(array $request);
    public function deleteWf(int $id);
}